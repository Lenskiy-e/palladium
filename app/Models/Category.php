<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use App\Traits\ActionsLogTrait;

class Category extends Model
{
    use CrudTrait;
    use ActionsLogTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['image','icon','main','sort_order','status','volume_weight'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function categoryDescription(){
        return $this->balongsTo(CategoryDescription::class,'id','category_id');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setImageAttribute($value)
    {
        
        $attribute_name = "image";
        $disk = "uploads";
        $folder = "categories";
            
        // if the image was erased
        if ($value == null)
        {
            // delete the image from disk
            \Storage::disk($disk)
                ->delete($this->{$attribute_name});
                
            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }
            
        // if a base64 was sent, store it in the db
        if (starts_with($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value);
            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)
                ->put($folder . '/' . $filename, $image->stream());
            // 3. Save the path to the database
            $this->attributes[$attribute_name] = $disk . '/' . $folder . '/' . $filename;
        }
    }
}
