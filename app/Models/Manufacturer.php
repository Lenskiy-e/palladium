<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use App\Traits\ActionsLogTrait;

class Manufacturer extends Model
{
    use CrudTrait;
    use ActionsLogTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'manufacturers';
    protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['title','h1','description','meta_title','meta_description','image','sort_order','active'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getCategories() : Builder
    {
        $result = CategoryDescription::whereHasAndWith('activeProducts', function($query){
            $query->where('manufacturer_id', $this->id);
        });

        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function products()
    {
        return $this->hasMany(ProductDescription::class);
    }

    public function activeProducts()
    {
        return $this->products()->where('edit_status', 3)
            ->whereHas('product', static function ($q) {
                $q->where('active', 1);
            });
    }

    public function url()
    {
        return $this->hasOne(Url::class, 'object_id', 'id')->where('object_type', 'manufacturer');
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
}
