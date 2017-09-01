<?php

namespace Tests\Feature;

use App\Notifications\NewComment;
use App\Team;
use App\Ticket;
use App\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegisterTest extends TestCase{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_register(){
        $team = factory(Team::class)->create(["token" => "TEAMTOKEN"]);

        $response = $this->post('register', [
            "name"                  => "Peter parker",
            "email"                 => "peter@parker.com",
            "password"              => "secret",
            "password_confirmation" => "secret",
            "team_token"            => "TEAMTOKEN",
        ]);

        $response->assertStatus(Response::HTTP_FOUND);
        $this->assertTrue($team->members->contains(function ($member) {
            return $member->name = "Peter parker";
        }));
    }

    /** @test */
    public function can_not_register_without_a_valid_token(){
        $response = $this->post('register', [
            "name"                  => "Peter parker",
            "email"                 => "peter@parker.com",
            "password"              => "secret",
            "password_confirmation" => "secret",
            "team_token"            => "NON_EXISTING_TOKEN",
        ]);
        $response->assertStatus(Response::HTTP_FOUND);
        $response->assertSessionHasErrors("team_token");
        $this->assertEquals(0, User::count());
    }

}