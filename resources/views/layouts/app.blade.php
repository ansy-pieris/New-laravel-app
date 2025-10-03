<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <title>@yield('title', 'Apparel Store')</title>

  {{-- Favicons --}}
  <link rel="icon" type="image/x-icon" href="/favicon.ico">

  {{-- External CSS --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  
  {{-- Tailwind CSS --}}
  <script src="https://cdn.tailwindcss.com"></script>

  {{-- Livewire Styles --}}
  @livewireStyles
  
  {{-- Additional Styles --}}
  @stack('styles')
  
  {{-- Fix for any stray navbar elements --}}
  <style>
    /* Ensure no stray text appears outside navbar */
    body > *:not(nav):not(main):not(footer) {
      display: none !important;
    }
    
    /* Force clean navbar display */
    nav {
      position: fixed !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      z-index: 9999 !important;
    }
    
    /* Hide any duplicate or overlapping elements */
    nav + nav {
      display: none !important;
    }
  </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col">
  {{-- Role-specific navbars --}}
  @auth
      @if(auth()->user()->isAdmin())
          @include('partials.navbars.navbar-admin')
      @elseif(auth()->user()->isStaff())
          @include('partials.navbars.navbar-staff')
      @else
          @include('partials.navbars.navbar-customer')
      @endif
  @else
      {{-- Guest users see customer navbar --}}
      @include('partials.navbars.navbar-customer')
  @endauth

  <main class="flex-1 container mx-auto py-6">
    {{-- Flash Messages --}}
    @if(session('success'))
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
      </div>
    @endif

    @yield('content')
  </main>

  @include('partials.footer')

  {{-- Alpine.js --}}
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  {{-- Livewire Scripts --}}
  @livewireScripts

  {{-- Additional Scripts --}}
  @stack('scripts')
</body>
</html>
