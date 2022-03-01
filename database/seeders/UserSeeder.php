<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\Payment;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = (new Role())->where('name', '=', 'Admin')->first();

        $user = User::create([
            'role_id' => $role->id,
            'username' => 'admin',
            'email' => 'admin@bnpfinance.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'), // 12345678
            'remember_token' => Str::random(10),
        ]);

        $user->profile()->create([
            'identity_number' => '1234567890',
            'nick_name' => 'Admin',
            'full_name' => 'Administrator'
        ]);

        User::factory()->count(19)
            ->hasProfile(1)
            ->hasLoans(1)
            ->unverified()
            ->create();

        $loans = Loan::all();
        $loans->map(function ($item) {
            $paymentCount = rand(0, ($item->period * 12));

            for ($i = 0; $i < $paymentCount; $i++) {
                $payment = Payment::orderBy('id', 'desc')->first();
                $paymentSumaries = Payment::where('loan_id', '=', $item->id)->sum('amount');

                if ($paymentSumaries == 0) {
                    $balance = $item->principal + ((($item->interest / 100) / 12) * $item->principal / ($item->period * 12));
                } else {
                    $balance = $payment->balance - $payment->amount;
                }

                if ($payment) {
                    $payment_date = Carbon::parse($payment->payment_date)->addMonth(1)->format('Y-m-d');
                } else {
                    $int = mt_rand(1609430400, 1672416000);
                    $payment_date = date("Y-m-d", $int);
                }

                Payment::create([
                        'loan_id' => $item->id,
                        'amount' => $item->installment,
                        'balance' => round($balance, 2),
                        'payment_date' => $payment_date
                    ]);
            }

        });

    }
}
