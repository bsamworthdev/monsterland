<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Monster;
use Tests\TestCase;

class MonsterTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function monster_can_be_created()
    {
        $monster = Monster::factory()->create([
            'name' => 'TestMonster_GwqPFafsSL',
        ]);

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_GwqPFafsSL',
        ]);
    }
}
