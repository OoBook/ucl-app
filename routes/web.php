<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\SimulationController;
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Teams
Route::resource('teams', TeamController::class);

// Fixtures
Route::get('/fixtures', [FixtureController::class, 'index'])->name('fixtures.index');
Route::post('/fixtures/generate', [FixtureController::class, 'generate'])->name('fixtures.generate');
Route::post('/fixtures/clear', [FixtureController::class, 'clearFixtures'])->name('fixtures.clear');

// Simulation
Route::get('/simulation', [SimulationController::class, 'index'])->name('simulation.index');
Route::post('/simulation/play-next-week', [SimulationController::class, 'playNextWeek'])->name('simulation.playNextWeek');
Route::post('/simulation/play-all-weeks', [SimulationController::class, 'playAllWeeks'])->name('simulation.playAllWeeks');
Route::post('/simulation/reset', [SimulationController::class, 'resetData'])->name('simulation.reset');

require __DIR__.'/auth.php';
