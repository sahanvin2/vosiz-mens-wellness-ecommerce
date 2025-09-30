<?php

use App\Models\User;

// Create test user and send verification email
$user = new User();
$user->name = 'Test User';
$user->email = 'test@example.com';
$user->password = bcrypt('password');
$user->role = 'user';
$user->save();

echo "User created: {$user->name} ({$user->email})\n";

// Send verification email
$user->sendEmailVerificationNotification();

echo "Verification email sent to log!\n";
echo "Check storage/logs/laravel.log for the email content.\n";