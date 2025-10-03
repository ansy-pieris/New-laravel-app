<?php

// Simple script to create test user
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::firstOrCreate(
    ['email' => 'test@university.com'],
    [
        'name' => 'Test Student',
        'password' => bcrypt('password123'),
        'email_verified_at' => now()
    ]
);

echo "Test user created/found:\n";
echo "Email: " . $user->email . "\n";
echo "Name: " . $user->name . "\n";
echo "ID: " . $user->id . "\n";