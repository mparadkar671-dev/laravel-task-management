<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $task = $this->route('task');

        if (! ($task instanceof Task)) {
            $task = Task::find($task);
        }

        if (! $task) {
            return false;
        }

        return $this->user()?->can('update', $task) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        // Employees are restricted to only updating the status field
        if ($user && $user->hasRole('employee') && ! $user->hasAnyRole(['admin', 'manager'])) {
            return [
                'status' => 'required|in:pending,in-progress,completed',
                'title' => 'prohibited',
                'description' => 'prohibited',
                'priority' => 'prohibited',
                'assigned_to' => 'prohibited',
                'due_date' => 'prohibited',
            ];
        }

        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|required|in:low,medium,high',
            'status' => 'sometimes|required|in:pending,in-progress,completed',
            'assigned_to' => 'sometimes|required|exists:users,id',
            'due_date' => 'sometimes|required|date',
        ];
    }
}
