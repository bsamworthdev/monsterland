<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function user_can_be_created()
    {
        $monster = User::factory()->create([
            'name' => 'TestUser',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'TestUser',
        ]);
    }
}
