<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        // Get all users that are admin or staff (not customers)
        $users = User::whereIn('role', ['admin', 'staff'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.manage-staff', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,staff'
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            return redirect()->route('admin.staff')->with('success', 'Staff member created successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.staff')->with('error', 'Failed to create staff member. Please try again.');
        }
    }

    public function edit($id)
    {
        $user = User::where('id', $id)->whereIn('role', ['admin', 'staff'])->firstOrFail();
        return view('admin.edit-staff', compact('user'));
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::where('id', $id)->whereIn('role', ['admin', 'staff'])->firstOrFail();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->id)
                ],
                'role' => 'required|in:admin,staff',
                'password' => 'nullable|string|min:8'
            ]);

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $user->update($updateData);

            return redirect()->route('admin.staff')->with('success', 'Staff member updated successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.staff')->with('error', 'Failed to update staff member. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::where('id', $id)->whereIn('role', ['admin', 'staff'])->firstOrFail();
            
            // Prevent deleting the current user
            if ($user->id === auth()->id()) {
                return redirect()->route('admin.staff')->with('error', 'You cannot delete your own account.');
            }

            $user->delete();

            return redirect()->route('admin.staff')->with('success', 'Staff member deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.staff')->with('error', 'Failed to delete staff member. Please try again.');
        }
    }

    public function showResetPasswordForm($id)
    {
        $user = User::where('id', $id)->whereIn('role', ['admin', 'staff'])->firstOrFail();
        return view('admin.reset-password', compact('user'));
    }

    public function resetPassword(Request $request, $id)
    {
        try {
            $user = User::where('id', $id)->whereIn('role', ['admin', 'staff'])->firstOrFail();

            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return redirect()->route('admin.staff')->with('success', 'Password reset successfully for ' . $user->name . '!');

        } catch (\Exception $e) {
            return redirect()->route('admin.staff')->with('error', 'Failed to reset password. Please try again.');
        }
    }
}