<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Support\Str;

class Aggregators extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'aggregators';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name','link','categories','slug','status','template'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'categories' => 'array',
    ];
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Добавление отношения товара
     * к агрегатору
     */
    public static function boot() : void
    {
        parent::boot();
        self::updating(function($model)
        {

            if(isset($model->attributes['categories']) && $model->attributes['categories'] !== "[null]")
            {
                $categories = json_decode($model->attributes['categories']);
                foreach($categories as $cat)
                {
                    $category = CategoryDescription::find($cat);
                    foreach($category->productDescription as $product)
                    {
                        $product->aggregators()->detach(['aggregator_id' => $model->id]);
                        $product->aggregators()->attach(['aggregator_id' => $model->id]);
                    }
                }
            }else{
                $model->product()->detach();
            }
        });

        self::deleting(function($model){
            if(isset($model->attributes['categories']))
            {
                $model->product()->detach();
            }
        });
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product(){
        return $this->belongsToMany(ProductDescription::class,'aggregator_product','aggregator_id','product_id');
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

    public function setLinkAttribute(){
        //Ссылка на прайс

        return $this->attributes['link'] = url('/feed/' . str_replace('-','_',Str::slug($this->attributes['name'])));
    }
    public function setSlugAttribute(){
        //Используется для названия метода в App\Models\Feed.php

        return $this->attributes['slug'] = str_replace('-','_',Str::slug($this->attributes['name']));
    }
}
