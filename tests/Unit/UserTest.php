<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Repositories\DBUserRepository;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    protected $DBUserRepo;

    /** @test */
    public function user_can_be_created()
    {
        $name = Str::random(6);
        $user = User::factory()->create([
            'name' => 'TestUser_'.$name,
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'TestUser_'.$name,
        ]);
    }

    /** @test */
    public function user_can_be_made_vip()
    {
        $name = Str::random(6);
        $user = User::factory()->create([
            'name' => 'TestUser_'.$name,
        ]); 

        $DBUserRepo = new DBUserRepository();
        $DBUserRepo->gildUser($user->id);
        $user = User::find($user->id);

        $this->assertEquals(1, $user->vip);

    }

    /** @test */
    public function user_can_be_made_not_vip()
    {
        $name = Str::random(6);
        $user = User::factory()->create([
            'name' => 'TestUser_'.$name,
        ]); 

        $DBUserRepo = new DBUserRepository();
        $DBUserRepo->ungildUser($user->id);
        $user = User::find($user->id);

        $this->assertEquals(0, $user->vip);

    }

    /** @test */
    public function user_can_be_set_as_needs_monitoring()
    {
        $name = Str::random(6);
        $user = User::factory()->create([
            'name' => 'TestUser_'.$name,
        ]); 

        $DBUserRepo = new DBUserRepository();
        $DBUserRepo->monitorUser($user->id);
        $user = User::find($user->id);

        $this->assertEquals(1, $user->needs_monitoring);

    }

    /** @test */
    public function user_can_be_set_as_not_needs_monitoring()
    {
        $name = Str::random(6);
        $user = User::factory()->create([
            'name' => 'TestUser_'.$name,
        ]); 

        $DBUserRepo = new DBUserRepository();
        $DBUserRepo->unmonitorUser($user->id);
        $user = User::find($user->id);

        $this->assertEquals(0, $user->needs_monitoring);

    }
}
