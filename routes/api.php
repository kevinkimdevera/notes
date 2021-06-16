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
  // GET NOTES
  Route::get('/', [ NotesController::class, 'get' ])->name('get');

  // SAVE NOTE
  Route::post('/', [ NotesController::class, 'store' ])->name('store');

  // UPDATE NOTE
  Route::patch('/{note}', [ NotesController::class, 'update' ])->name('update');

  // SOFT DELETE NOTE
  Route::delete('/{note}', [ NotesController::class, 'delete' ])->name('delete.soft');

  // FORCE DELETE NOTE
  Route::delete('/{note}/force', [ NotesController::class, 'deleteForever' ])->name('delete.force');

  // RESTORE NOTE
  Route::patch('/{note}/restore', [ NotesController::class, 'restore' ])->name('restore');

  // DELETE ALL SOFT-DELETED NOTES
  Route::delete('/', [ NotesController::class, 'emptyTrash' ])->name('delete.trash');
});

Route::name('setting.')->prefix('settings')->group(function() {
  Route::get('/', [ SettingsController::class, 'get' ])->name('get');
  Route::patch('/', [ SettingsController::class, 'update' ])->name('update');
});
