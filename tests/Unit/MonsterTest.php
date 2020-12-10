<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Monster;
use Tests\TestCase;
use Illuminate\Support\Str;

class MonsterTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function monster_can_be_created()
    {
        $name = Str::random(6);
        $monster = Monster::factory()->create([
            'name' => 'TestMonster_'.$name,
        ]);

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
        ]);
    }
}
