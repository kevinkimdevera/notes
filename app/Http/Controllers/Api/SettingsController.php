<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
  public function updateSettings(Request $request) {
    $saved = Setting::where('key', $request->key)
      ->update([
        'value' => $request->value
      ]);

    return response()->json([
      'saved' => $saved
    ]);
  }
}
