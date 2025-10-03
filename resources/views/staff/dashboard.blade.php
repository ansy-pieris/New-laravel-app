@extends('layouts.app')

@section('title', 'Staff Dashboard')

@section('content')
<div class="min-h-screen bg-black text-white pt-24 px-6 lg:px-20 pb-16">

    <!-- Welcome Box -->
    <div class="bg-gray-800 p-6 rounded-xl mb-10 shadow-md">
        <h2 class="text-2xl font-semibold mb-2">Welcome, {{ $userName }} 👋</h2>
        <p class="text-gray-300">You're now logged in to the staff dashboard. Please follow the guidelines below:</p>
        <ul class="list-disc list-inside mt-4 text-gray-300 space-y-1">
            <li>Ensure product details are accurate before adding or updating.</li>
            <li>Do not delete customer data unless instructed by an admin.</li>
            <li>Regularly check and update order statuses.</li>
            <li>Respect confidentiality of customer information.</li>
            <li>Report any suspicious activity to your administrator.</li>
        </ul>
    </div>

    <!-- Dashboard Summary Cards -->
    <h1 class="text-3xl font-bold mb-10">Staff Dashboard</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Customers -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Total number of Customers</h2>
            <p class="text-3xl font-bold">{{ $totalCustomers }}</p>
        </div>

        <!-- Products -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Total number of Products</h2>
            <p class="text-3xl font-bold">{{ $totalProducts }}</p>
        </div>

        <!-- Orders -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Total number of Orders</h2>
            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
        </div>
    </div>
</div>
@endsection