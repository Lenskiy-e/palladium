<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Backpack\CRUD\CrudTrait;
use Illuminate\Http\Request;
use App\Traits\ActionsLogTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;


class ProductDescription extends Model
{
    use CrudTrait;
    use HasTranslations;
    use ActionsLogTrait;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'product_description';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // protected $guarded = ['id'];
    protected $fillable = ['check_date', 'category_id', 'manufacturer_id', 'title', 'h1', 'short_description', 'description', 'meta_title', 'meta_description', 'markdown_reason', 'photos', 'edit_status', 'manager_id'];
    protected $translatable = ['title', 'h1', 'short_description', 'description', 'meta_title', 'meta_description', 'markdown_reason'];
    // protected $hidden = [];
    protected $dates = ['check_date'];
    protected $casts = [
        'photos' => 'array'
    ];
    // Используется для getParents
    private $breadcrumbs = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Присвоение товара менеджеру
     * @param Request $request
     */
    public static function appoint(Request $request)
    {
        $manager_id = $request->input('manager_id');

        $product = self::find($request->input('product_id'));

        $product->update(['manager_id' => $manager_id]);

        $product->manager()->wherePivot('check_date', null)->detach();

        $product->manager()->attach($manager_id);
    }

    /**
     * @param Request $request
     */
    public static function contentStatus(Request $request)
    {
        /*
        **
        * Если пользователь, изменивший товар - контент менеджер
        * Проверяем статус редактирования
        * Если статус "отредактирован" - в таблице manager_product
        * Выставляем параметр check_date в текущую метку времени
        * Если любой другой статус, то check_date в null
        * Это для отчетов
        **
        */
        if (backpack_user()->hasRole('content manager')) {
            $product_id = $request->input('id');
            $manager_id = backpack_user()->id;
            $product = self::find($product_id);
            // $product->manager()->wherePivot('manager_id',$manager_id)->detach();
            if ($request->input('edit_status') == 2) {
                if (!$product->manager()->where('manager_id', $manager_id)->first()->pivot->check_date) {
                    $product->manager()->updateExistingPivot($manager_id, ['check_date' => Carbon::now()]);
                }
            }

        }
        /*
        **
        * Если пользователь, изменивший товар - имеет права
        * Назначать контент менеджеров
        * Проверяем статус редактирования
        * Если статус "опубликовано" - в таблице manager_product
        * Выставляем параметр publish_date в текущую метку времени
        * Контент менеджер больше не может редактировать
        * Для отчетов
        **
        */
        if (backpack_user()->hasPermissionTo('appoint user to product')) {
            $product_id = $request->input('id');
            $product = self::find($request->input('id'));
            $manager_id = $product->manager_id;
            if ($request->input('edit_status') == 3) {
                $product->manager()->updateExistingPivot($manager_id, ['publish_date' => Carbon::now()]);
            }
        }
    }

    /*
    * Порядок заполнения похожих продуктов
    */
    public function addSilimar($request)
    {

        /*
        * Удаляем все записи, где присутствует данный товар
        * как в качестве product_id так и similar_id
        */
        $similar_table = DB::table('similar_products');
        $similar_table->where('product_id', $request->id)->orWhere('similar_id', $request->id)->delete();

        if ($request->similar && $request->similar_attribute) {
            foreach ($request->similar as $similar) {
                //Вносим похожие для продукта

                $similar_table->insert([
                    'product_id' => $request->id,
                    'similar_id' => $similar,
                    'attribute_id' => $request->similar_attribute
                ]);

                //Теперь обратная последовательность
                $similar_table->insert([
                    'product_id' => $similar,
                    'similar_id' => $request->id,
                    'attribute_id' => $request->similar_attribute
                ]);
            }
        }

    }

