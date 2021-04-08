<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'title' => $this->title,
        'contents' => $this->contents,
        'color' => $this->color,
        'pinned' => $this->pinned,
        'archived' => $this->archived,
        'trashed' => $this->trashed(),

        'updated_at' => $this->updated_at->format('M d, Y h:i:s a')
      ];
    }
}
