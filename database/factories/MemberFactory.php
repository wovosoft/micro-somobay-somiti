<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemberFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "membership_no" => uniqid(),
            "name" => $this->faker->name(),
            "pf_index" => substr(uniqid(), -8),
            "current_workplace" => $this->faker->address(),
            "bank_joining_date" => $this->faker->date(),
            "email" => $this->faker->safeEmail()
        ];
    }
}
