@extends('layouts.app')

@section('title', 'Staff Management - Admin')

@section('content')
<div class="min-h-screen bg-black py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20">
        
        <!-- Page Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Staff Management</h1>
            <p class="text-lg text-gray-400">Manage your team members and administrators</p>
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

        <!-- Add Staff Form -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-8 border border-gray-200">
            <div class="bg-black px-6 py-4 border-b border-gray-800">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Add New Staff Member
                </h2>
            </div>
            
            <form method="POST" action="{{ route('admin.staff.store') }}" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-900">Full Name</label>
                        <input name="name" type="text" placeholder="Enter full name" required value="{{ old('name') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition duration-200 bg-white text-black" />
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-900">Email Address</label>
                        <input name="email" type="email" placeholder="Enter email address" required value="{{ old('email') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition duration-200 bg-white text-black" />
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-900">Password</label>
                        <input name="password" type="password" placeholder="Enter password" required 
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition duration-200 bg-white text-black" />
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-900">Role</label>
                        <select name="role" required 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black transition duration-200 bg-white text-black">
                            <option value="">Select Role</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff Member</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-center">
                    <button type="submit" 
                            class="bg-black hover:bg-gray-800 text-white font-semibold px-8 py-3 rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition duration-200 flex items-center border-2 border-black">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>

        <!-- Staff List -->
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200">
            <div class="bg-black px-6 py-4 border-b border-gray-800">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Current Team Members
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-black uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($users->count() > 0)
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-black">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-900 text-white border border-black">
                                            #{{ $user->id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-black border-2 border-gray-300 flex items-center justify-center text-white font-bold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-black">
                                                    {{ $user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-black font-medium">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->role === 'admin')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-black text-white border-2 border-gray-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M9.504 1.132a1 1 0 01.992 0l1.75 1a1 1 0 11-.992 1.736L10 3.152l-1.254.716a1 1 0 11-.992-1.736l1.75-1zM5.618 4.504a1 1 0 01-.372 1.364L5.016 6l.23.132a1 1 0 11-.992 1.736L4 7.723V8a1 1 0 01-2 0V6a.996.996 0 01.52-.878l1.734-.99a1 1 0 011.364.372zm8.764 0a1 1 0 011.364-.372l1.733.99A1.002 1.002 0 0118 6v2a1 1 0 11-2 0v-.277l-.254.145a1 1 0 11-.992-1.736l.23-.132-.23-.132a1 1 0 01-.372-1.364zm-7 4a1 1 0 011.364-.372L10 8.848l1.254-.716a1 1 0 11.992 1.736L11 10.58V12a1 1 0 11-2 0v-1.42l-1.246-.712a1 1 0 01-.372-1.364zM3 11a1 1 0 011 1v1.42l1.246.712a1 1 0 11-.992 1.736L3 15.152l-1.254.716a1 1 0 11-.992-1.736L2 13.42V12a1 1 0 011-1zm14 0a1 1 0 011 1v1.42l1.246.712a1 1 0 11-.992 1.736L17 15.152l-1.254.716a1 1 0 11-.992-1.736L16 13.42V12a1 1 0 011-1zm-9.618 4.504a1 1 0 01.372-1.364L9 13.848l1.254.716a1 1 0 11-.992 1.736L9 15.848l-.254-.144a1 1 0 01-.372-1.364z" clip-rule="evenodd"></path>
                                                </svg>
                                                Administrator
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white text-black border-2 border-black">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                </svg>
                                                Staff Member
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.staff.edit', $user->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-white hover:bg-gray-100 text-black text-xs font-bold rounded-lg border-2 border-black transition-colors duration-150">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            EDIT
                                        </a>
                                        
                                        <!-- Reset Password Button -->
                                        <a href="{{ route('admin.staff.reset-password-form', $user->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-lg border-2 border-orange-500 transition-colors duration-150"
                                           onclick="return confirm('Reset password for {{ $user->name }}?')">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-3.586l6.879-6.879A6 6 0 0121 9z"></path>
                                            </svg>
                                            RESET
                                        </a>
                                        
                                        <!-- Delete Button -->
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.staff.destroy', $user->id) }}" class="inline-block" 
                                                  onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="inline-flex items-center px-3 py-2 bg-black hover:bg-gray-800 text-white text-xs font-bold rounded-lg border-2 border-black transition-colors duration-150">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    DELETE
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-bold text-black mb-2">No team members found</h3>
                                        <p class="text-gray-600 font-medium">Get started by adding your first staff member or administrator.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection