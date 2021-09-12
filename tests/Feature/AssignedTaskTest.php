<?php

namespace Tests\Feature;

use App\Models\AssignedTask;
use App\Models\Step;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignedTaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        Parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_store_assigned_task()
    {
        $assigned_to = User::factory()->create()->id;
        $formData = [
            'assigned_to' => $assigned_to,
            'steps' => [
                [
                    'title' => 'Title 1',
                    'description' => 'Description 1',
                ],
                [
                    'title' => 'Title 2',
                    'description' => 'Description 2',
                ],
            ],
        ];

        $this->json('POST', route('assigned_task.store'), $formData)
            ->assertStatus(200);
    }

    public function test_can_show_assigned_task()
    {
        $assigned_to = User::factory()->create()->id;

        $data = $this->create_assigned_task_with_steps($assigned_to);

        $this->get(route('assigned_task.show', $data['assigned_task']['id']))
            ->assertJsonStructure([
                '*' => [
                    'assigned_to',
                    'steps' => ['*' => [
                        'title',
                        'description',
                    ]],
                ],
            ])
            ->assertStatus(200);
    }

    public function test_can_update_assigned_task()
    {
        $assigned_to = User::factory()->create()->id;

        $data = $this->create_assigned_task_with_steps($assigned_to);

        $formData = [
            'assigned_to' => $assigned_to,
            'steps' => [
                $data['steps'][0],
                [
                    'title' => 'New Title',
                    'description' => 'New description',
                ],
            ],
        ];

        $this->json('PUT', route('assigned_task.update', $data['assigned_task']['id']), $formData)
            ->assertStatus(200);
    }

    public function test_can_destroy_assigned_task()
    {
        $assigned_to = User::factory()->create()->id;

        $data = $this->create_assigned_task_with_steps($assigned_to);

        $this->delete(route('assigned_task.destroy', $data['assigned_task']['id']))
            ->assertStatus(200);
    }

    public function test_can_list_assigned_task()
    {
        $assigned_to = User::factory()->create()->id;
        $data = $this->create_assigned_task_with_steps($assigned_to);

        $this->get(route('assigned_task.index'))
             ->assertJsonStructure([
                '*' => [
                    'assigned_to',
                    'steps' => ['*' => [
                        'title',
                        'description',
                    ]],
                ],
            ])
            ->assertStatus(200);
    }

    private function create_assigned_task_with_steps($assigned_to)
    {
        $data = new AssignedTask();
        $data->assigned_to = $assigned_to;

        $assigned_task = $this->user->assignedTasks()->save($data);

        $steps = Step::factory()->count(3)->make();
        $assigned_task->steps()->saveMany($steps);

        return ['assigned_task' => $assigned_task, 'steps' => $steps];
    }
}
