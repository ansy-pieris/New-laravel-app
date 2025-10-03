@extends('layouts.guest')
@section('title', 'Contact')

@section('content')
<div class="bg-black text-white pt-20">

  {{-- Hero Section with Background Image and Text Overlay --}}
  <div class="relative h-[300px] md:h-[400px] lg:h-[400px] w-full">
    {{-- Background Image --}}
    <img src="{{ asset('images/hero3.webp') }}" 
         alt="Contact Banner" 
         class="absolute inset-0 w-full h-full object-cover opacity-100"
         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';"

    {{-- Optional dark overlay --}}
    <div class="absolute inset-0 bg-black/50"></div>

    {{-- Text Overlay --}}
    <div class="absolute inset-0 flex flex-col justify-center items-center text-center px-4">
      <h1 class="text-4xl md:text-5xl font-bold mb-4 z-10">How can we help you?</h1>
      <p class="text-lg text-gray-300 z-10">Choose a category below to reach the right support team quickly.</p>
    </div>
  </div>

  {{-- Support Cards Section --}}
  <div class="container mx-auto py-16 px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

      {{-- Card 1 --}}
      <div class="bg-neutral-900 p-6 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">General Support</h3>
        <p class="text-sm text-gray-400 mb-4">Browse our resources or submit a ticket to our support team.</p>
        <a href="{{ route('contact') }}" class="text-white font-medium underline">Contact</a>
      </div>

      {{-- Card 2 --}}
      <div class="bg-neutral-900 p-6 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Suggest an Integration</h3>
        <p class="text-sm text-gray-400 mb-4">Recommend a new feature or tool for ARES.</p>
        <a href="#" class="text-white font-medium underline">Suggest</a>
      </div>

      {{-- Card 3 --}}
      <div class="bg-neutral-900 p-6 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Contact Sales</h3>
        <p class="text-sm text-gray-400 mb-4">Talk to our team about bulk orders or partnerships.</p>
        <a href="{{ route('contact') }}" class="text-white font-medium underline">Contact Sales</a>
      </div>

      {{-- Card 4 --}}
      <div class="bg-neutral-900 p-6 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Technical Support</h3>
        <p class="text-sm text-gray-400 mb-4">Facing an issue? Submit a technical support ticket.</p>
        <a href="{{ route('contact') }}" class="text-white font-medium underline">Get Help</a>
      </div>

      {{-- Card 5 --}}
      <div class="bg-neutral-900 p-6 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Partner Program</h3>
        <p class="text-sm text-gray-400 mb-4">Explore affiliate and co-marketing opportunities.</p>
        <a href="#" class="text-white font-medium underline">Join Us</a>
      </div>

      {{-- Card 6 --}}
      <div class="bg-neutral-900 p-6 rounded-xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Billing & Account</h3>
        <p class="text-sm text-gray-400 mb-4">Questions about payments, subscriptions, or account details?</p>
        <a href="{{ route('contact') }}" class="text-white font-medium underline">Ask Billing</a>
      </div>

    </div>
  </div>
</div>
@endsection
