<?php

namespace Database\Factories;

use App\Models\Withdraw;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Withdraw::class;

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
            "type" => ['monthly', 'onetime', 'others'][random_int(0, 2)]
        ];
    }
}
