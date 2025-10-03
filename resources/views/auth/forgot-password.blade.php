@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
  {{-- Success message when reset link is sent --}}
  @if (session('status'))
    <div class="max-w-md mx-auto mt-24 mb-4 px-4 py-3 rounded bg-green-600/10 text-green-300 border border-green-600/20">
      {{ session('status') }}
    </div>
  @endif

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
      <h2 class="text-3xl font-semibold mb-6 text-center">Reset Password</h2>
      
      <div class="mb-6 text-sm text-gray-300 text-center">
        Forgot your password? No problem. Just enter your email address and we'll send you a password reset link.
      </div>

      <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium">Email Address</label>
          <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
            placeholder="Enter your email address"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <button type="submit" class="w-full bg-blue-400 bg-opacity-20 text-white py-2 rounded-md hover:bg-opacity-40 transition border border-white">
          Send Password Reset Link
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
