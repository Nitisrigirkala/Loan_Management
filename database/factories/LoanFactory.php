<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition()
    {
        return [
            'amount' => $this->faker->numberBetween(1000, 10000),
            'interest_rate' => $this->faker->randomFloat(2, 1, 10),
            'duration_years' => $this->faker->numberBetween(1, 5),
            'lender_id' => User::factory(),
            'borrower_id' => User::factory(),
        ];
    }
}
