<?php

namespace Database\Factories;

use App\Models\MonsterSegment;
use Illuminate\Database\Eloquent\Factories\Factory;

class MonsterSegmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $monster_id;
    protected $segment;

    protected $model = MonsterSegment::class;

    public function _construct($monster_id, $segment){
        $this->monster_id = $monster_id;
        $this->segment = $segment;
    }

    public function definition()
    {
        return [
            'monster_id' => $this->monster_id,
            'segment' => $this->segment,
            'image' => 'test_image.png',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