    /**
     * Рекурсивно получаем список
     * родительских категорий.
     * Записываем в breadcrumbs
     * @return array
     */
    public function breadcrumbs() : array
    {
        $current = $this->mainCategory()->get()->first();
        $this->breadcrumbs[] = [
            'name' => $current->title,
            'href' => url($current->url->url)
        ];

        while ($current->parent_id != 0) {
            $current = $current->parent()->get()->first();
            array_unshift($this->breadcrumbs, ['name' => $current->title, 'href' => url($current->url->url)]);
        }
        array_unshift($this->breadcrumbs, ['name' => 'Главная', 'href' => url('/')]);

        return $this->breadcrumbs;
    }


    /**
     * @param Request $request
     * @return Request
     */
    public static function addMain(Request $request) : Request
    {
        /**
         * Добавляем родительскую категорию
         * В список категорий
         */

        if ($request->category_id) {
            // если заполнены другие категории - мерджим с главной
            $categories = $request->category ?? [];

            $request->merge(['category' => array_merge([$request->category_id], $categories)]);
        }

        return $request;
    }


    /**
     * Возвращает массив цен
     * price - текущая цена (базовая / скидочная)
     * old_price - базовая цена
     * @return array
     */
    public function getPrice(): array
    {

        $price = $this->base_price;

        $sale_price = $this->product->sale_price;

        if ($sale_price) {
            $price_data = [
                'price' => $sale_price,
                'old_price' => $price
            ];
        } else {
            $price_data = [
                'price' => $price,
                'old_price' => null
            ];
        }

        return $price_data;
    }


    /**
     * @return string
     */
    public function getPriceType() : string
    {
        // все типы не скидочных цен на сайте
        $price_types = [
            0 => 'rrc_price',
            1 => 'base_price',
            2 => 'purchase_price'
        ];

        return $price_types[$this->product->price_mark];
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product()
    {

        return $this->hasOne(Product::class, 'product_id', 'id');
    }

    public function mainCategory()
    {
        return $this->belongsTo(CategoryDescription::class, 'category_id', 'id');
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, 'parameter_product', 'product_id', 'parameter_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'manager_id');
    }

    public function manager()
    {
        return $this->belongsToMany(BackpackUser::class, 'manager_product', 'product_id', 'manager_id')->withPivot('check_date', 'publish_date');
    }

    public function category()
    {
        return $this->belongsToMany(CategoryDescription::class, 'category_product', 'product_id', 'category_id');
    }

    public function aggregators()
    {
        return $this->belongsToMany(Aggregators::class, 'aggregator_product', 'product_id', 'aggregator_id');
    }

    public function sales()
    {
        return $this->belongsToMany(Sales::class, 'product_sale', 'product_id', 'sale_id');
    }

    public function similar()
    {
        return $this->belongsToMany(ProductDescription::class, 'similar_products', 'product_id', 'similar_id')->withPivot('attribute_id');
    }

    public function recommend()
    {
        return $this->belongsToMany(ProductDescription::class, 'product_recommends', 'product_id', 'recommend_id');
    }

    public function accessories()
    {
        return $this->belongsToMany(ProductDescription::class, 'accessories_product', 'product_id', 'accessory_id');
    }

    public function url()
    {
        return $this->hasOne(Url::class, 'object_id', 'id')->where('object_type', 'product');
    }

    public function inFavorite()
    {
        return $this->belongsToMany(User::class, 'user_favorites', 'product_id', 'user_id');
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
     * Возвращает префикс категории
     *
     */
    public function getNameAttribute()
    {
        $category = $this->mainCategory()->first();
        $titles = json_decode($this->attributes['title'], true);
        $locale = app()->getLocale();
        if(!isset($titles[$locale]))
        {
            $locale = config('app.fallback_locale');
        }
        return $category->prefix . ' ' . $titles[$locale];
    }

    public function getBasePriceAttribute()
    {
        //тип цены, в зависимости от price_mark в таблице products
        $price_type = $this->getPriceType();

        return $this->product->$price_type;
    }

    public function getActivePriceAttribute()
    {
        return $this->getPrice()['price'];
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */


}
