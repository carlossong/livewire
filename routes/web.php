<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'))->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

use App\Livewire\Roles\RoleForm;
use App\Livewire\Roles\RoleIndex;
use App\Livewire\Users\UserForm;
use App\Livewire\Users\UserIndex;

Route::middleware(['auth', 'role:Admin'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', UserIndex::class)->name('index');
    Route::get('/create', UserForm::class)->name('create');
    Route::get('/{userId}/edit', UserForm::class)->name('edit');
});

Route::middleware(['auth', 'role:Admin'])->prefix('roles')->name('roles.')->group(function () {
    Route::get('/', RoleIndex::class)->name('index');
    Route::get('/create', RoleForm::class)->name('create');
    Route::get('/{roleId}/edit', RoleForm::class)->name('edit');
});

require __DIR__.'/settings.php';
