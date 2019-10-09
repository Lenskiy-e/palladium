<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;
use App\Traits\ActionsLogTrait;
use Illuminate\Http\Request;

class CategoryDescription extends Model
{
    use CrudTrait;
    use HasTranslations;
    use ActionsLogTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'category_description';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['parent_id','title','h1','description_top','description_bottom','meta_title','meta_description', 'depth', 'prefix'];
    protected $translatable = ['title','h1','description_top','description_bottom','meta_title','meta_description', 'prefix'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /*
    * Кнопки для просмотра
    * родительских / дочерних категорий
    */
    public function getChildrens($crud = false)
    {
        return '<a class="btn btn-xs btn-default" href="?category_id=' . $this->id . '" data-toggle="tooltip" title="Смотреть подкатегории"><i class="fa fa-search"></i> Подкатегории</a>';
    }

    public function getParent($crud = false)
    {
        if($this->parent()->get()[0]->parent_id){
            return '<a class="btn btn-xs btn-default" href="?category_id=' . $this->parent()->get()[0]->parent_id . '" data-toggle="tooltip" title="Смотреть подкатегории"><i class="fa fa-arrow-up"></i> На уровень выше</a>';
        }else{
            return '<a class="btn btn-xs btn-default" href="?depth=1" data-toggle="tooltip" title="Смотреть подкатегории"><i class="fa fa-arrow-up"></i> На уровень выше</a>';
        }
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function category(){

        return $this->hasOne(Category::class,'category_id','id');
    }

    public function productDescription(){
        return $this->hasMany(ProductDescription::class,'category_id');
    }

    public function attributes(){
      return $this->belongsToMany(Attributes::class,'attributes_category','attribute_id','category_id');
    }

    public function filters(){
      return $this->belongsToMany(Attributes::class,'attribute_category_filter','attribute_id','category_id')->orderBy('id');
    }

    public function parent(){
        return $this->belongsTo(CategoryDescription::class,'parent_id');
    }

    public function children(){
        return $this->hasMany(CategoryDescription::class,'parent_id');
    }

    public function categoryCost(){
        return $this->belongsToMany(CategoryCost::class,'category_cost_category','category_id','category_cost_id');
    }

    public function products(){
        return $this->belongsToMany(ProductDescription::class,'category_product','category_id','product_id');
    }

    public function activeProducts()
    {
        return $this->belongsToMany(ProductDescription::class,'category_product','category_id','product_id')
            ->where('edit_status', 3)
            ->whereHas('product', static function ($q) {
                $q->where('active', 1);
            });
    }

    public function url(){
        return $this->hasOne(Url::class, 'object_id', 'id')->where('object_type', 'category');
    }


    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeWhereHasAndWith($query, $relation, $constraint)
    {
        return $query
                    ->whereHas($relation, $constraint)
                    ->with([$relation => $constraint]);
    }

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

    public function setParentIdAttribute($value)
    {
        $this->attributes['parent_id'] = $value;
        $this->attributes['depth'] = 1;
        if($value)
        {
            $this->attributes['depth'] = $this->find($value)->depth + 1;
        }
    }
}
