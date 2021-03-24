<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Peek;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Image;

class MonsterSegment extends Model
{
    use HasFactory;

    protected $table = 'monster_segments';
    protected $fillable = array('image_path');
    protected $with = array('creator');
    protected $appends = array('peekUsed');

    public function creator()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by')
            ->select(['id', 'name', 'vip', 'needs_monitoring']);
    }

    // public function userPeeks() {
    //     return $this->hasMany('App\Models\Peek', 'user_id', 'created_by');
    // }
    
    // public function monsterPeeks() {
    //     return $this->hasMany('App\Models\Peek', 'monster_id', 'monster_id');
    // }

    public function peeksUsed() {
        return Peek::where(function($q) {
            $q->where('user_id',$this->created_by)
                ->where('monster_id',$this->monster_id);
        });
    }
    
    public function getPeekUsedAttribute() {
        // return $this->userPeeks->merge($this->monsterPeeks)->count() > 0 ? 1 : 0;
        return $this->peeksUsed()->count() > 0 ? 1 : 0;
    }


    public function monster()
    {
        return $this->belongsTo('App\Models\Monster', 'id', 'monster_id');
    }

    public function createImage($monster_id, $segment_image, $segment_name) {
        // $output_image = imagecreatetruecolor(800, 266);

        // $segment_image = base64_decode(str_replace('data:image/png;base64,','', $segment_image));
        // $segment_image = imagecreatefromstring($segment_image);

        // $image_path = storage_path('app/public/segments/'.$monster_id.'_'.$segment_name.'.png');
        // // $image_path = Storage::url($this->id.'.png');
        
        // imagesavealpha($output_image, true);
        // $color = imagecolorallocatealpha($output_image,255,255,255,127);
        // // imagefilledrectangle($output_image, 0, 0, 799, 299, $color);
        // imagefill($output_image, 0, 0, $color);
        
        // imagepng($output_image, $image_path);
        
        // // frees images from memory
        // imagedestroy($segment_image);
        // imagedestroy($output_image);

        $segment_image = str_replace('data:image/png;base64,', '', $segment_image);
        $segment_image = str_replace(' ', '+', $segment_image);
        $data = base64_decode($segment_image);
        $image_path = storage_path('app/public/segments/'.$monster_id.'_'.$segment_name.'.png');
        file_put_contents($image_path, $data, FILE_APPEND);

        // Storage::disk('public')->put('test2', $image_1);
        return '/storage/segments/'.$monster_id.'_'.$segment_name.'.png';
        // return Storage::url($this->id.'.png');
    } 

    public function createSegmentImageFromFullImage($full_image) {

        $monster_id = $this->monster_id;
        $segment_name = $this->segment;
        if ($segment_name == 'head'){
            $cropped_image = Image::make($full_image)->crop(800, 266, 0, 0)->encode('png');
        } elseif($segment_name == 'body') {
            $cropped_image = Image::make($full_image)->crop(800, 299, 0, 236)->encode('png');
        }

        $image_path = storage_path('app/public/segments/'.$monster_id.'_'.$segment_name.'.png');
        file_put_contents($image_path, $cropped_image);

        return '/storage/segments/'.$monster_id.'_'.$segment_name.'.png';
    } 

    public function cloneSegmentImage($existing_monster_id){
        
        $existing_image_path = storage_path('app/public/segments/'.$existing_monster_id.'_'.$this->segment.'.png');
        $new_image_path = storage_path('app/public/segments/'.$this->monster_id.'_'.$this->segment.'.png');
        // Log::Debug('$new_image_path='.$new_image_path);
        // Log::Debug('$existing_image_path='.$existing_image_path);
        File::copy($existing_image_path , $new_image_path);
        return '/storage/segments/'.$this->monster_id.'_'.$this->segment.'.png';

    }
}
