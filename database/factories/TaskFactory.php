<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word($nb = 3),
            'description' => $this->faker->text($maxNbChars = 255) ,
            'deadline' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
        ];
    }
}
