<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::view('cashier', 'cashier')
    ->middleware(['auth'])
    ->name('cashier');

Route::view('products', 'product')
    ->middleware(['auth'])
    ->name('product');

Route::view('category', 'category')
    ->middleware(['auth'])
    ->name('category');

Route::view('buyer', 'buyer')
    ->middleware(['auth'])
    ->name('buyer');
require __DIR__ . '/auth.php';
