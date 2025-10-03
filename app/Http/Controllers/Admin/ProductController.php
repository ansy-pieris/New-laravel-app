<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->orderBy('created_at', 'desc')->get();
        $categories = Category::all();

        // Detect if this is being accessed by staff or admin
        $userRole = Auth::user()->role;
        $routePrefix = $userRole === 'staff' ? 'staff' : 'admin';

        return view('admin.products', compact('products', 'categories', 'routePrefix'));
    }

    public function store(Request $request)
    {
        // Debug logging
        \Log::info('Product store request received', [
            'data' => $request->all(),
            'files' => $request->hasFile('image'),
            'user' => Auth::id(),
            'wants_json' => $request->wantsJson(),
            'ajax' => $request->ajax(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'name' => 'required|string|min:2|max:100',
                'description' => 'required|string|min:10|max:1000',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,category_id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);
            
            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'Validation failed: ' . implode(', ', array_map(function($errors) { return implode(', ', $errors); }, $e->errors()))], 422);
            }
            throw $e;
        }

        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->category_id = $request->category_id;
            $product->added_by = Auth::id();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                \Log::info('Image upload details', [
                    'has_file' => $request->hasFile('image'),
                    'file_valid' => $image->isValid(),
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime_type' => $image->getMimeType(),
                    'tmp_name' => $image->getPathname()
                ]);

                $imageName = time() . '_' . $image->getClientOriginalName();
                $destinationPath = storage_path('app/public/products');
                $fullFilePath = $destinationPath . '/' . $imageName;
                
                // Ensure the directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                    \Log::info('Created directory: ' . $destinationPath);
                }
                
                try {
                    // Try Laravel's storage method first
                    $storagePath = $image->storeAs('public/products', $imageName);
                    
                    // Verify file was actually saved
                    if ($storagePath && file_exists(storage_path('app/' . $storagePath))) {
                        \Log::info('Image storage SUCCESS (Laravel method)', [
                            'image_name' => $imageName,
                            'storage_path' => $storagePath,
                            'full_path' => storage_path('app/' . $storagePath),
                            'file_size' => filesize(storage_path('app/' . $storagePath))
                        ]);
                        $product->image = $imageName;
                    } else {
                        // Fallback: Manual file move
                        \Log::warning('Laravel storage failed, trying manual move');
                        
                        if (move_uploaded_file($image->getPathname(), $fullFilePath)) {
                            \Log::info('Image storage SUCCESS (manual move)', [
                                'image_name' => $imageName,
                                'full_path' => $fullFilePath,
                                'file_size' => filesize($fullFilePath)
                            ]);
                            $product->image = $imageName;
                        } else {
                            \Log::error('Manual file move FAILED', [
                                'source' => $image->getPathname(),
                                'destination' => $fullFilePath,
                                'source_exists' => file_exists($image->getPathname()),
                                'dest_dir_writable' => is_writable($destinationPath)
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Image upload EXCEPTION: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::info('No image file in request');
            }

            $product->save();

            \Log::info('Product saved successfully', [
                'product_id' => $product->product_id,
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['success' => 'Product added successfully!']);
            }

            return redirect()->route('admin.products')->with('success', 'Product added successfully!');

        } catch (\Exception $e) {
            \Log::error('Product store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => Auth::id(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'Failed to add product: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to add product. Please try again.');
        }
    }

    public function update(Request $request)
    {
        // Debug logging
        \Log::info('Product update request received', [
            'data' => $request->all(),
            'files' => $request->hasFile('image'),
            'user' => Auth::id(),
            'wants_json' => $request->wantsJson(),
            'ajax' => $request->ajax(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'product_id' => 'required|exists:products,product_id',
                'name' => 'required|string|min:2|max:100',
                'description' => 'required|string|min:10|max:1000',
                'price' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'category_id' => 'required|exists:categories,category_id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Update validation failed', [
                'errors' => $e->errors(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);
            
            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'Validation failed: ' . implode(', ', array_map(function($errors) { return implode(', ', $errors); }, $e->errors()))], 422);
            }
            throw $e;
        }

        try {
            $product = Product::where('product_id', $request->product_id)->firstOrFail();
            
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->category_id = $request->category_id;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image && Storage::exists('public/products/' . $product->image)) {
                    Storage::delete('public/products/' . $product->image);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $destinationPath = storage_path('app/public/products');
                $fullFilePath = $destinationPath . '/' . $imageName;
                
                // Ensure the directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                
                try {
                    // Try Laravel's storage method first
                    $storagePath = $image->storeAs('public/products', $imageName);
                    
                    // Verify file was actually saved
                    if ($storagePath && file_exists(storage_path('app/' . $storagePath))) {
                        $product->image = $imageName;
                    } else {
                        // Fallback: Manual file move
                        if (move_uploaded_file($image->getPathname(), $fullFilePath)) {
                            $product->image = $imageName;
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Update image upload EXCEPTION: ' . $e->getMessage());
                }
            }

            $product->save();

            \Log::info('Product updated successfully', [
                'product_id' => $product->product_id,
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['success' => 'Product updated successfully!']);
            }

            return redirect()->route('admin.products')->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Product update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => Auth::id(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'Failed to update product: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to update product. Please try again.');
        }
    }

    public function destroy(Request $request)
    {
        // Debug logging
        \Log::info('Product delete request received', [
            'data' => $request->all(),
            'user' => Auth::id(),
            'wants_json' => $request->wantsJson(),
            'ajax' => $request->ajax(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'product_id' => 'required|exists:products,product_id'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Delete validation failed', [
                'errors' => $e->errors(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);
            
            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'Validation failed: ' . implode(', ', array_map(function($errors) { return implode(', ', $errors); }, $e->errors()))], 422);
            }
            throw $e;
        }

        try {
            $product = Product::where('product_id', $request->product_id)->firstOrFail();
            
            // Delete image if exists
            if ($product->image && Storage::exists('public/products/' . $product->image)) {
                Storage::delete('public/products/' . $product->image);
            }

            $product->delete();

            \Log::info('Product deleted successfully', [
                'product_id' => $request->product_id,
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['success' => 'Product deleted successfully!']);
            }

            return redirect()->route('admin.products')->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Product delete error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => Auth::id(),
                'wants_json' => $request->wantsJson(),
                'ajax' => $request->ajax()
            ]);

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson() || $request->hasHeader('X-Requested-With')) {
                return response()->json(['error' => 'Failed to delete product: ' . $e->getMessage()], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete product. Please try again.');
        }
    }
}