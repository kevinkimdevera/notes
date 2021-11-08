<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
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
Route::name('api.')->group(function () {
  Route::prefix('auth')->name('auth.')->group(function () {
    // REGISTER ACCOUNT
    Route::post('register', [AuthController::class, 'registerAccount'])->name('register');

    // LOGIN
    Route::post('login', [AuthController::class, 'attemptLogin'])->name('login');
  });

  Route::middleware(['auth:sanctum'])->group(function () {
    // USER DATA
    Route::prefix('auth')->name('auth.')->group(function () {
      Route::get('user', [AuthController::class, 'showUser'])->name('user.show');

      // USER SETTINGS
      Route::name('user.settings.')->prefix('settings')->group(function() {
        Route::patch('/', [ SettingsController::class, 'update' ])->name('update');
      });
    });

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
  });
});

