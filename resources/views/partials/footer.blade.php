<footer class="mt-12 bg-gray-100 text-gray-600 py-8 px-4">
  <div class="max-w-6xl mx-auto">
    <!-- Newsletter Section -->
    <div class="text-center mb-8">
      <h3 class="text-lg font-bold mb-2">FOLLOW US</h3>
      <h4 class="mb-4">SIGN UP FOR THE ARES NEWSLETTER</h4>
      <p class="text-sm mb-4">Be the first to know about our new collections and promotions</p>
      <div class="flex justify-center">
        <form method="POST" action="#">
          @csrf
          <div class="flex">
            <input 
              type="email" 
              name="newsletter_email" 
              placeholder="Email" 
              class="px-4 py-2 border border-gray-300 rounded-l focus:outline-none" 
              required
            >
            <button type="submit" class="bg-black text-white px-4 py-2 rounded-r">
              Submit
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Links Sections -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Support Column -->
      <div>
        <h3 class="font-bold mb-4">SUPPORT</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-black">Contact Us</a></li>
          <li><a href="#" class="hover:text-black">Return and Exchange Policy</a></li>
          <li><a href="#" class="hover:text-black">Shipping Policy</a></li>
        </ul>
      </div>

      <!-- Info Column -->
      <div>
        <h3 class="font-bold mb-4">INFO</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-black">Terms and Conditions</a></li>
          <li><a href="#" class="hover:text-black">Privacy Policy</a></li>
          <li><a href="#" class="hover:text-black">Careers</a></li>
        </ul>
      </div>

      <!-- About Column -->
      <div>
        <h3 class="font-bold mb-4">ABOUT</h3>
        <ul class="space-y-2">
          <li><a href="#" class="hover:text-black">Our Story</a></li>
          <li><a href="#" class="hover:text-black">FAQ</a></li>
          <li><a href="#" class="hover:text-black">Store</a></li>
        </ul>
      </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-gray-300 mt-8 pt-6 text-center text-sm">
      <p>&copy; {{ date('Y') }} ARES Apparel. All rights reserved.</p>
    </div>
  </div>
</footer>
