<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'changed_by' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'changed_at' => $this->changed_at,
        ];
    }
}
