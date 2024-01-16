<?php

namespace App\Http\Controllers;

use App\Http\Requests\EditTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Interfaces\TaskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    private TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index(): JsonResponse
    {
        return response()
            ->json($this->taskRepository->getAllUserTasks(Auth::user()->id));
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = Auth::user()->id;

            $task = $this->taskRepository->createTask($data);
        } catch (\Exception $exception)
        {
            return response()->json([
                'error' => [
                    'message' => 'Error, task not created!'
                ]
            ], 500);
        }

        return response()->json($task, Response::HTTP_CREATED);
    }

    public function update(EditTaskRequest $request, $task): JsonResponse
    {
        $task = $this->taskRepository
            ->updateTask($task, $request->validated());

        return response()->json($task);
    }

    public function destroy($task): JsonResponse
    {
        $this->taskRepository->deleteTask($task);

        return response()->json([
            'success' => [
                'message' => 'Task deleted successful'
            ]
        ]);
    }
}
