<?php

use Livewire\Volt\Volt;
use App\Http\Controllers\HomePages;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomePages::class, 'welcome'])->name('welcome');
Route::get('contact', [HomePages::class, 'contact'])->name('contact');
Route::get('docs', [HomePages::class, 'docs'])->name('docs');
Route::get('blog', [HomePages::class, 'blog'])->name('blog');
Route::get('terms', [HomePages::class, 'terms'])->name('terms');
Route::get('privacy', [HomePages::class, 'privacy'])->name('privacy');
Route::get('clients', [HomePages::class, 'clients'])->name('clients');
Route::get('pricing', [HomePages::class, 'pricing'])->name('pricing');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('{user:username}/dashboard', 'user.dashboard')->name('dashboard');

    Volt::route('{user:username}/subjects', 'subjects.index')->name('subjects.index');
    Volt::route('{user:username}/subjects/{slug}', 'subjects.show')->name('subjects.show');

    Volt::route('{user:username}/subjects/{slug}/exams', 'subjects.components.exams')->name('subjects.components.exams');
    Volt::route('{user:username}/subjects/{slug}/assignments', 'subjects.components.assignments')->name('subjects.components.assignments');
    Volt::route('{user:username}/subjects/{slug}/topics', 'subjects.components.topics')->name('subjects.components.topics');
    Volt::route('{user:username}/subjects/{slug}/events', 'subjects.components.events')->name('subjects.components.events');
    Volt::route('{user:username}/subjects/{slug}/summaries', 'subjects.components.summaries')->name('subjects.components.summaries');

    Volt::route('{user:username}/calendar', 'events.index')->name('calendar');
    
    Volt::route('{user:username}/events/{slug}', 'events.show')->name('events.show');

    Volt::route('{user:username}/assignments', 'assignments.index')->name('assignments.index');
    Volt::route('{user:username}/assignments/{slug}', 'assignments.show')->name('assignments.show');

    Volt::route('{user:username}/summaries', 'summaries.index')->name('summaries.index');
    Volt::route('{user:username}/summaries/{slug}', 'summaries.show')->name('summaries.show');

    Volt::route('{user:username}/profile', 'user.profile')->name('profile');
    Volt::route('{user:username}/settings', 'user.settings')->name('settings');
    Volt::route('{user:username}/library', 'attachments.index')->name('library');
    Volt::route('{user:username}/exams', 'exams.index')->name('exams');
});

require __DIR__ . '/auth.php';
