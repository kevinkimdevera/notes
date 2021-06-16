<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
  public function get() {
    $settings = Setting::all()->mapWithKeys( function($item) {
      return [$item['key'] => $item['value']];
    });

    return [
      'view' => $settings['view'] ?? 'grid',
      'theme' => $settings['theme'] ?? 'light'
    ];
  }

  public function update(Request $request) {
    $saved = Setting::upsert([
        'key' => $request->key,
        'value' => $request->value
      ], ['key']);

    return response()->json([
      'saved' => $saved
    ]);
  }
}
