{{-- resources/views/partials/navbars/navbar-customer.blade.php --}}
<nav x-data="{ openMobile:false, openStore:false, openMobileStore:false }"
     class="bg-black shadow-md fixed w-full top-0 left-0 z-50 text-white">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-20 items-center">
      <!-- Logo -->
      <div class="flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center space-x-3 text-white hover:text-gray-300">
          <div class="flex items-center">
            <img src="{{ asset('images/logo.png') }}" alt="ARES Logo" class="h-16 w-16 object-contain">
            <span class="ml-2 text-xl font-bold">ARES</span>
          </div>
        </a>
      </div>

      <!-- Center Navigation Links (Desktop) -->
      <div class="hidden md:flex space-x-8 items-center">
        <a href="{{ route('home') }}" class="text-white hover:text-gray-300 font-medium">Home</a>
        <a href="{{ route('about') }}" class="text-white hover:text-gray-300 font-medium">About</a>
        <a href="{{ route('faq') }}" class="text-white hover:text-gray-300 font-medium">FAQ</a>

        <!-- Store Dropdown (desktop) -->
        <div class="relative" @click.outside="openStore=false">
          <button type="button"
                  @click="openStore = !openStore"
                  :aria-expanded="openStore.toString()"
                  aria-haspopup="true"
                  class="text-white hover:text-gray-300 font-medium focus:outline-none flex items-center">
            Store ▾
          </button>

          <!-- Menu -->
          <div x-cloak x-show="openStore"
               x-transition.origin.top.left
               class="absolute bg-black mt-2 rounded shadow-lg z-50 min-w-[10rem] border border-white/10">
            <a href="{{ route('products.category', ['category' => 'men']) }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800">Men</a>
            <a href="{{ route('products.category', ['category' => 'women']) }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800">Women</a>
            <a href="{{ route('products.category', ['category' => 'accessories']) }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800">Accessories</a>
            <a href="{{ route('products.category', ['category' => 'footwear']) }}" class="block px-4 py-2 text-sm text-white hover:bg-gray-800">Footwear</a>
          </div>
        </div>

        <a href="{{ route('contact') }}" class="text-white hover:text-gray-300 font-medium">Contact</a>

        {{-- Track My Order: only when logged in --}}
        @auth
          <a href="{{ route('orders.track') }}" class="text-white hover:text-gray-300 font-medium">Track My Order</a>
        @endauth
      </div>

      <!-- Right Side - Auth & Icons (Desktop) -->
      <div class="hidden md:flex items-center space-x-4">
        @guest
          <a href="{{ route('login') }}" class="text-white hover:text-gray-300 font-medium">Login</a>
          <a href="{{ route('register') }}" class="bg-white text-black px-4 py-2 rounded font-medium hover:bg-gray-200 transition-colors">Register</a>
        @endguest

        @auth
          <a href="{{ route('cart.index') }}" class="text-white hover:text-gray-300 text-xl relative" aria-label="Cart">
            🛒
            @livewire('shop.cart-counter')
          </a>
          <a href="{{ route('profile.index') }}" class="text-white hover:text-gray-300 text-xl" aria-label="Profile">👤</a>
          <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-white hover:text-gray-300 font-medium">Logout</button>
          </form>
        @endauth
      </div>

      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <button type="button"
                @click="openMobile = !openMobile"
                :aria-expanded="openMobile.toString()"
                class="focus:outline-none text-white text-xl">☰</button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div x-cloak x-show="openMobile" x-transition
       class="md:hidden bg-black border-t border-gray-700">
    <div class="px-4 py-4 space-y-3">
      <a href="{{ route('home') }}" class="block text-white hover:text-gray-300 font-medium">Home</a>
      <a href="{{ route('about') }}" class="block text-white hover:text-gray-300 font-medium">About</a>
      <a href="{{ route('faq') }}" class="block text-white hover:text-gray-300 font-medium">FAQ</a>

      <!-- Mobile Store Dropdown -->
      <div class="pt-1" @click.outside="openMobileStore=false">
        <button type="button"
                @click="openMobileStore = !openMobileStore"
                class="w-full text-left text-white hover:text-gray-300 font-medium focus:outline-none">
          Store ▾
        </button>
        <div x-cloak x-show="openMobileStore" x-transition
             class="pl-4 pt-2 space-y-2">
          <a href="{{ route('products.category', ['category' => 'men']) }}" class="block text-gray-300 hover:text-white text-sm">Men</a>
          <a href="{{ route('products.category', ['category' => 'women']) }}" class="block text-gray-300 hover:text-white text-sm">Women</a>
          <a href="{{ route('products.category', ['category' => 'accessories']) }}" class="block text-gray-300 hover:text-white text-sm">Accessories</a>
          <a href="{{ route('products.category', ['category' => 'footwear']) }}" class="block text-gray-300 hover:text-white text-sm">Footwear</a>
        </div>
      </div>

      <a href="{{ route('contact') }}" class="block text-white hover:text-gray-300 font-medium">Contact</a>

      @auth
        <a href="{{ route('orders.track') }}" class="block text-white hover:text-gray-300 font-medium">Track My Order</a>
      @endauth

      <div class="border-t border-gray-700 pt-3 mt-3">
        @guest
          <a href="{{ route('login') }}" class="block text-white hover:text-gray-300 font-medium mb-2">Login</a>
          <a href="{{ route('register') }}" class="block bg-white text-black text-center py-2 rounded font-medium hover:bg-gray-200">Register</a>
        @endguest

        @auth
          <div class="flex space-x-4 mb-3">
            <a href="{{ route('cart.index') }}" class="text-white hover:text-gray-300 text-xl relative" aria-label="Cart">
              🛒
              @livewire('shop.cart-counter')
            </a>
            <a href="{{ route('profile.index') }}" class="text-white hover:text-gray-300 text-xl" aria-label="Profile">👤</a>
          </div>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="block w-full text-left text-white hover:text-gray-300 font-medium">Logout</button>
          </form>
        @endauth
      </div>
    </div>
  </div>
</nav>
