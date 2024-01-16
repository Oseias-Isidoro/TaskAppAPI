<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\TaskRepositoryInterface;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllTasks(): Collection
    {
        return Task::all();
    }

    public function getAllUserTasks($userId)
    {
        return Task::whereUserId($userId)->get();
    }

    public function getTaskById($taskId)
    {
        return Task::find($taskId);
    }

    public function deleteTask($taskId): int
    {
        return Task::destroy($taskId);
    }

    public function createTask(array $taskDetails)
    {
        return Task::create($taskDetails);
    }

    public function updateTask($taskId, array $newDetails)
    {
        return Task::whereId($taskId)->update($newDetails);
    }
}
