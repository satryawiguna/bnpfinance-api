<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class LoanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Loan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $principal = $this->faker->randomFloat(2, 0, 10000000);
        $period = rand(1, 5);
        $loanStartDate = $this->faker->date();
        $interest = (rand(1, 5) / 100);

        return [
            'principal' => $principal,
            'installment' => ($principal / ($period * 12)) + ((($interest / 100) / 12) * ($principal / ($period * 12))),
            'loan_start_date' => $loanStartDate,
            'loan_end_date' => Carbon::parse($loanStartDate)->addYears($period)->format('Y-m-d'),
            'period' => $period,
            'interest' => $interest
        ];
    }

    public function user()
    {
        return User::class;
    }
}
