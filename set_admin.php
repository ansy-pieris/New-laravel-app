<?php

// Quick script to set user as admin
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

// Get the first user and make them admin
$user = User::first();
if ($user) {
    $user->role = 'admin';
    $user->save();
    echo "User '{$user->name}' (ID: {$user->id}) has been set as admin.\n";
} else {
    echo "No users found in database.\n";
}

// Also make sure user ID 2 is admin (if exists)
$user2 = User::find(2);
if ($user2) {
    $user2->role = 'admin';
    $user2->save();
    echo "User '{$user2->name}' (ID: 2) has been set as admin.\n";
}

echo "Done!\n";