<?php

namespace Tests\Unit;

use App\Models\Profile;
use App\Models\User;
use App\Notifications\WelcomeEmailNotification;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     */
    public function it_use_the_correct_driver()
    {
        $expectDrivers = ['smtp'];

        $this->seed(RoleSeeder::class);

        User::factory()
            ->hasProfile()
            ->create();

        $user = (new User())->with('profile')->first();

        $notification = new WelcomeEmailNotification($user);

        $this->assertEquals($expectDrivers, $notification->via(new User));
    }

    /**
     * @test
     */
    public function it_send_the_correct_message()
    {
        $this->seed(RoleSeeder::class);

        User::factory()
            ->hasProfile()
            ->create();

        $user = (new User())->with('profile')->first();

        $notification = new WelcomeEmailNotification($user);

        $expectSubject = "Finance Subscription";
        $expectGreeting = "Hello, " . $user->profile->full_name;
        $expectIntrolines = ["Welcome to BnPFinance."];
        $expectOutrolines = ["Thank you for using our application!"];
        $expectActionTest = "Explore";
        $expectActionUrl = url('/');

        $message = $notification->toMail($user);

        $this->assertEquals($expectSubject, $message->subject);
        $this->assertEquals($expectGreeting, $message->greeting);
        $this->assertEquals($expectIntrolines, $message->introLines);
        $this->assertEquals($expectOutrolines, $message->outroLines);
        $this->assertEquals($expectActionTest, $message->actionText);
        $this->assertEquals($expectActionUrl, $message->actionUrl);

    }
}
