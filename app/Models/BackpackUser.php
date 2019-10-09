<?php

namespace App\Models;

use App\User;
use Backpack\Base\app\Notifications\ResetPasswordNotification as ResetPasswordNotification;
use Tightenco\Parental\HasParent;
use Backpack\CRUD\CrudTrait;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class BackpackUser extends User
{
    use HasParent;
    use CrudTrait;
    use HasRoles;

    protected $table = 'users';

    protected $attributes = [
        'is_admin' => 1
    ];

    /**
     * Send the password reset notification.
     *
     * @param string $token
     *
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    // Relations
    public function products(){
        return $this->belongsToMany(ProductDescription::class,'manager_product','manager_id','product_id');
    }

    // Functions

    public function getManagerProducts($id){
        /*
        * Получаем id нужного пользователя
        * С него создаем объект класса BackpackUser
        * Выбираем все продукты, у которых статус
        * "Отредактировано" был выставлен с начала месяца
        * И статус "Опубликовано" был выставлен до 5 числа
        * Текущего месяца
        */
        $data = array();
        $plan_products = array();
        $overplan_products = array();
        $first = new Carbon('first day of this month');
        $last = new Carbon('last day of this month');
        $user = BackpackUser::where('id',$id)->get()->first();
        $products = $user->products()
        ->where('check_date','>=',$first->toDateString())
        ->where('publish_date', '<=',$last->toDateString());

        /*
        * Выбираем все продукты в границах плана менеджера
        * Сортируя по дате выставления статуса "Отредактировано"
        */
        $total_count = $products->get()->count();
        $plan_products = $products->limit(1)->orderBy('check_date', 'ASC')->get();
        $count_products = $plan_products->count();

        /*
        * Отнимаем от нужного плана кол-во товаров
        * Которые заполнил менеджер, чтобы
        * Понять на сколько выполнен план
        */
        $done_plan = 1 - $count_products;

        /*
        * Если count_products больше план, выбирем
        * Продукты, которые заполнены свыше плана
        */
        if ($done_plan <= 0) {
            $overplan_products = $products->skip(1)->get();
            $done_plan = 1;
        }

        /*
        * Формируем массив
        */
        $data = [
            'plan_products' => $plan_products,
            'overplan_products' => $overplan_products,
            'plan' => $done_plan,
            'total_count' => $total_count
        ];
        return $data;
    }

    // Список пользователей с ролью content manager
    public static function getManagers(){
        return \App\Models\BackpackUser::whereHas('roles', function($q){
             $q->where('name', 'content manager');
             })->get();
    }

    public function managers(){
        $managers = self::getManagers()->pluck('id')->toArray();
        return $managers;
    }

}
