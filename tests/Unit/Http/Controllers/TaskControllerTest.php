<?php

namespace Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_user_tasks()
    {
        Task::factory()->count(6)->create();

        $user = User::factory()->create();
        $task = Task::factory()->count(6)->create([
            'user_id' => $user->id
        ]);

        $response = $this->get('api/tasks', [
            'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
        ]);

        $response->assertOk();
        $this->assertCount(6, $response->json());
        $this->assertDatabaseCount(Task::class, 12);

    }

    public function test_create_task()
    {
        $user = User::factory()->create();

        $response = $this->post('api/tasks', [
            'description' => 'this is just a test!',
            'parent_task_id' => null
        ], [
            'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount(Task::class, 1);
        $this->assertDatabaseHas(Task::class, [
            'user_id' => $user->id,
            'description' => 'this is just a test!',
            'parent_task_id' => null,
            'status' => 'pending'
        ]);
    }

    public function test_create_task_with_parent()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
           'user_id' => $user->id
        ]);

        $response = $this->post('api/tasks', [
            'description' => 'this is just a test!',
            'parent_task_id' => $task->id
        ], [
            'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseCount(Task::class, 2);
        $this->assertDatabaseHas(Task::class, [
            'user_id' => $user->id,
            'description' => 'this is just a test!',
            'parent_task_id' => $task->id,
            'status' => 'pending'
        ]);
    }

    /**
     * @dataProvider creationTaskRequestValidation
     */
    public function test_creation_task_validation($data)
    {
        $user = User::factory()->create();

        $response = $this->post(
            'api/tasks',
            $data,
            [
                'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
            ]
        );

        $response->assertStatus(422);
        $this->assertDatabaseCount(Task::class, 0);
    }

    public static function creationTaskRequestValidation(): array
    {
        return [
            'without description' => [
                ['parent_task_id' => null]
            ],
            'description null' => [
                ['description' => null]
            ],
            'without parent_task_id' => [
                ['description' => 'this is just a test!']
            ],
            'with non-existent parent_task_id' => [
                [
                    'description' => 'this is just a test!',
                    'parent_task_id' => 3
                ]
            ],
            'without data' => [
                []
            ]
        ];
    }


    /**
     * @dataProvider editTaskData
     */
    public function test_edit_task($data)
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->put(
            "api/tasks/$task->id",
            $data,
            [
                'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
            ]
        );

        $response->assertOk();
    }

    public static function editTaskData()
    {
        return [
            'edit description' => [
                ['description' => 'edit test!']
            ],
            'edit description and status' => [
                [
                    'description' => 'this is just a test!',
                    'status' => 'deleted'
                ]
            ],
            'turn status to completed' => [
                ['status' => 'completed']
            ],
            'turn status to deleted' => [
                ['status' => 'deleted']
            ]
        ];
    }

    /**
     * @dataProvider editTaskRequestValidationData
     */
    public function test_edit_task_request_validation($data)
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->put(
            "api/tasks/$task->id",
            $data,
            [
                'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
            ]
        );

        $response->assertStatus(422);
    }

    public static function editTaskRequestValidationData(): array
    {
        return [
            'edit description with a 300 char text' => [
                ['description' => fake()->realText(300)]
            ],
            'edit description with null' => [
                ['description' => null]
            ],
            'edit status, with status that dont exist' => [
                ['status' => 'finish']
            ],
            'edit status, with status null' => [
                ['status' => null]
            ],
        ];
    }

    public function test_delete_task()
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->delete(
            "api/tasks/$task->id",
            [],
            [
                'Authorization' => 'Bearer '.$user->createToken('token')->plainTextToken
            ]
        );

        $response->assertOk();
        $this->assertDatabaseCount(Task::class, 0);
    }
}
