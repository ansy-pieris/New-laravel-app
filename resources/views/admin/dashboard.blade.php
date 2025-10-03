@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="min-h-screen bg-black text-white pt-24 px-6 lg:px-20 pb-16">

    <!-- Welcome Box -->
    <div class="bg-gray-800 p-6 rounded-xl mb-10 shadow-md">
        <h2 class="text-2xl font-semibold mb-2">Welcome, {{ $userName }} 👑</h2>
        <p class="text-gray-300">You're now logged in to the admin dashboard. Please follow the guidelines below:</p>
        <ul class="list-disc list-inside mt-4 text-gray-300 space-y-1">
            <li>Only assign staff roles to trusted users.</li>
            <li>Monitor all staff activities and customer interactions.</li>
            <li>Verify product accuracy and stock levels regularly.</li>
            <li>Ensure sensitive customer data remains secure.</li>
            <li>Approve, cancel, or escalate orders as necessary.</li>
        </ul>
    </div>

    <!-- Dashboard Summary Cards -->
    <h1 class="text-3xl font-bold mb-10">Admin Dashboard</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Customers -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Customers</h2>
            <p class="text-3xl font-bold">{{ $totalCustomers }}</p>
        </div>

        <!-- Staff -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Staff Members</h2>
            <p class="text-3xl font-bold">{{ $totalStaff }}</p>
        </div>

        <!-- Products -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Products</h2>
            <p class="text-3xl font-bold">{{ $totalProducts }}</p>
        </div>

        <!-- Orders -->
        <div class="bg-white text-black p-6 rounded-2xl shadow-lg text-center">
            <h2 class="text-xl font-semibold mb-2">Orders</h2>
            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
        </div>
    </div>
</div>
@endsection