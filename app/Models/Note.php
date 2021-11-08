<?php

namespace App\Models;

use App\Http\Resources\NotesResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'user_id',
    'title',
    'contents',
    'color',
    'pinned_at',
    'archived_at'
  ];

  protected $casts = [
    'pinned_at' => 'datetime',
    'archived_at' => 'datetime'
  ];


  public function scopePinned($query)
  {
    return $query->whereNotNull('pinned_at');
  }

  public function scopeUnpinned($query)
  {
    return $query->whereNull('pinned_at');
  }

  public function scopeArchived($query)
  {
    return $query->whereNotNull('archived_at');
  }

  public function scopeUnarchived($query)
  {
    return $query->whereNull('archived_at');
  }

  public function getPinnedAttribute() {
    return !!$this->pinned_at;
  }

  public function getArchivedAttribute() {
    return !!$this->archived_at;
  }

  public function newCollection(array $models = [])
  {
    return NotesResource::collection($models);
  }
}
