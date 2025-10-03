@extends('layouts.guest')
@section('title', 'About')

@section('content')
<!-- Hero Section -->
<sectio    <!-- Founder's Message -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
      <div>
        <img src="{{ asset('images/founder.jpg') }}" alt="Founder" class="rounded-lg shadow-2xl w-full border border-gray-600 hover:shadow-cyan-500/20 transition-all duration-300">
      </div>
      <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-cyan-400 pl-4 text-white">From Our Founder</h2>
        <p class="text-gray-300 text-lg leading-relaxed italic">
          "ARES started as a dream to empower people through fashion and technology. Today, that dream has turned into a
          vibrant platform, thanks to our customers and the passionate team behind it. We're not just building a brand –
          we're building a movement for a better, more connected future."
        </p>
      </div>
    </div>
  </div>
</section>
@endsection bg-gradient-to-r from-gray-900 via-black to-gray-900 text-white min-h-[60vh] flex items-center">
  <div class="absolute inset-0">
    <img src="{{ asset('images/d5c9ca2e-7286-4aa4-bc7f-e16f93e53f4e.png') }}" alt="About ARES" class="w-full h-full object-cover opacity-20">
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70"></div>
  </div>
  
  <div class="relative container mx-auto px-6 text-center z-10">
    <div class="max-w-4xl mx-auto">
      <h1 class="text-5xl md:text-6xl font-bold mb-6 bg-gradient-to-r from-white via-gray-200 to-white bg-clip-text text-transparent">
        Welcome to ARES
      </h1>
      <p class="text-xl md:text-2xl text-gray-300 leading-relaxed max-w-3xl mx-auto">
        At ARES, we blend fashion with purpose. As a rising apparel store, we deliver style and quality while
        empowering innovation through every thread. Get to know who we are, our values, and what drives us.
      </p>
      <div class="mt-8">
        <div class="w-24 h-1 bg-gradient-to-r from-blue-400 to-purple-400 mx-auto"></div>
      </div>
    </div>
  </div>
</section>

<!-- About Sections -->
<section class="bg-gradient-to-br from-gray-900 to-black py-12">
  <div class="container mx-auto px-6">
    <!-- Who We Are -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-400 pl-4 text-white">Who We Are</h2>
        <p class="text-gray-300 text-lg leading-relaxed">
          ARES is not just a clothing brand – we are a passionate team committed to redefining the retail experience
          in Sri Lanka. With deep roots in innovation and a dedication to quality, we bring premium fashion products
          to our customers while fostering trust, transparency, and community support. Whether it's everyday essentials
          or bold statements, ARES stands for individuality and reliability.
        </p>
      </div>
      <div>
        <img src="{{ asset('images/team.jpg') }}" alt="Team at work" class="rounded-lg shadow-2xl w-full border border-gray-600 hover:shadow-blue-500/20 transition-all duration-300">
      </div>
    </div>

    <!-- Our Story -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <img src="{{ asset('images/story.jpg') }}" alt="Our journey" class="rounded-lg shadow-2xl w-full border border-gray-600 hover:shadow-green-500/20 transition-all duration-300">
      </div>
      <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-green-400 pl-4 text-white">Our Story</h2>
        <p class="text-gray-300 text-lg leading-relaxed">
          Founded by a group of passionate entrepreneurs and tech-savvy designers, ARES was born out of the need for
          a modern, customer-first fashion store that caters to the evolving tastes of Sri Lankan youth. We started with
          a simple mission: offer stylish, sustainable, and affordable fashion choices for everyone. From humble beginnings
          to a growing digital presence, our journey has always been about putting people first.
        </p>
      </div>
    </div>

    <!-- Mission -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-purple-400 pl-4 text-white">Our Mission</h2>
        <p class="text-gray-300 text-lg leading-relaxed">
          To revolutionize the online fashion shopping experience by offering stylish, affordable apparel, driven by
          transparency, integrity, and excellent customer service. We aim to connect with customers emotionally and
          empower local manufacturers while staying committed to innovation and quality.
        </p>
      </div>
      <div>
        <img src="{{ asset('images/mission.jpg') }}" alt="Our mission" class="rounded-lg shadow-2xl w-full border border-gray-600 hover:shadow-purple-500/20 transition-all duration-300">
      </div>
    </div>

    <!-- Vision -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <img src="{{ asset('images/vision.jpg') }}" alt="Our vision" class="rounded-lg shadow-2xl w-full border border-gray-600 hover:shadow-yellow-500/20 transition-all duration-300">
      </div>
      <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-yellow-400 pl-4 text-white">Our Vision</h2>
        <p class="text-gray-300 text-lg leading-relaxed">
          To become the most trusted and innovative e-commerce fashion platform in Sri Lanka and beyond, promoting local
          talent and transforming how people connect with clothing and brands online. Our vision is a digital-first,
          sustainable, and inclusive future for fashion.
        </p>
      </div>
    </div>

    <!-- Core Values -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div class="bg-gray-800/50 rounded-xl p-8 border border-gray-700 shadow-2xl">
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-red-400 pl-4 text-white">Core Values</h2>
        <ul class="text-gray-300 text-lg list-disc pl-6">
          <li class="mb-2 hover:text-white transition-colors">Integrity & Trust – We operate with honesty and build trust with every order.</li>
          <li class="mb-2 hover:text-white transition-colors">Innovation – Always learning, improving, and adapting to trends and tech.</li>
          <li class="mb-2 hover:text-white transition-colors">Customer-Centric – The customer is at the heart of everything we do.</li>
          <li class="mb-2 hover:text-white transition-colors">Community & Sustainability – Supporting local and promoting ethical fashion.</li>
        </ul>
      </div>
      <div>
        <img src="{{ asset('images/values.jpg') }}" alt="Core values" class="rounded-lg shadow-2xl w-full border border-gray-600 hover:shadow-red-500/20 transition-all duration-300">
      </div>
    </div>

    <!-- Founder’s Message -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
      <div>
        <img src="{{ asset('images/founder.jpg') }}" alt="Founder" class="rounded-lg shadow-lg w-full">
      </div>
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4">From Our Founder</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
          “ARES started as a dream to empower people through fashion and technology. Today, that dream has turned into a
          vibrant platform, thanks to our customers and the passionate team behind it. We’re not just building a brand –
          we’re building a movement for a better, more connected future.”
        </p>
      </div>
    </div>
  </div>
</section>
@endsection
