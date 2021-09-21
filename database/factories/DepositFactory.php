<?php

namespace Database\Factories;

use App\Models\Deposit;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepositFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Deposit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "member_id" => random_int(1, 200),
            "date" => $this->faker->date(),
            "amount" => $this->faker->randomFloat(3, 100, 1000),
            "description" => $this->faker->sentence(3),
            "type" => ['monthly', 'onetime', 'fine', 'others'][random_int(0, 3)]
        ];
    }
}
