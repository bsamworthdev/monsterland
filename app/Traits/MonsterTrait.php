<?php

namespace App\Traits;

trait MonsterTrait
{
    public function segments()
    {
        return $this->hasMany('App\Models\MonsterSegment')
            ->select('id', 'created_by','monster_id', 'email_on_complete', 'segment',
            'created_by_session_id', 'created_by_group_username', 'created_at', 'updated_at');
    }

    public function segmentsWithImages()
    {
        return $this->hasMany('App\Models\MonsterSegment');
    }

    public function ratings()
    {
        return $this->hasMany('App\Models\Rating', 'monster_id', 'id');
    }

    public function createImage($legs_image = NULL) {
        $output_image = imagecreatetruecolor(800, 800);
        if ($legs_image) {
            if (count($this->segments) < 2) return 'n/a';
        } else {
            if (count($this->segments) < 3) return 'n/a';
            $legs_image = $this->segmentsWithImages[2]->image;
        }

        $head_image = base64_decode(str_replace('data:image/png;base64,','', $this->segmentsWithImages[0]->image));
        $body_image = base64_decode(str_replace('data:image/png;base64,','', $this->segmentsWithImages[1]->image));
        $legs_image = base64_decode(str_replace('data:image/png;base64,','', $legs_image));
        $image_1 = imagecreatefromstring($head_image);
        $image_2 = imagecreatefromstring($body_image);
        $image_3 = imagecreatefromstring($legs_image);

        //Get background color
        $background = $this->background ? : "#FFFFFF";
        list($r, $g, $b) = sscanf($this->background, "#%02x%02x%02x");
        $backgroundColor = imagecolorallocate($output_image, $r, $g, $b);

        $image_path = storage_path('app/public/'.$this->id.'.png');
        // $image_path = Storage::url($this->id.'.png');

        imagefilledrectangle($output_image, 0, 0, 799, 799, $backgroundColor);
        imagecopy($output_image, $image_1, 0, 0, 0, 0, 800, 266);
        imagecopy($output_image, $image_2, 0, 233, 0, 0, 800, 299);
        imagecopy($output_image, $image_3, 0, 499, 0, 0, 800, 299);
        //Log::info('before:'.memory_get_usage(). ' ');
        imagepng($output_image, $image_path);
        //Log::info('after:'.memory_get_usage(). ' ');
        
        // frees images from memory
        imagedestroy($image_1);
        imagedestroy($image_2);
        imagedestroy($image_3);
        imagedestroy($output_image);

        // Storage::disk('public')->put('test2', $image_1);
        return '/storage/'.$this->id.'.png';
        // return Storage::url($this->id.'.png');
    } 

    public function books(){
        return $this->belongsToMany('App\Models\Book');
    }

    public function linkedUsers(){
        //Users linked to this monsters (i.e. has contributed to or commented on it)
        return $this->belongsToMany('App\Models\User', 'user_linked_monsters');
    }

    public function favouritedByUsers(){
        return $this->belongsToMany('App\Models\User', 'favourites')->select('users.id');
    }
}