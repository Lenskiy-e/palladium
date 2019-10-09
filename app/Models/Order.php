<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Auth;
use Illuminate\Support\Facades\Redis;

class Order extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'orders';
    protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'user_id', 'address_id', 'coupon',
        'getter', 'getter_first_name', 'getter_last_name',
        'message', 'payment_method', 'complete',
        'total_amount', 'total_count', 'phone', 'promo'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    public function getOrder(int $id = null): ?Order
    {

        if (!$id) {
            $id = Redis::get('order_id');
        }

        return $this->findOrFail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function product()
    {
        return $this->belongsToMany(ProductDescription::class, 'order_product', 'order_id', 'product_id')->withPivot('cost', 'count');
    }

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function promocode()
    {
        return $this->belongsTo(Promo::class, 'promo');
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

    public function getProductsSumAttribute() : float
    {
        $products = $this->product()->get();
        $sum = 0;

        if($products->count())
        {
            foreach ($products as $product) {
                $price = $product->getPrice();

                $sum += $price['price'];
            }
        }

        return $sum;
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
