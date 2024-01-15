<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static find($taskId)
 * @method static create(array $taskDetails)
 * @method static whereId($taskId)
 * @method static whereUserId($userId)
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = array(
        'description',
        'parent_task_id',
        'status',
        'user_id'
    );
}
