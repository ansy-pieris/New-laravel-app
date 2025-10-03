<?php

namespace App\Livewire\Shop;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutPage extends Component
{
    public $items = [];
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $itemCount = 0;

    // Shipping form properties
    public $recipient_name = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $postal_code = '';

    // Payment properties
    public $payment_method = 'cod';
    public $card_type = '';
    public $card_number = '';
    public $card_name = '';
    public $card_cvv = '';

    protected $rules = [
        'recipient_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'postal_code' => 'required|string|max:10',
        'payment_method' => 'required|in:cod,card',
        'card_type' => 'required_if:payment_method,card|in:visa,mastercard',
        'card_number' => 'required_if:payment_method,card|string|max:19',
        'card_name' => 'required_if:payment_method,card|string|max:255',
        'card_cvv' => 'required_if:payment_method,card|string|max:4',
    ];

    protected $messages = [
        'recipient_name.required' => 'Recipient name is required.',
        'phone.required' => 'Phone number is required.',
        'address.required' => 'Street address is required.',
        'city.required' => 'City is required.',
        'postal_code.required' => 'Postal code is required.',
        'card_type.required_if' => 'Card type is required for card payment.',
        'card_number.required_if' => 'Card number is required for card payment.',
        'card_name.required_if' => 'Name on card is required for card payment.',
        'card_cvv.required_if' => 'CVV is required for card payment.',
    ];

    public function mount()
    {
        $this->loadCartData();
        
        // Redirect to home if cart is empty
        if ($this->itemCount == 0) {
            session()->flash('error', 'Your cart is empty. Please add items before checkout.');
            return redirect()->route('home');
        }

        // Pre-fill shipping details if user has them
        $user = Auth::user();
        if ($user && $user->addresses()->exists()) {
            $address = $user->addresses()->latest()->first();
            $this->recipient_name = $address->recipient_name ?? $user->name ?? '';
            $this->phone = $address->phone ?? '';
            $this->address = $address->address ?? '';
            $this->city = $address->city ?? '';
            $this->postal_code = $address->postal_code ?? '';
        } else {
            $this->recipient_name = $user->name ?? '';
        }
    }

    public function loadCartData()
    {
        if (!Auth::check()) {
            $this->items = collect();
            $this->itemCount = 0;
            $this->subtotal = 0;
            $this->tax = 0;
            $this->total = 0;
            return;
        }

        $this->items = CartItem::with('product')
            ->where('user_id', Auth::id())
            ->get();

        $this->itemCount = $this->items->sum('quantity');
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        // Calculate tax (you can adjust this rate as needed)
        $this->tax = $this->subtotal * 0.0; // 0% tax for now, change as needed
        $this->total = $this->subtotal + $this->tax;
    }

    public function placeOrder()
    {
        $this->validate();

        if ($this->itemCount == 0) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        try {
            DB::beginTransaction();

            // Check stock availability first
            foreach ($this->items as $cartItem) {
                if ($cartItem->product->stock < $cartItem->quantity) {
                    throw new \Exception("Insufficient stock for {$cartItem->product->name}. Only {$cartItem->product->stock} available.");
                }
            }

            // Create the order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $this->total, // Changed from total_amount to total_price
                'status' => 'pending',
            ]);

            // Create order items and reduce stock
            foreach ($this->items as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->order_id, // Use order_id instead of id
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Reduce product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
                \Log::info('Stock reduced for product via Livewire checkout', [
                    'product_id' => $cartItem->product_id,
                    'quantity_reduced' => $cartItem->quantity,
                    'remaining_stock' => $cartItem->product->fresh()->stock
                ]);
            }

            // Create shipping address
            ShippingAddress::create([
                'user_id' => Auth::id(),
                'order_id' => $order->order_id, // Use order_id instead of id
                'recipient_name' => $this->recipient_name,
                'phone' => $this->phone,
                'address' => $this->address,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
            ]);

            // Clear the cart
            CartItem::where('user_id', Auth::id())->delete();

            DB::commit();

            // Send order confirmation email via Google Gmail API
            try {
                $googleMailService = new \App\Services\GoogleMailService();
                $user = Auth::user();
                $orderWithItems = $order->load(['items.product']);
                $googleMailService->sendOrderConfirmation($orderWithItems, $user->email);
                \Log::info('Order confirmation email sent successfully', ['order_id' => $order->order_id, 'email' => $user->email]);
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                \Log::error('Order confirmation email failed: ' . $e->getMessage(), ['order_id' => $order->order_id]);
            }

            // Flash success message with order details
            $deliveryDate = now()->addDays(4)->format('M j, Y');
            session()->flash('order_success', "Thank you for your purchase! Order #{$order->order_id} will be delivered by {$deliveryDate}. A confirmation email has been sent to {$user->email}.");
            
            // Dispatch event to update cart counter
            $this->dispatch('cartUpdated');

            // Redirect to home or order confirmation page
            return redirect()->route('home');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Provide more specific error messages
            $errorMessage = 'Something went wrong. Please try again.';
            
            if (str_contains($e->getMessage(), 'Insufficient stock')) {
                $errorMessage = $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'Integrity constraint violation')) {
                $errorMessage = 'Order processing error. Please try again.';
            } elseif (str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'Connection timed out')) {
                $errorMessage = 'Database connection error. Please try again.';
            }
            
            session()->flash('error', $errorMessage);
            \Log::error('Order placement failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'cart_items_count' => count($this->items),
                'total' => $this->total
            ]);
            
            // Don't redirect on error, stay on checkout page
            return;
        }
    }

    public function updatedPaymentMethod()
    {
        // Clear card details when switching to COD
        if ($this->payment_method === 'cod') {
            $this->card_type = '';
            $this->card_number = '';
            $this->card_name = '';
            $this->card_cvv = '';
        }
    }

    public function render()
    {
        return view('livewire.shop.checkout-page');
    }
}