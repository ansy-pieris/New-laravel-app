@extends('layouts.guest')
@section('title', 'Login')

@section('content')
  {{-- Fortify flash (e.g. password reset link sent) --}}
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
      <h2 class="text-3xl font-semibold mb-6 text-center">Login to ARES</h2>

      <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
          <label for="email" class="block text-sm font-medium">Email</label>
          <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <div>
          <label for="password" class="block text-sm font-medium">Password</label>
          <input
            type="password"
            name="password"
            id="password"
            required
            autocomplete="current-password"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <div class="flex items-center justify-between">
          <label class="inline-flex items-center text-sm">
            <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 bg-black text-blue-400 focus:ring-blue-400">
            Remember me
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm underline hover:text-blue-300">Forgot password?</a>
          @endif
        </div>

        <button type="submit" class="w-full bg-blue-400 bg-opacity-20 text-white py-2 rounded-md hover:bg-opacity-40 transition border border-white">
          Login
        </button>
      </form>

      <p class="mt-4 text-sm text-center">
        Don't have an account?
        <a href="{{ route('register') }}" class="underline hover:text-blue-300">Register</a>
      </p>
    </div>
  </div>
@endsection
