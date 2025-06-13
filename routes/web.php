<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Boards - Rotas principais
    Route::get('/boards', [BoardController::class, 'index'])->name('boards.index');
    Route::get('/boards/create', [BoardController::class, 'create'])->name('boards.create');
    Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');

    // Rotas específicas para quadros
    Route::prefix('/boards/{board}')->group(function () {
        Route::get('/', [BoardController::class, 'index'])->name('boards.show');
        Route::get('/edit', [BoardController::class, 'edit'])->name('boards.edit');
        Route::put('/', [BoardController::class, 'update'])->name('boards.update');
        Route::delete('/', [BoardController::class, 'destroy'])->name('boards.destroy');

        // Operações de colunas
        Route::post('/columns', [ColumnController::class, 'store'])->name('columns.store');
        Route::put('/columns/{column}', [ColumnController::class, 'update'])->name('columns.update');
        Route::delete('/columns/{column}', [ColumnController::class, 'destroy'])->name('columns.destroy');
        Route::patch('/columns/reorder', [ColumnController::class, 'reorderColumns']);
        Route::get('/columns/{column}', [ColumnController::class, 'getColumn'])->name('boards.column');
        Route::get('/columns', [ColumnController::class, 'getColumns'])->name('boards.columns');

        // Operações de tarefas
        Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/tasks/{task}', [TaskController::class, 'showTask'])->name('tasks.show');
        Route::put('/tasks/{task}', [TaskController::class, 'updateTask'])->name('tasks.update');
        Route::delete('/tasks/{task}', [TaskController::class, 'destroyTask'])->name('tasks.destroy');
        Route::get('/stats', [TaskController::class, 'stats'])->name('boards.stats');

        Route::patch('/tasks/{task}/move', [TaskController::class, 'move'])->name('tasks.move');
    });
});

require __DIR__.'/auth.php';
