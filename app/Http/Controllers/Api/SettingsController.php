<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
  public function update(Request $request) {
    $saved = $request->user()
      ->settings()
      ->update([
        'dark' => $request->dark,
        'view' => $request->view
      ]);

    return response()->json([
      'saved' => $saved
    ]);
  }
}
