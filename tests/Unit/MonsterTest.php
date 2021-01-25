<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Monster;
use App\Models\MonsterSegment;
use Tests\TestCase;
use Illuminate\Support\Str;
use App\Repositories\DBMonsterRepository;

class MonsterTest extends TestCase
{
    use DatabaseTransactions;
    /** @test */
    public function monster_can_be_created()
    {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
        ]);
    }
    
    public function createMonster($name){
        
        $monster = Monster::factory()->create([
            'name' => 'TestMonster_'.$name,
        ]);
        $head = MonsterSegment::factory()->create([
            'monster_id' => $monster->id,
            'segment' => 'head'
        ]);
        $body = MonsterSegment::factory()->create([
            'monster_id' => $monster->id,
            'segment' => 'body'
        ]);
        $legs = MonsterSegment::factory()->create([
            'monster_id' => $monster->id,
            'segment' => 'legs'
        ]);
        return $monster->id;
    }

     /** @test */
     public function monster_can_be_rolled_back_to_head()
     {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $DBMonsterRepo = new DBMonsterRepository();
        $DBMonsterRepo->rollbackMonster($monster_id, ['body','legs']);

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
            'status' => 'awaiting body'
        ]);
     }

     /** @test */
     public function monster_can_be_rolled_back_to_body()
     {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $DBMonsterRepo = new DBMonsterRepository();
        $DBMonsterRepo->rollbackMonster($monster_id, ['legs']);

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
            'status' => 'awaiting legs'
        ]);
     }
}
