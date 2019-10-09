<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class Attributes extends Model
{
    use CrudTrait;
    use HasTranslations;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'attributes';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['title','multiply','sufix','prefix'];
    protected $translatable = ['title'];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Заполняем атрибуты
     * @param Request $request
     */
    public function editParameters(Request $request)
    {

        $parameter_model = new Parameter();

        $active_params = [];
        $i = 0;

        foreach(json_decode($request->parameters, true) as $param)
        {
            $data = [
                'title' => json_encode(
                    [
                        'ua' => $param['ua'],
                        'ru' => $param['ru']
                    ]
                ),
                'image'         => $request->image[$i],
                'attribute_id'  => $request->id
            ];

            $id = $param['id'] ?? null;

            try
            {
                $parameter = $parameter_model->findOrFail($id);
                $parameter->update($data);

            }catch(ModelNotFoundException $th) {

                $id = $parameter_model->create($data)->id;
            }

            $i++;
            $active_params[] = $id;

        }
        $parameter_model->where('attribute_id', $request->id)->whereNotIn('id', $active_params)->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function parameter(){
      return $this->hasMany(Parameter::class,'attribute_id');
    }

    public function CategoryDescription(){
      return $this->belongsToMany(CategoryDescription::class,'attributes_category','category_id','attribute_id');
    }

    public function filter(){
      return $this->belongsToMany(CategoryDescription::class,'attribute_category_filter','category_id','attribute_id');
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
