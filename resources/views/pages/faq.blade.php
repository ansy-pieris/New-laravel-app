@extends('layouts.guest')
@section('title', 'FAQ')

@section('content')
<div class="bg-black text-white min-h-screen px-6 lg:px-20 pt-24 pb-16">
    {{-- Top section: heading only --}}
    <div class="text-center mb-16">
        <h1 class="text-5xl font-bold">Frequently Asked Questions</h1>
    </div>

    {{-- FAQ Content --}}
    <div class="max-w-4xl mx-auto space-y-10">
        <div>
            <h2 class="text-2xl font-semibold">1. What is ARES?</h2>
            <p class="mt-2 text-gray-300">
                ARES is an online apparel store offering bold, modern clothing for all genders,
                inspired by the Greek god of war.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold">2. How can I place an order?</h2>
            <p class="mt-2 text-gray-300">
                Browse the store, add items to your cart, and proceed to checkout.
                We support both cash on delivery and digital payments.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold">3. What are the delivery options?</h2>
            <p class="mt-2 text-gray-300">
                Standard and express delivery available across Sri Lanka. Delivery typically takes 2–5 working days.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold">4. Can I return or exchange a product?</h2>
            <p class="mt-2 text-gray-300">
                Yes, within 7 days if the product is unused and in original condition. Contact our support team to start the process.
            </p>
        </div>

        <div>
            <h2 class="text-2xl font-semibold">5. How do I contact customer support?</h2>
            <p class="mt-2 text-gray-300">
                Use our <a href="{{ route('contact') }}" class="underline text-white">Contact Us</a> page
                or email <a href="mailto:support@aresapparel.lk" class="underline text-white">support@aresapparel.lk</a>.
            </p>
        </div>
    </div>
</div>
@endsection
