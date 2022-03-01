<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use WithFaker;

    public function setUp():void {
        parent::setUp();
        Artisan::call('migrate:fresh');
        Artisan::call('passport:install');
    }

    /**
     * @test
     */
    public function a_user_admin_can_view_all_users_loans()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $userSample = (new User())->where('id', '<>', $user->id)->get()
            ->random(1)
            ->first();

        $response = $this->actingAs($user, 'api')->withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->get(route('api.user_loans', ['id' => $userSample->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * @test
     */
    public function a_user_customer_can_view_their_own_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', '!=', 'Admin');
        })->first();

        $response = $this->actingAs($user, 'api')->withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->get(route('api.user_loans', ['id' => $user->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $userSample = (new User())->where('id', '<>', $user->id)
            ->first();

        $response = $this->actingAs($user, 'api')->withHeaders([
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        ])->get(route('api.user_loans', ['id' => $userSample->id]));

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_user_admin_can_create_a_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $userSample = (new User())->where('id', '<>', $user->id)->get()
            ->random(1)
            ->first();
        $principal = $this->faker->randomFloat(2, 0, 10000000);
        $period = rand(1, 5);
        $loanStartDate = $this->faker->date();
        $interest = (rand(1, 5) / 100);

        $response = $this->actingAs($user, 'api')->post(route('api.loan_store'), [
            'user_id' => $userSample->id,
            'principal' => $principal,
            'installment' => ($principal / ($period * 12)) + ((($interest / 100) / 12) * ($principal / ($period * 12))),
            'loan_start_date' => $loanStartDate,
            'loan_end_date' => Carbon::parse($loanStartDate)->addYears($period)->format('Y-m-d'),
            'period' => $period,
            'interest' => $interest
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * @test
     */
    public function a_user_customer_cannot_create_a_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', '!=', 'Admin');
        })->first();

        $principal = $this->faker->randomFloat(2, 0, 10000000);
        $period = rand(1, 5);
        $loanStartDate = $this->faker->date();
        $interest = (rand(1, 5) / 100);

        $response = $this->actingAs($user, 'api')->post(route('api.loan_store'), [
            'user_id' => $user->id,
            'principal' => $principal,
            'installment' => ($principal / ($period * 12)) + ((($interest / 100) / 12) * ($principal / ($period * 12))),
            'loan_start_date' => $loanStartDate,
            'loan_end_date' => Carbon::parse($loanStartDate)->addYears($period)->format('Y-m-d'),
            'period' => $period,
            'interest' => $interest
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_user_admin_can_view_detail_a_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $userSample = (new User())->where('id', '<>', $user->id)->get()
            ->random(1)
            ->first();

        $loan = Loan::where('user_id', $userSample->id)->first();

        $response = $this->actingAs($user, 'api')->get(route('api.loan', ["id" => $loan->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * @test
     */
    public function a_user_customer_can_view_their_own_detail_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', '<>', 'Admin');
        })->first();

        $loan = Loan::where('user_id', $user->id)->first();

        $response = $this->actingAs($user, 'api')->get(route('api.loan', ["id" => $loan->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * @test
     */
    public function a_user_admin_can_update_a_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $loan = Loan::all()->first();

        $principal = $this->faker->randomFloat(2, 0, 10000000);
        $period = rand(1, 5);
        $loanStartDate = $this->faker->date();
        $interest = (rand(1, 5) / 100);

        $response = $this->actingAs($user, 'api')->put(route('api.loan_update', ['id' => $loan->id]),
            [
                'user_id' => $user->id,
                'principal' => $principal,
                'installment' => ($principal / ($period * 12)) + ((($interest / 100) / 12) * ($principal / ($period * 12))),
                'loan_start_date' => $loanStartDate,
                'loan_end_date' => Carbon::parse($loanStartDate)->addYears($period)->format('Y-m-d'),
                'period' => $period,
                'interest' => $interest
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * @test
     */
    public function a_user_admin_can_delete_a_loan()
    {
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

        $user = (new User())->whereHas('role', function($query) {
            $query->where('name', 'Admin');
        })->first();

        $loan = Loan::all()->first();

        $response = $this->actingAs($user, 'api')->delete(route('api.loan_delete', ["id" => $loan->id]));

        $response->assertStatus(200);
    }
}
