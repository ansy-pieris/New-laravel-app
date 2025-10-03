@extends('layouts.app')
@section('title', 'Shopping Cart')

@section('content')
<div class="pt-24 pb-12 min-h-screen bg-black text-white">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-3xl font-bold text-white flex items-center">
        <svg class="w-8 h-8 mr-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m15 6a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        Your Cart
      </h1>
      <a href="{{ route('home') }}" class="text-blue-400 hover:text-blue-300 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Continue Shopping
      </a>
    </div>

    @livewire('shop.cart-page')
  </div>
</div>
@endsection