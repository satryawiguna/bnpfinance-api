<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $profileable = $this->profileable();

        return [
            'profileable_id' => $profileable::factory(),
            'profileable_type' => $profileable,
            'identity_number' => $this->faker->numerify('##########'),
            'nick_name' => $this->faker->firstName(),
            'full_name' => $this->faker->firstName() . ' ' . $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'nationality' => 'Indonesia',
            'address' => $this->faker->address(),
            'post_code' => $this->faker->numerify('#####'),
            'phone' => $this->faker->phoneNumber()
        ];
    }

    public function profileable()
    {
        return $this->faker->randomElement([
            User::class
        ]);
    }
}
