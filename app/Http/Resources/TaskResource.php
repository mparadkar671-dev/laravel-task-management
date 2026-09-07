<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'description' => $this->description,
        'priority' => $this->priority,
        'status' => $this->status,
        'due_date' => $this->due_date,
        'assigned_to' => [
            'id' => $this->assignedTo->id,
            'name' => $this->assignedTo->name,
        ],
        'created_at' => $this->created_at->format('Y-m-d H:i:s'),
    ];
}
}
