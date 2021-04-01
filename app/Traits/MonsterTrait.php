<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    // public function createImage($legs_image = NULL) {
    //     $output_image = imagecreatetruecolor(800, 800);
    //     if ($legs_image) {
    //         if (count($this->segments) < 2) return 'n/a';
    //     } else {
    //         if (count($this->segments) < 3) return 'n/a';
    //         $legs_image = $this->segmentsWithImages[2]->image;
    //     }

    //     $head_image = base64_decode(str_replace('data:image/png;base64,','', $this->segmentsWithImages[0]->image));
    //     $body_image = base64_decode(str_replace('data:image/png;base64,','', $this->segmentsWithImages[1]->image));
    //     $legs_image = base64_decode(str_replace('data:image/png;base64,','', $legs_image));
    //     $image_1 = imagecreatefromstring($head_image);
    //     $image_2 = imagecreatefromstring($body_image);
    //     $image_3 = imagecreatefromstring($legs_image);

    //     //Get background color
    //     $background = $this->background ? : "#FFFFFF";
    //     list($r, $g, $b) = sscanf($this->background, "#%02x%02x%02x");
    //     $backgroundColor = imagecolorallocate($output_image, $r, $g, $b);

    //     $image_path = storage_path('app/public/'.$this->id.'.png');
    //     // $image_path = Storage::url($this->id.'.png');

    //     imagefilledrectangle($output_image, 0, 0, 799, 799, $backgroundColor);
    //     imagecopy($output_image, $image_1, 0, 0, 0, 0, 800, 266);
    //     imagecopy($output_image, $image_2, 0, 233, 0, 0, 800, 299);
    //     imagecopy($output_image, $image_3, 0, 499, 0, 0, 800, 299);
    //     //Log::info('before:'.memory_get_usage(). ' ');
    //     imagepng($output_image, $image_path);
    //     //Log::info('after:'.memory_get_usage(). ' ');
        
    //     // frees images from memory
    //     imagedestroy($image_1);
    //     imagedestroy($image_2);
    //     imagedestroy($image_3);
    //     imagedestroy($output_image);

    //     // Storage::disk('public')->put('test2', $image_1);
    //     return '/storage/'.$this->id.'.png';
    //     // return Storage::url($this->id.'.png');
    // } 

    public function createImage() {
        $output_image = imagecreatetruecolor(800, 800);
        imagealphablending( $output_image, true );
        imagesavealpha( $output_image, true );

        $image_path = storage_path('app/public/'.$this->id.'.png');
        // $image_path = Storage::url($this->id.'.png');

        $head_image = imagecreatefrompng(public_path().$this->segmentsWithImages[0]->image_path);
        $body_image = imagecreatefrompng(public_path().$this->segmentsWithImages[1]->image_path);
        $legs_image = imagecreatefrompng(public_path().$this->segmentsWithImages[2]->image_path);

        if (date('m-d') == '04-01'){
            //April Fool
            $extra_image = imagecreatefrompng(public_path().'/storage/cat'.rand(1,4).'.png');
        }

        //Get background color
        $background = $this->background ? : "#FFFFFF";
        list($r, $g, $b) = sscanf($background, "#%02x%02x%02x");
        $backgroundColor = imagecolorallocate($output_image, $r, $g, $b);

        imagefilledrectangle($output_image, 0, 0, 799, 799, $backgroundColor);
        imagecopyresampled($output_image, $head_image, 0, 0, 0, 0, 800, 266, 800, 266);
        imagecopyresampled($output_image, $body_image, 0, 233, 0, 0, 800, 299, 800, 299);
        imagecopyresampled($output_image, $legs_image, 0, 499, 0, 0, 800, 299, 800, 299);

        if (date('m-d') == '04-01'){
            imagecopyresampled($output_image, $extra_image, 0, 670, 0, 0, 160, 299, 160, 299);
        }

        imagepng($output_image, $image_path);
        
        // frees images from memory
        imagedestroy($head_image);
        imagedestroy($body_image);
        imagedestroy($legs_image);
        if (date('m-d') == '04-01'){
            imagedestroy($extra_image);
        }
        imagedestroy($output_image);

        return '/storage/'.$this->id.'.png';
        // return Storage::url($this->id.'.png');
    } 

    public function createThumbnailImage(){
        $storage_path = storage_path('app/public');
        $img_path = $storage_path.'/'.$this->id.'.png';
        $thumbnail_img_path = $storage_path.'/'.$this->id.'_thumb.png';

        $this->resize_crop_image(185, 185, $img_path, $thumbnail_img_path);
        return '/storage/'.$this->id.'_thumb.png';
    }

    function resize_crop_image($max_width, $max_height, $source_file, $dst_dir){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
    
        $image_create = "imagecreatefrompng";
        $image = "imagepng";
        $quality = 7;
    
        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);
    
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
    
        $image($dst_img, $dst_dir, $quality);
    
        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);
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