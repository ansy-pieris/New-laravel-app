@extends('layouts.app')

@section('title', 'Edit Staff Member - Admin')

@section('content')
<div class="min-h-screen bg-black py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        
        <!-- Page Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Edit Staff Member</h1>
            <p class="text-gray-400 text-sm">Update the details of the selected team member</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Edit Staff Form -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200">
            <div class="bg-black px-6 py-4 border-b border-gray-800">
                <h2 class="text-xl font-semibold text-white">Edit User #{{ $user->id }}</h2>
            </div>
            
            <form method="POST" action="{{ route('admin.staff.update', $user->id) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Full Name</label>
                        <input name="name" type="text" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-white text-black focus:ring-2 focus:ring-black focus:border-black transition duration-200" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Email Address</label>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-white text-black focus:ring-2 focus:ring-black focus:border-black transition duration-200" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-900">Role</label>
                        <select name="role" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg bg-white text-black focus:ring-2 focus:ring-black focus:border-black transition duration-200">
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>Staff Member</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-between items-center pt-4">
                    <!-- Back Button -->
                    <a href="{{ route('admin.staff') }}"
                       class="text-sm text-gray-600 hover:underline hover:text-black transition">
                        ← Back to Staff Management
                    </a>
                    
                    <!-- Submit -->
                    <button type="submit" 
                            class="bg-black hover:bg-gray-800 text-white font-semibold px-6 py-3 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 flex items-center border-2 border-black">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection