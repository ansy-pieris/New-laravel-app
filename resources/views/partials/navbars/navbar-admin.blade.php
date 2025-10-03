{{-- resources/views/partials/navbars/navbar-admin.blade.php --}}

<nav class="bg-black shadow-md fixed w-full top-0 left-0 z-50 text-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-20 items-center">
      <!-- Logo -->
      <div class="flex-shrink-0">
        <a href="{{ route('home') }}" class="flex items-center space-x-2 text-xl font-bold text-white hover:text-gray-300">
          <img src="{{ asset('images/logo.png') }}" alt="ARES Logo" class="h-16 w-16 object-contain">
          <span>ARES</span>
        </a>
      </div>

      <!-- Links (Desktop) -->
      <div class="hidden md:flex space-x-8 items-center">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-gray-300">Dashboard</a>
        <a href="{{ route('admin.staff') }}" class="hover:text-gray-300">Manage Staff</a>
        <a href="{{ route('admin.products') }}" class="hover:text-gray-300">Manage Products</a>
        <a href="{{ route('admin.orders') }}" class="hover:text-gray-300">Manage Orders</a>

        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit" class="hover:text-gray-300">Logout</button>
        </form>
      </div>

      <!-- Icons (Desktop) -->
      <div class="hidden md:flex items-center space-x-4">
        <a href="{{ route('profile.index') }}" class="text-white hover:text-gray-300 text-xl" aria-label="Profile">
          <i class="fas fa-user-circle"></i>
        </a>
      </div>

      <!-- Mobile Menu Button -->
      <div class="md:hidden">
        <button id="mobile-menu-btn" class="focus:outline-none text-white">☰</button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="md:hidden hidden px-4 pb-4 space-y-2">
    <a href="{{ route('admin.dashboard') }}" class="block py-2 hover:text-gray-300">Dashboard</a>
    <a href="{{ route('admin.staff') }}" class="block py-2 hover:text-gray-300">Manage Staff</a>
    <a href="{{ route('admin.products') }}" class="block py-2 hover:text-gray-300">Manage Products</a>
    <a href="{{ route('admin.orders') }}" class="block py-2 hover:text-gray-300">Manage Orders</a>

    <form method="POST" action="{{ route('logout') }}" class="pt-2">
      @csrf
      <button type="submit" class="block py-2 hover:text-gray-300">Logout</button>
    </form>

    <!-- Profile Icon -->
    <div class="flex space-x-4 pt-2">
      <a href="{{ route('profile.index') }}" class="text-white hover:text-gray-300 text-xl" aria-label="Profile">
        <i class="fas fa-user-circle"></i>
      </a>
    </div>
  </div>
</nav>

<!-- Script -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const mobileBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    mobileBtn?.addEventListener('click', () => mobileMenu?.classList.toggle('hidden'));
  });
</script>
