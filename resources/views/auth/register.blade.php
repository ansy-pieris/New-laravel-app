@extends('layouts.guest')
@section('title', 'Register')

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
      <h2 class="text-3xl font-semibold mb-6 text-center">Join ARES</h2>

      <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
          <label for="name" class="block text-sm font-medium">Name</label>
          <input
            type="text"
            name="name"
            id="name"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        <div>
          <label for="email" class="block text-sm font-medium">Email</label>
          <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email') }}"
            required
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
            autocomplete="new-password"
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
            class="w-full px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-200 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-400" />
        </div>

        @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
          <div class="flex items-start space-x-2">
            <input type="checkbox" name="terms" id="terms" required class="mt-1 rounded border-gray-300 bg-black text-blue-400 focus:ring-blue-400">
            <label for="terms" class="text-sm">
              I agree to the 
              <a target="_blank" href="{{ route('terms.show') }}" class="text-blue-400 underline hover:text-blue-300">Terms of Service</a>
              and 
              <a target="_blank" href="{{ route('policy.show') }}" class="text-blue-400 underline hover:text-blue-300">Privacy Policy</a>
            </label>
          </div>
        @endif

        <button type="submit" class="w-full bg-blue-400 bg-opacity-20 text-white py-2 rounded-md hover:bg-opacity-40 transition border border-white">
          Register
        </button>
      </form>

      <p class="mt-4 text-sm text-center">
        Already have an account?
        <a href="{{ route('login') }}" class="underline hover:text-blue-300">Login</a>
      </p>
    </div>
  </div>
@endsection
