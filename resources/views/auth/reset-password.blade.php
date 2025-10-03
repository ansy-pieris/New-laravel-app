@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
  {{-- Validation errors --}}
  @if ($errors->any())
    <div class="max-w-md mx-auto mt-24 mb-4 px-4 py-3 rounded bg-red-600/10 text-red-300 border border-red-600/20">
      <ul class="list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="min-h-screen bg-black flex items-center justify-center pt-20">
    <div class="bg-blue-400 bg-opacity-5 backdrop-blur-md text-white rounded-xl shadow-lg w-full max-w-md p-8 border border-blue-400">
      <h2 class="text-3xl font-semibold mb-6 text-center">Create New Password</h2>
      
      <div class="mb-6 text-sm text-gray-300 text-center">
        Enter your email and create a new password for your account.
      </div>

      <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
          <label for="email" class="block text-sm font-medium">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', $request->email) }}"
            required
            autofocus
            autocomplete="username"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium">New Password</label>
          <input
            type="password"
            name="password"
            id="password"
            required
            autocomplete="new-password"
            placeholder="Enter new password"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium">Confirm Password</label>
          <input
            type="password"
            name="password_confirmation"
            id="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="Confirm new password"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <button type="submit" class="w-full bg-blue-400 bg-opacity-20 text-white py-2 rounded-md hover:bg-opacity-40 transition border border-white">
          Reset Password
        </button>
      </form>

      <div class="mt-6 text-center">
        <a href="{{ route('login') }}" class="text-sm underline hover:text-blue-300">
          Back to Login
        </a>
      </div>
    </div>
  </div>
@endsection
