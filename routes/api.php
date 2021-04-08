<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotesController;
use App\Http\Controllers\Api\SettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('notes.')->prefix('notes')->group(function() {
  Route::get('/', [ NotesController::class, 'get' ])->name('get');
  Route::post('/', [ NotesController::class, 'store' ])->name('store');
  Route::patch('/{id}', [ NotesController::class, 'update' ])->name('update');
  Route::delete('/{id}', [ NotesController::class, 'delete' ])->name('delete.soft');
  Route::delete('/{id}/force', [ NotesController::class, 'deleteForever' ])->name('delete.force');
  Route::patch('/{id}/restore', [ NotesController::class, 'restore' ])->name('restore');
});

Route::name('setting.')->prefix('settings')->group(function() {
  Route::patch('/', [ SettingsController::class, 'updateSettings' ])->name('update');
});
