<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Backpack\CRUD\ModelTraits\SpatieTranslatable\HasTranslations;

class Sales extends Model
{
    use CrudTrait;
    use HasTranslations;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'sales';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = ['name','description','baner','type','discountType','discount','start','end'];
    // protected $hidden = [];
    protected $dates = ['start','end'];
    protected $translatable = ['name','description'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Добавляем сеты
     * @param $sale_id
     * @param $request
     */
    public function addSets($sale_id, $request) : void
    {
        $cur_set = Set::where('sales_id',$sale_id)->get();

        //удаляем сеты
        if($cur_set)
        {
            foreach ($cur_set as $cs)
            {
                $cs->product()->detach();
                $cs->delete();
            }
        }
        if($request->input('set'))
        {
            foreach ($request->input('set') as $sets)
            {
                $set = Set::create(['sales_id' => $sale_id, 'new_price' => $sets['new_price']]);
                $product_data = array();
                foreach ($sets['block'] as $block)
                {
                    $product_data[] = [
                        'product_id' => $block['product'],
                        'product_discount' => $block['discount'],
                        'discount_type' => $block['type']
                    ];
                }
                $set->product()->attach($product_data);
            }
        }
    }

    /**
     * Добавляем подарки
     * @param $sale_id
     * @param $request
     */
    public function addGifts(int $sale_id, Request $request) : void
    {
        Gift::where('sale_id',$sale_id)->delete();

        foreach ($request->input('gift_ar') as $gifts)
        {
            if(!empty($gifts['gift_id']))
            {
                Gift::create([
                    'product_id' => $gifts['product_id'],
                    'gift_product_id' => $gifts['gift_id'],
                    'sale_id' => $sale_id
                ]);
            }else {
                Gift::create([
                    'product_id' => $gifts['product_id'],
                    'image' => $this->loadImage($gifts['gift_image']),
                    'name' => $gifts['gift_name'],
                    'sale_id' => $sale_id
                ]);
            }
        }
    }


    /**
     * @param int $id
     * @param $request_products
     */
    public function attachRequestProducts(int $id, $request_products) : void
    {
        $model = self::find($id);
        $model->price_product()->detach();
        if(!empty($request_products))
        {
            $products = array();
            foreach ($request_products as $product)
            {
                $products[] = [
                    'product_id' => $product->product_id,
                    'new_price' => $product->new_price ?? 0
                ];
            }
            $model->price_product()->attach($products);
        }

    }

    public function loadImage($img){
        $disk = "uploads";
        $folder = "sales";
        $image_name = "";

        if(!$img){
            return $image_name;
        }
        // if a base64 was sent, store it in the db
        if (starts_with($img, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($img);
            // 1. Generate a filename.
            $filename = md5($img . time()) . '.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($folder . '/' . $filename, $image->stream());
            // 3. Assigning a path to image
            $image_name = $disk . '/' . $folder . '/' . $filename;
        }else{
            return $img;
        }
        return $image_name;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function category(){
        return $this->belongsToMany(CategoryDescription::class,'category_sale','sale_id','category_id');
    }

    public function gift(){
        return $this->belongsToMany(ProductDescription::class,'gifts_sale','sale_id','gift_id');
    }

    public function gifts(){
        return $this->hasMany(Gift::class,'sale_id');
    }

    public function manufacturer(){
        return $this->belongsToMany(Manufacturer::class,'manufacturer_sale','sale_id','manufacturer_id');
    }

    public function price_product(){
        return $this->belongsToMany(ProductDescription::class,'price_products_sale','sale_id','product_id')->withPivot('new_price');
    }

    public function product(){
        return $this->belongsToMany(ProductDescription::class,'product_sale','sale_id','product_id');
    }

    public function sets(){
        return $this->hasMany(Set::class);
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
    public function setBanerAttribute($value)
    {

            $attribute_name = "baner";
            $disk = "uploads";
            $folder = "sales";

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
