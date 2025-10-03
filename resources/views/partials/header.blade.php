<header class="bg-white shadow-md border-b">
  <div class="container mx-auto px-4">
    <div class="flex justify-between items-center py-4">
      {{-- Logo --}}
      <div class="flex items-center">
        <a href="{{ url('/') }}" class="flex items-center space-x-3">
          <div class="bg-black text-white p-2 rounded-lg">
            <i class="fas fa-tshirt text-xl"></i>
          </div>
          <h1 class="text-2xl font-bold text-black">Apparel Store</h1>
        </a>
      </div>

      {{-- Navigation Links --}}
      <nav class="hidden md:flex space-x-8">
        <a href="{{ url('/') }}" class="text-gray-700 hover:text-black font-medium">Home</a>
        <a href="#" class="text-gray-700 hover:text-black font-medium">Categories</a>
        <a href="#" class="text-gray-700 hover:text-black font-medium">New Arrivals</a>
        <a href="#" class="text-gray-700 hover:text-black font-medium">Sale</a>
        <a href="#" class="text-gray-700 hover:text-black font-medium">Contact</a>
      </nav>

      {{-- Right Side Actions --}}
      <div class="flex items-center space-x-4">
        {{-- Search --}}
        <button class="text-gray-700 hover:text-black">
          <i class="fas fa-search text-lg"></i>
        </button>

        {{-- Authentication Links --}}
        @guest
          <a href="{{ route('login') }}" class="text-gray-700 hover:text-black font-medium">Login</a>
          <a href="{{ route('register') }}" class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 font-medium">
            Sign Up
          </a>
        @else
          {{-- Cart Icon (Coming Soon) --}}
          <button disabled class="text-gray-400 cursor-not-allowed opacity-50" title="Cart Coming Soon">
            <i class="fas fa-shopping-bag text-lg"></i>
          </button>

          {{-- User Dropdown --}}
          <div class="relative">
            <button class="flex items-center space-x-2 text-gray-700 hover:text-black" onclick="toggleDropdown('headerDropdown')">
              <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-sm"></i>
              </div>
              <span class="font-medium">{{ Auth::user()->name }}</span>
              <i class="fas fa-chevron-down text-sm"></i>
            </button>
            <div id="headerDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50 border">
              <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
              </a>
              <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-user mr-2"></i>Profile
              </a>
              <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                <i class="fas fa-history mr-2"></i>Orders
              </a>
              <div class="border-t border-gray-200 my-1"></div>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100">
                  <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
              </form>
            </div>
          </div>
        @endguest

        {{-- Mobile Menu Button --}}
        <button class="md:hidden text-gray-700 hover:text-black" onclick="toggleMobileMenu()">
          <i class="fas fa-bars text-lg"></i>
        </button>
      </div>
    </div>

    {{-- Mobile Menu --}}
    <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200 py-4">
      <div class="space-y-2">
        <a href="{{ url('/') }}" class="block py-2 text-gray-700 hover:text-black">Home</a>
        <a href="#" class="block py-2 text-gray-700 hover:text-black">Categories</a>
        <a href="#" class="block py-2 text-gray-700 hover:text-black">New Arrivals</a>
        <a href="#" class="block py-2 text-gray-700 hover:text-black">Sale</a>
        <a href="#" class="block py-2 text-gray-700 hover:text-black">Contact</a>
      </div>
    </div>
  </div>
</header>

<script>
function toggleDropdown(id) {
  document.getElementById(id).classList.toggle('hidden');
}

function toggleMobileMenu() {
  document.getElementById('mobileMenu').classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
  const dropdown = document.getElementById('headerDropdown');
  const button = event.target.closest('button');
  if (!button || !button.onclick) {
    if (dropdown) dropdown.classList.add('hidden');
  }
});
</script>