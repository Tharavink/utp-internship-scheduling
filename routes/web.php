<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\UserController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\RescheduleController;

use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return redirect(route('internship.dashboard'));
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::resource('user', UserController::class)->names([
        'index'     => 'user.index',
        'create'    => 'user.create',
        'store'     => 'user.store',
        'show'      => 'user.show',
        'edit'      => 'user.edit',
        'update'    => 'user.update',
        'destroy'   => 'user.destroy'
    ]);

    Route::get('/dashboard/supervisor', function () {
        return Inertia\Inertia::render('Supervisor/Dashboard', [
            'user_index_route' => route('user.index')
        ]);
    })->name('supervisor.dashboard');

    Route::resource('term', TermController::class)->names([
        'index'     => 'term.index',
        'create'    => 'term.create',
        'store'     => 'term.store',
        'show'      => 'term.show',
        'edit'      => 'term.edit',
        'update'    => 'term.update',
        'destroy'   => 'term.destroy'
    ]);

    Route::get('/dashboard/term', function () {
        return Inertia\Inertia::render('Term/Dashboard', [
            'user' => Auth::user(),
            'term_index_route' => route('term.index'),
            'internship_index_route' => route('internship.index')
        ]);
    })->name('term.dashboard');

    Route::resource('internship', InternshipController::class)->names([
        'index'     => 'internship.index',
        'create'    => 'internship.create',
        'store'     => 'internship.store',
        'show'      => 'internship.show',
        'edit'      => 'internship.edit',
        'update'    => 'internship.update',
        'destroy'   => 'internship.destroy'
    ]);

    Route::get('/dashboard/internship', function () {
        return Inertia\Inertia::render('Internship/Dashboard', [
            'internship_index_route' => route('internship.index'),
            'user' => Auth::user()
        ]);
    })->name('internship.dashboard');

    Route::get('internship/enrol/{term_id}', function ($term_id) {
        return Inertia\Inertia::render('Internship/Edit', [
            'internship_index_route' => route('internship.index'),
            'term_index_route' => route('term.index'),
            'term_id' => $term_id
        ]);
    })->name('internship.enrol');

    Route::resource('reschedule', RescheduleController::class)->names([
        'index'     => 'reschedule.index',
        'create'    => 'reschedule.create',
        'store'     => 'reschedule.store',
        'show'      => 'reschedule.show',
        'edit'      => 'reschedule.edit',
        'update'    => 'reschedule.update',
        'destroy'   => 'reschedule.destroy'
    ]);

    Route::get('internship/detail/{internship_id}', function ($internship_id) {
        return Inertia\Inertia::render('Internship/Detail', [
            'internship_index_route' => route('internship.index'),
            'reschedule_index_route' => route('reschedule.index'),
            'user_index_route' => route('user.index'),
            'internship_id' => $internship_id,
            'user' => Auth::user()
        ]);
    })->name('internship.detail');
});