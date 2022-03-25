<?php

namespace App\Http\Controllers\Api;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotesController extends Controller
{
  public function get(Request $request)
  {
    $filter = $request->get('filter');

    $notes = Note::where('user_id', $request->user()->id)

    // WHEN FILTER IS NOT SPECIFIED
    ->when(!$filter, function ($q) {
      // Get unarchived notes
      // Sort by created_at DESC (newsest -> oldest)
      return $q->unarchived()->latest();
    })

    // WHEN FILTER IS 'archive'
    ->when(($filter == 'archive'), function ($q) {
      // Get archived notes
      // Sort by archived_at DESC
      return $q->archived()->latest('archived_at');
    })

    // WHEN FILTER IS TRASH
    ->when(($filter == 'trash'), function ($q) {
      // Get only trashed (soft deleted model) notes [deleted_at IS NOT NULL]
      // Sort by deleted_at DESC
      return $q->onlyTrashed()->latest('deleted_at');
    })

    // GET ALL RESULTS
    ->get();

    return [
      'notes' => $notes,
    ];
  }

  public function store(Request $request) {
    // UPDATE OR INSERT
    // https://laravel.com/docs/8.x/eloquent#upserts

    $saved = Note::upsert(
      $request->merge([
        'user_id' => $request->user()->id
      ])->all(),
      'id'
    );

    return response()->json([
      'saved' => !!$saved
    ]);
  }

  public function update(Request $request, $id) {
    $note = Note::find($id);
    $updated = $note->update($request->all());

    return response()->json([
      'updated' => $updated
    ]);
  }

  public function delete($id) {
    $note = Note::find($id);

    // REMOVE pinned status (Setting pinned_at to NULL)
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

  public function emptyTrash() {
    $deleted = Note::onlyTrashed()->forceDelete();
    return response()->json([
      'deleted' => $deleted
    ]);
  }
}
