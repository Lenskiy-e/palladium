<?php


namespace App\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User as Model;

class User
{


    /**
     * При вводе телефона/почты в форме заказа
     * Проверяет был ли зареган юзер
     * @param array $request
     * @return array
     * @throws \Throwable
     */
    public function checkUserInOrder(array $request) : array
    {
        try
        {
            $user_by_phone = $this->getUserByKey('phone', $request);
            $user_by_email = $this->getUserByKey('email', $request);
            $standard_response =
                [
                    'status'    => 1,
                    'message'   => 'Вы уже зарегистрированы, войдите чтобы получить бонусы'
                ];

            if(!$user_by_phone && !$user_by_email)
            {
                return [
                    'status'    => 0,
                    'message'   => ''
                ];
            }

            if($user_by_phone && $user_by_email && $user_by_phone->id !== $user_by_email->id)
            {
                return [
                    'status'  => 1,
                    'message' => 'Номер телефона и почта зарегистрированы на разных пользователей'
                ];
            }

            return $standard_response;
        }
        catch(\Throwable $th)
        {
            //TODO exception
            throw $th;
        }
    }


    /**
     * Поиск юзера для админки по нужному ключу
     * @param string $key
     * @param array $request
     * @return Model|null
     */
    public function getUserByKey(string $key, array $request) : ? Model
    {

        $user = new Model();

        try
        {
            if (array_key_exists($key, $request))
            {
                $response = $user->select('id')->where($key, $request[$key])->where('is_admin', 0)->firstOrFail();

                return $response;
            }
        }catch(ModelNotFoundException $th)
        {
            return null;
        }

    }
}
