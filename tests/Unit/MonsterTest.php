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
    public function monster_can_be_created()
    {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
        ]);
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

     /** @test monster can be set as basic */
     public function monster_can_be_set_as_basic()
     {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $DBMonsterRepo = new DBMonsterRepository();
        $DBMonsterRepo->updateAuthLevel($monster_id, 'basic');

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
            'auth' => '0',
            'vip' => '0'
        ]);
     }

     /** @test monster can be set as standard */
     public function monster_can_be_set_as_standard()
     {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $DBMonsterRepo = new DBMonsterRepository();
        $DBMonsterRepo->updateAuthLevel($monster_id, 'standard');

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
            'auth' => '1',
            'vip' => '0'
        ]);
     }

     /** @test monster can be set as pro */
     public function monster_can_be_set_as_pro()
     {
        $name = Str::random(6);
        $monster_id = $this->createMonster($name);

        $DBMonsterRepo = new DBMonsterRepository();
        $DBMonsterRepo->updateAuthLevel($monster_id, 'pro');

        $this->assertDatabaseHas('monsters', [
            'name' => 'TestMonster_'.$name,
            'auth' => '1',
            'vip' => '1'
        ]);
     }
}
