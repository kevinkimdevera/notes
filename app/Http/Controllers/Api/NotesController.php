<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Setting;
use Illuminate\Http\Request;

class NotesController extends Controller
{
  public function get() {
    return [
      'notes' => [
        'unpinned' => Note::unarchived()->unpinned()->latest()->get(),
        'pinned' => Note::unarchived()->pinned()->latest('pinned_at')->get(),
        'archived' => Note::archived()->latest('archived_at')->get(),
        'trashed' => Note::onlyTrashed()->latest('deleted_at')->get()
      ],
      'settings' => Setting::all()->mapWithKeys( function($item) {
        return [$item['key'] => $item['value']];
      })
    ];
  }

  public function store(Request $request) {
    $note = new Note($request->all());

    return response()->json([
      'saved' => $note->save()
    ]);
  }

  public function update(Request $request, $id) {
    $updated = Note::find($id)->update($request->all());

    return response()->json([
      'updated' => $updated
    ]);
  }

  public function delete($id) {
    $note = Note::find($id);
    $note->update([
      'pinned_at' => null
    ]);

    return response()->json([
      'deleted' => $note->delete()
    ]);
  }

  public function restore($id) {
    $restored = Note::onlyTrashed()
      ->find($id)
      ->restore();

    return response()->json([
      'restored' => $restored
    ]);
  }

  public function deleteForever($id) {
    $deleted = Note::onlyTrashed()
      ->find($id)
      ->forceDelete();

    return response()->json([
      'deleted' => $deleted
    ]);
  }
}
