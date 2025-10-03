<?php

namespace App\Http\Controllers;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the profile page for any authenticated user (admin, staff, customer).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Load (or create lazily) the default shipping address for backup
        $address = ShippingAddress::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();

        // Use unified profile view for all roles
        return view('profile.index', [
            'user'    => $user,
            'address' => $address, // can be null - form will use user fields instead
        ]);
    }

    /**
     * Update profile information for any authenticated user.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'        => ['nullable', 'string', 'max:50'],
            'address'      => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'postal_code'  => ['nullable', 'string', 'max:32'],
        ]);

        // Update user fields directly since they exist in users table
        $user->fill([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'phone'        => $data['phone'],
            'address'      => $data['address'],
            'city'         => $data['city'],
            'postal_code'  => $data['postal_code'],
        ])->save();

        // Also create/update shipping address as backup for checkout
        if ($data['phone'] ?? $data['address'] ?? $data['city'] ?? $data['postal_code']) {
            $addr = ShippingAddress::firstOrNew([
                'user_id'    => $user->id,
                'is_default' => true,
            ]);

            $addr->recipient_name = $data['name'];
            $addr->phone          = $data['phone'] ?? $addr->phone;
            $addr->address        = $data['address'] ?? $addr->address;
            $addr->city           = $data['city'] ?? $addr->city;
            $addr->postal_code    = $data['postal_code'] ?? $addr->postal_code;
            if (!$addr->exists) {
                $addr->created_at = now();
            }
            $addr->save();
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password for any authenticated user.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required'],
            'new_password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->forceFill([
            'password' => Hash::make($data['new_password']),
        ])->save();

        return back()->with('success', 'Password updated successfully.');
    }
}