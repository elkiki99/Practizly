<?php

use Livewire\Volt\Volt;
use App\Http\Controllers\HomePages;
use App\Http\Controllers\AdminPanel;
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
    Volt::route('{user:username}/dashboard', 'user.dashboard')->name('dashboard')->middleware(['auth', 'verified']);

    Volt::route('{user:username}/subjects', 'subjects.index')->name('subjects')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/subjects/{slug}', 'subjects.show')->name('subjects.show')->middleware(['auth', 'verified']);
    // Volt::route('{user:username}/subjects/{slug}/edit', 'subjects.edit')->name('subjects.edit')->middleware(['auth', 'verified']);

    Volt::route('{user:username}/profile', 'user.profile')->name('profile')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/calendar', 'events.index')->name('calendar')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/settings', 'user.settings')->name('settings')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/summaries', 'summaries.index')->name('summaries')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/library', 'attachments.index')->name('library')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/assignments', 'assignments.index')->name('assignments')->middleware(['auth', 'verified']);
    Volt::route('{user:username}/exams', 'exams.index')->name('exams')->middleware(['auth', 'verified']);
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
