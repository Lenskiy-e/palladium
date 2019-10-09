<?php

namespace App;

use App\Models\Promo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use CrudTrait;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'is_admin','phone'
    ];

    protected $attributes = [
        'is_admin' => 0
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


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

    public function profile()
    {
        return $this->hasOne(Models\Profile::class);
    }

    public function marketing()
    {
        return $this->hasOne(Models\MarketingProfile::class);
    }

    public function children()
    {
        return $this->hasMany(Models\Children::class);
    }

    public function hobbies()
    {
        return $this->belongsToMany(Models\Hobbies::class);
    }

    public function order()
    {
        return $this->hasMany(Models\Order::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Models\ProductDescription::class, 'user_favorites', 'user_id','product_id');
    }

    public function promocode()
    {
        return $this->belongsToMany(Promo::class,'promo_user','user_id', 'promo_id');
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
