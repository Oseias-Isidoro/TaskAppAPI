<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Auth::user()->tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = Auth::user()->tasks()
                ->create($request->validated());
        } catch (\Exception $exception)
        {
            return response()->json([
                'error' => [
                    'message' => 'Error, task not created!'
                ]
            ], 500);
        }

        return response()->json($task);
    }

    public function update(EditTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        return response()->json($task->fresh());
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();
        return response()->json([
            'success' => [
                'message' => 'Task deleted successful'
            ]
        ]);
    }
}
