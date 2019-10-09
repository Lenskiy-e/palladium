<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Promo extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'promocodes';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'code','reusable','type',
        'discount','expired_at',
        'active','used', 'description',
        'start_at', 'minimum_amount',
        'unique'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Проверяем действует ли на товар промокод
     * @param ProductDescription $product
     * @return bool
     */
    public function checkProductPromo(ProductDescription $product) : bool
    {
        $check = 0;

        $promo_product = $this->product()->count();
        $promo_category = $this->category()->count();
        $promo_manufacturer = $this->manufacturer()->count();

        if(!$promo_category && !$promo_product && !$promo_manufacturer)
        {
            return true;
        }

        if ($this->category()->count())
        {
            $check += in_array($product->mainCategory->id, $this->category()->get()->pluck('id')->toArray(), true);
        }

        if ($this->product()->count())
        {
            $check += in_array($product->id, $this->product()->get()->pluck('id')->toArray(), true);
        }

        if ($this->manufacturer()->count())
        {
            $check += in_array($product->manufacturer->id, $this->manufacturer()->get()->pluck('id')->toArray(), true);
        }

        return $check ? true : false;
    }

    /**
     * @param Order $order
     * @return bool
     * Проверяет, использовал ли юзер промокод
     * False - промокод юзером не юзался
     * или может быть использован многократно
     * True - промокод уже был использован
     */
    public function checkUser(Order $order)
    {
        if($this->reusable)
        {
            return false;
        }

        $status = $order->user()->whereHas('promocode', function($query){
            $query->where('id', $this->id);
        })->with('promocode')->get()->isNotEmpty();

        return $status;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function category()
    {
        return $this->belongsToMany(CategoryDescription::class,'category_promo', 'promo_id','category_id');
    }

    public function manufacturer()
    {
        return $this->belongsToMany(Manufacturer::class,'manufacturer_promo', 'promo_id','manufacturer_id');
    }

    public function product()
    {
        return $this->belongsToMany(ProductDescription::class,'product_promo', 'promo_id','product_id');
    }

    public function user()
    {
        return $this->belongsToMany(User::class,'promo_user', 'promo_id','user_id');
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
