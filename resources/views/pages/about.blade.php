@extends('layouts.guest')
@section('title', 'About')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gray-900 text-white">
  <img src="{{ asset('images/hero3.webp') }}" 
       alt="About ARES" 
       class="w-full h-96 object-cover opacity-50"
       onerror="this.onerror=null; this.src='{{ asset('images/placeholder.jpg') }}';"
  <div class="absolute inset-0 flex items-center justify-center">
    <div class="text-center px-4">
      <h1 class="text-4xl font-bold mb-2">Welcome to ARES</h1>
      <p class="text-lg max-w-2xl mx-auto">
        At ARES, we blend fashion with purpose. As a rising apparel store, we deliver style and quality while
        empowering innovation through every thread. Get to know who we are, our values, and what drives us.
      </p>
    </div>
  </div>
</section>

<!-- About Sections -->
<section class="bg-gray-100 py-12">
  <div class="container mx-auto px-6">
    <!-- Who We Are -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4 text-black">Who We Are</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
          ARES is not just a clothing brand – we are a passionate team committed to redefining the retail experience
          in Sri Lanka. With deep roots in innovation and a dedication to quality, we bring premium fashion products
          to our customers while fostering trust, transparency, and community support. Whether it's everyday essentials
          or bold statements, ARES stands for individuality and reliability.
        </p>
      </div>
      <div>
        <img src="{{ asset('images/team.webp') }}" alt="Team at work" class="rounded-lg shadow-lg w-full">
      </div>
    </div>

    <!-- Our Story -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <img src="{{ asset('images/story.jpeg') }}" alt="Our journey" class="rounded-lg shadow-lg w-full">
      </div>
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4 text-black">Our Story</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
          Founded by a group of passionate entrepreneurs and tech-savvy designers, ARES was born out of the need for
          a modern, customer-first fashion store that caters to the evolving tastes of Sri Lankan youth. We started with
          a simple mission: offer stylish, sustainable, and affordable fashion choices for everyone. From humble beginnings
          to a growing digital presence, our journey has always been about putting people first.
        </p>
      </div>
    </div>

    <!-- Mission -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4 text-black">Our Mission</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
          To revolutionize the online fashion shopping experience by offering stylish, affordable apparel, driven by
          transparency, integrity, and excellent customer service. We aim to connect with customers emotionally and
          empower local manufacturers while staying committed to innovation and quality.
        </p>
      </div>
      <div>
        <img src="{{ asset('images/mission.webp') }}" alt="Our mission" class="rounded-lg shadow-lg w-full">
      </div>
    </div>

    <!-- Vision -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <img src="{{ asset('images/vision.jpg') }}" alt="Our vision" class="rounded-lg shadow-lg w-full">
      </div>
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4 text-black">Our Vision</h2>
        <p class="text-gray-700 text-lg leading-relaxed">
          To become the most trusted and innovative e-commerce fashion platform in Sri Lanka and beyond, promoting local
          talent and transforming how people connect with clothing and brands online. Our vision is a digital-first,
          sustainable, and inclusive future for fashion.
        </p>
      </div>
    </div>

    <!-- Core Values -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-16 items-center">
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4 text-black">Core Values</h2>
        <ul class="text-gray-700 text-lg list-disc pl-6">
          <li class="mb-2">Integrity & Trust – We operate with honesty and build trust with every order.</li>
          <li class="mb-2">Innovation – Always learning, improving, and adapting to trends and tech.</li>
          <li class="mb-2">Customer-Centric – The customer is at the heart of everything we do.</li>
          <li class="mb-2">Community & Sustainability – Supporting local and promoting ethical fashion.</li>
        </ul>
      </div>
      <div>
        <img src="{{ asset('images/values.jpeg') }}" alt="Core values" class="rounded-lg shadow-lg w-full">
      </div>
    </div>

    <!-- Founder’s Message -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
      <div>
        <img src="{{ asset('images/team.webp') }}" alt="Founder" class="rounded-lg shadow-lg w-full">
      </div>
      <div>
        <h2 class="text-3xl font-bold mb-4 border-l-4 border-blue-500 pl-4 text-black">From Our Founder</h2>
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
