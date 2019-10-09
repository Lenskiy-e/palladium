<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Accessories extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'accessories';
    protected $primaryKey = 'id';
    public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['category_id','product_id','parameter_id','name'];
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

    public function category(){
        return $this->belongsTo(CategoryDescription::class);
    }

    public function product(){
        return $this->belongsTo(ProductDescription::class);
    }

    public function parameter(){
        return $this->belongsTo(Parameter::class);
    }

    // категории, к которым относится аксессуар
    public function accessoryToCategory(){
        return $this->belongsToMany(CategoryDescription::class,'accessories_categories', 'accessory_id','category_id');
    }

    // товары, к которым относится аксессуар
    public function accessoryToProducts(){
        return $this->belongsToMany(ProductDescription::class,'accessories_product', 'accessory_id','product_id');
    }

    // параметры, к которым относится аксессуар
    public function accessoryToParameters(){
        return $this->belongsToMany(SearchableParameter::class,'accessories_parameters', 'accessory_id','parameter_id');
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

    /*
    * Первые два муратора сделаны,
    * чтобы при вызове третьего массив attributes был не пустой
    * Последний метод формирует имя аксессуара, в зависимости от
    * Того категория это или один продукт
    */
    public function setProductIdAttribute($value)
    {
        if($value){
            $this->attributes['product_id'] = $value;
        }
    }

    public function setCategoryIdAttribute($value)
    {
        if($value){
            $this->attributes['category_id'] = $value;
        }
    }

    public function setNameAttribute($value)
    {
        if($this->product){
            $this->attributes['name'] = $this->product->title;
        }elseif($this->category){
            $this->attributes['name'] = $this->category->title;
        }
    }
}
