<?php

namespace Database\Seeders;

use App\Models\Deposit;
use App\Models\Expense;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        \App\Models\User::factory(10)->create();
        (new User())
            ->forceFill([
                "name" => "Admin",
                "email" => "admin@admin.com",
                "password" => Hash::make("admin123456789")
            ])
            ->saveOrFail();
        Member::factory(200)->create();
        Expense::factory(400)->create();
        Deposit::factory(300)->create();
    }
}
