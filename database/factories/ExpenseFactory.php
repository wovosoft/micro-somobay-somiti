<?php

namespace Database\Factories;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "date" => $this->faker->date(),
            "description" => $this->faker->sentence(),
            "amount" => $this->faker->randomFloat(3, 1000, 5000),
            "type" => ['annual_meeting', 'general_meeting', 'special_meeting', 'others'][random_int(0, 3)]
        ];
    }
}
