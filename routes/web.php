<?php

use Livewire\Volt\Volt;
use App\Http\Controllers\HomePages;
use App\Http\Controllers\AdminPanel;
use App\Http\Controllers\UserDashboard;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsNotAdmin;

Route::get('/', [HomePages::class, 'welcome'])->name('welcome');
Route::get('contact', [HomePages::class, 'contact'])->name('contact');
Route::get('docs', [HomePages::class, 'docs'])->name('docs');
Route::get('blog', [HomePages::class, 'blog'])->name('blog');
Route::get('terms', [HomePages::class, 'terms'])->name('terms');
Route::get('privacy', [HomePages::class, 'privacy'])->name('privacy');
Route::get('clients', [HomePages::class, 'clients'])->name('clients');
Route::get('pricing', [HomePages::class, 'pricing'])->name('pricing');

Route::middleware([EnsureUserIsNotAdmin::class])->group(function () {
    Route::get('{user:username}/dashboard', [UserDashboard::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::get('{user:username}/profile', [UserDashboard::class, 'profile'])->middleware(['auth', 'verified'])->name('profile');
    Volt::route('{user:username}/calendar', 'calendar.index')->name('calendar');
    Volt::route('{user:username}/subjects', 'subjects.index')->name('subjects');
    Route::get('{user:username}/settings', [UserDashboard::class, 'settings'])->middleware(['auth', 'verified'])->name('settings');
    Route::get('{user:username}/summaries', [UserDashboard::class, 'summaries'])->middleware(['auth', 'verified'])->name('summaries');
    Route::get('{user:username}/library', [UserDashboard::class, 'library'])->middleware(['auth', 'verified'])->name('library');
    Volt::route('{user:username}/assignments', 'assignments.index')->name('assignments');
    Volt::route('{user:username}/exams', 'exams.index')->name('exams');
});

Route::middleware([EnsureUserIsAdmin::class])->group(function () {
    Route::get('panel', [AdminPanel::class, 'panel'])->middleware(['auth', 'verified'])->name('panel');
    Route::get('messages', [AdminPanel::class, 'messages'])->middleware(['auth', 'verified'])->name('messages');
    Route::get('marketing', [AdminPanel::class, 'marketing'])->middleware(['auth', 'verified'])->name('marketing');
    Route::get('tasks', [AdminPanel::class, 'tasks'])->middleware(['auth', 'verified'])->name('tasks');
    Route::get('seo', [AdminPanel::class, 'seo'])->middleware(['auth', 'verified'])->name('seo');
    Route::get('configuration', [AdminPanel::class, 'configuration'])->middleware(['auth', 'verified'])->name('configuration');
    Route::get('analytics', [AdminPanel::class, 'analytics'])->middleware(['auth', 'verified'])->name('analytics');
});

require __DIR__ . '/auth.php';
