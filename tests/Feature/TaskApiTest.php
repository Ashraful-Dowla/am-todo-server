<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp():void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->actingAs($this->user, 'api');
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_store_task()
    {
        $formData = [
            'name' => 'Task 1',
            'description' => 'Task description 1',
            'deadline' => '2020-10-08',
        ];

        $this->withoutExceptionHandling();
        $this->json('POST', route('task.store'), $formData)
            ->assertStatus(200);
    }

    public function test_can_show_task()
    {
        $task = Task::factory()->make();
        $this->user->tasks()->save($task);

        $this->get(route('task.index', $task->id))
            ->assertStatus(200);
    }

    public function test_can_update_task()
    {
        $task = Task::factory()->make();
        $this->user->tasks()->save($task);

        $updatedData = [
            'name' => 'Update Task 1',
            'description' => 'Update Task description 1',
            'deadline' => '2020-10-10',
        ];

        $this->json('PUT', route('task.update', $task->id), $updatedData)
            ->assertStatus(200);
    }

    public function test_can_destroy_task()
    {
        $task = Task::factory()->make();
        $this->user->tasks()->save($task);

        $this->delete(route('task.destroy', $task->id))
            ->assertStatus(200);
    }

    public function test_can_list_task()
    {
        $tasks = Task::factory()->count(3)->make();
        $this->user->tasks()->saveMany($tasks);

        $this->get(route('task.index'))
            ->assertJson([ 'tasks' => $tasks->toArray()])
            ->assertJsonStructure([
                'tasks' => ['*' => ['name', 'description', 'deadline']]
            ])
            ->assertStatus(200);
    }
}
