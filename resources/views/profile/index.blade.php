@extends('layouts.app')
@section('title', 'My Profile')

@php
  use Illuminate\Support\Str;
  $firstLetter = strtoupper(Str::substr($user->name ?? 'U', 0, 1));
  $firstName   = Str::of($user->name ?? 'User')->before(' ');
@endphp

@section('content')
  {{-- Alerts - Fixed position to avoid pushing content --}}
  @if (session('success'))
    <div id="successAlert" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 max-w-md mx-auto px-4 py-3 rounded bg-green-600/10 text-green-300 border border-green-600/20 shadow-lg backdrop-blur-sm transition-all duration-300">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div id="errorAlert" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 max-w-md mx-auto px-4 py-3 rounded bg-red-600/10 text-red-300 border border-red-600/20 shadow-lg backdrop-blur-sm transition-all duration-300">
      {{ session('error') }}
    </div>
  @endif
  @if ($errors->any())
    <div id="errorsAlert" class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 max-w-md mx-auto px-4 py-3 rounded bg-red-600/10 text-red-300 border border-red-600/20 shadow-lg backdrop-blur-sm transition-all duration-300">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="min-h-screen bg-black text-white py-12 pt-24">
    <div class="max-w-4xl mx-auto px-4">
      <!-- Profile Header -->
      <div class="text-center mb-10">
        <div class="relative inline-block">
          <div class="w-24 h-24 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-4 mx-auto">
            {{ $firstLetter }}
          </div>
          <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white"></div>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">
          Welcome back, {{ $firstName }}!
        </h1>
        <p class="text-gray-400">Manage your profile information and account settings</p>
      </div>

      <div class="grid lg:grid-cols-3 gap-8">
        <!-- Profile Form -->
        <div class="lg:col-span-2">
          <div class="bg-gray-900 rounded-2xl shadow-xl border border-gray-700 p-8">
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-2xl font-bold text-white flex items-center">
                <svg class="w-6 h-6 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profile Information
              </h2>
              <div class="flex space-x-2">
                <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-400">Online</span>
              </div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
              @csrf
              <div class="grid md:grid-cols-2 gap-6">
                <div class="relative group">
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Full Name</label>
                  <div class="relative">
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:bg-gray-700 transition-all duration-300 pl-12" required>
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                  </div>
                </div>

                <div class="relative group">
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Email Address</label>
                  <div class="relative">
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:bg-gray-700 transition-all duration-300 pl-12" required>
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <div class="relative group">
                <label class="block text-sm font-semibold text-gray-300 mb-2">Phone Number</label>
                <div class="relative">
                  <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                         class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:bg-gray-700 transition-all duration-300 pl-12">
                  <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                  </svg>
                </div>
              </div>

              <div class="relative group">
                <label class="block text-sm font-semibold text-gray-300 mb-2">Street Address</label>
                <div class="relative">
                  <textarea name="address" rows="2"
                            class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:bg-gray-700 transition-all duration-300 pl-12 resize-none">{{ old('address', $user->address ?? '') }}</textarea>
                  <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1 1 0 01-2.827 0l-4.243-4.243a8 8 0 1111.313 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
              </div>

              <div class="grid md:grid-cols-2 gap-6">
                <div class="relative group">
                  <label class="block text-sm font-semibold text-gray-300 mb-2">City</label>
                  <div class="relative">
                    <input type="text" name="city" value="{{ old('city', $user->city ?? '') }}"
                           class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:bg-gray-700 transition-all duration-300 pl-12">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                </div>

                <div class="relative group">
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Postal Code</label>
                  <div class="relative">
                    <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code ?? '') }}"
                           class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-blue-500 focus:bg-gray-700 transition-all duration-300 pl-12">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-blue-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <div class="flex justify-center pt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center space-x-2">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                  </svg>
                  <span>Update Profile</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Account Security Card -->
          <div class="bg-gray-900 rounded-2xl shadow-xl border border-gray-700 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center">
              <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              Account Security
            </h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-3 bg-green-900/30 rounded-lg border border-green-600/20">
                <span class="text-sm text-green-300">Two-Factor Auth</span>
                <span class="text-xs bg-green-600/20 text-green-300 px-2 py-1 rounded-full border border-green-600/30">Active</span>
              </div>
              <button type="button" onclick="openPasswordModal()" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.01M15 4H9m6 3v9a4 4 0 01-4 4H8a4 4 0 01-4-4V7a4 4 0 014-4h3"></path>
                </svg>
                <span>Change Password</span>
              </button>
            </div>
          </div>

          <!-- Account Stats -->
          <div class="bg-gray-900 rounded-2xl shadow-xl border border-gray-700 p-6">
            <h3 class="text-lg font-bold text-white mb-4">Account Overview</h3>
            <div class="space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-400">Member Since</span>
                <span class="text-sm font-semibold text-white">{{ optional($user->created_at)->format('M Y') }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-400">Total Orders</span>
                <span class="text-sm font-semibold text-blue-400">{{ $user->orders()->count() }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-400">Loyalty Points</span>
                <span class="text-sm font-semibold text-purple-400">—</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Password Change Modal -->
  <div id="passwordModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
    <div class="bg-gray-900 rounded-2xl shadow-2xl max-w-md w-full transform scale-95 transition-all duration-300 border border-gray-700" id="modalContent">
      <div class="p-6">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-xl font-bold text-white flex items-center">
            <svg class="w-6 h-6 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m0 0a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.01M15 4H9m6 3v9a4 4 0 01-4 4H8a4 4 0 01-4-4V7a4 4 0 014-4h3"></path>
            </svg>
            Change Password
          </h3>
          <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-300 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
          @csrf
          <div class="relative group">
            <label class="block text-sm font-semibold text-gray-300 mb-2">Current Password</label>
            <div class="relative">
              <input type="password" name="current_password" id="currentPassword"
                     class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-red-500 focus:bg-gray-700 transition-all duration-300 pr-12" required>
              <button type="button" onclick="togglePassword('currentPassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="relative group">
            <label class="block text-sm font-semibold text-gray-300 mb-2">New Password</label>
            <div class="relative">
              <input type="password" name="new_password" id="newPassword"
                     class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-red-500 focus:bg-gray-700 transition-all duration-300 pr-12" required>
              <button type="button" onclick="togglePassword('newPassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="relative group">
            <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm New Password</label>
            <div class="relative">
              <input type="password" name="new_password_confirmation" id="confirmPassword"
                     class="w-full px-4 py-3 bg-gray-800 border-2 border-gray-600 text-white rounded-xl focus:border-red-500 focus:bg-gray-700 transition-all duration-300 pr-12" required>
              <button type="button" onclick="togglePassword('confirmPassword')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex space-x-3 pt-4">
            <button type="button" onclick="closePasswordModal()" class="flex-1 bg-gray-700 hover:bg-gray-600 text-white px-4 py-3 rounded-xl font-semibold transition-all duration-300">
              Cancel
            </button>
            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
              Update Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function openPasswordModal() {
      const modal = document.getElementById('passwordModal');
      const content = document.getElementById('modalContent');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      setTimeout(() => { content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }
    function closePasswordModal() {
      const modal = document.getElementById('passwordModal');
      const content = document.getElementById('modalContent');
      content.classList.remove('scale-100'); content.classList.add('scale-95');
      setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
    }
    function togglePassword(id) {
      const el = document.getElementById(id);
      el.type = el.type === 'password' ? 'text' : 'password';
    }
    document.getElementById('passwordModal').addEventListener('click', function(e) {
      if (e.target === this) closePasswordModal();
    });
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') closePasswordModal();
    });

    // Auto-dismiss alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
      // Success alert
      const successAlert = document.getElementById('successAlert');
      if (successAlert) {
        setTimeout(function() {
          successAlert.style.opacity = '0';
          successAlert.style.transform = 'translate(-50%, -20px)';
          setTimeout(function() {
            successAlert.remove();
          }, 300);
        }, 3000);
      }

      // Error alert
      const errorAlert = document.getElementById('errorAlert');
      if (errorAlert) {
        setTimeout(function() {
          errorAlert.style.opacity = '0';
          errorAlert.style.transform = 'translate(-50%, -20px)';
          setTimeout(function() {
            errorAlert.remove();
          }, 300);
        }, 3000);
      }

      // Errors alert
      const errorsAlert = document.getElementById('errorsAlert');
      if (errorsAlert) {
        setTimeout(function() {
          errorsAlert.style.opacity = '0';
          errorsAlert.style.transform = 'translate(-50%, -20px)';
          setTimeout(function() {
            errorsAlert.remove();
          }, 300);
        }, 3000);
      }
    });
  </script>
@endsection
