<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monster extends Model
{
    protected $table = 'monsters';
    protected $with = array('segments', 'ratings');

    public function segments()
    {
        return $this->hasMany('App\MonsterSegment');
    }

    public function ratings()
    {
        return $this->hasMany('App\Rating', 'monster_id', 'id');
    }

    public function createImage($legs_image = NULL) {
        // $output_image = imagecreatetruecolor(800, 800);

        
        // if ($legs_image) {
        //     if (count($this->segments) < 2) return 'n/a';
        // } else {
        //     if (count($this->segments) < 3) return 'n/a';
        //     $legs_image = $this->segments[2]->image;
        // }

        // $head_image = base64_decode(str_replace('data:image/png;base64,','', $this->segments[0]->image));
        // $body_image = base64_decode(str_replace('data:image/png;base64,','', $this->segments[1]->image));
        // $legs_image = base64_decode(str_replace('data:image/png;base64,','', $legs_image));
        // $image_1 = imagecreatefromstring($head_image);
        // $image_2 = imagecreatefromstring($body_image);
        // $image_3 = imagecreatefromstring($legs_image);

        // $white = imagecolorallocate($output_image, 255, 255, 255);
        // $image_path = storage_path('app/public/'.$this->id.'.png');

        // imagefilledrectangle($output_image, 0, 0, 799, 799, $white);
        // imagecopy($output_image, $image_1, 0, 0, 0, 0, 800, 266);
        // imagecopy($output_image, $image_2, 0, 233, 0, 0, 800, 299);
        // imagecopy($output_image, $image_3, 0, 499, 0, 0, 800, 299);
        // imagepng($output_image, $image_path);

        // // Storage::disk('public')->put('test2', $image_1);
        // return '/storage/'.$this->id.'.png';
    }  
}
