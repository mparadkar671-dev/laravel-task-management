<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskHistoryResource;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only([
            'status',
            'priority',
            'due_date',
            'assigned_to',
            'search',
            'sort_by',
            'sort_order',
            'per_page',
        ]);

        $tasks = $this->taskService->getTasks($filters);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->createTask($request->validated());

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(201);
    }

    public function show(int|string $id): TaskResource
    {
        $task = $this->taskService->findById($id, ['assignedTo', 'creator', 'histories.user']);
        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, int|string $id): TaskResource
    {
        $task = $this->taskService->findById($id);
        Gate::authorize('update', $task);

        $updatedTask = $this->taskService->updateTask($task, $request->validated());

        return new TaskResource($updatedTask);
    }

    public function destroy(int|string $id): JsonResponse
    {
        $task = $this->taskService->findById($id);
        Gate::authorize('delete', $task);

        $this->taskService->deleteTask($task);

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }

    public function history(int|string $id): AnonymousResourceCollection
    {
        $task = $this->taskService->findById($id);
        Gate::authorize('view', $task);

        $histories = $this->taskService->getTaskHistories($task);

        return TaskHistoryResource::collection($histories);
    }

    public function statistics(): JsonResponse
    {
        $stats = $this->taskService->getStatistics();

        return response()->json([
            'data' => $stats,
        ], 200);
    }
}
