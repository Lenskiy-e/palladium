<?php


namespace App\Services;

use Auth;
use Illuminate\Support\Facades\Redis;

class Product
{
    /**
     * @param int $id
     * Добавляет товар в избранное
     */
    public function addToFavorites(int $id) : void
    {
        $favorites = $this->getFavorites();

        if(!$this->inFavorite($id, $favorites))
        {
            $favorites[$id] = $id;

            $this->setFavorites($favorites);
        }
    }

    /**
     * Удаляет из избранных
     * @param int $id
     */
    public function removeFromFavorites(int $id) : void
    {

        if ($this->inFavorite($id)) {
            $favorites = $this->getFavorites();
            unset($favorites[$id]);

            if (Auth::check())
            {
                Auth::user()->favorites()->detach($id);
            }
            Redis::del('favorites');
            $this->setFavorites($favorites);
        }
    }

    /**
     * Проверяет есть ли товар в избранном
     * @param int $id
     * @param array $favorites
     * @return bool
     */
    public function inFavorite(int $id, array $favorites = []) : bool
    {
        $favorites = $favorites ?: $this->getFavorites();

        return in_array($id, $favorites, true);
    }


    /**
     * Получение списка избранных товаров
     * @return array
     */
    public function getFavorites(): array
    {
        $favorites = json_decode(Redis::get('favorites'), true) ?? [];

        if(Auth::check())
        {
            $user_favorites = Auth::user()->favorites()->get()->pluck('id')->toArray();
            foreach ($user_favorites as $user_favorite) {
                $favorites[$user_favorite] = $user_favorite;
            }
        }
        return $favorites;
    }

    /**
     * Для использования во вьюхах
     * @param int $id
     * @return bool
     */
    public static function favorite(int $id) : bool
    {
        $instance = new self();

        return $instance->inFavorite($id, $instance->getFavorites());
    }

    /**
     * Записывает айди товара в редиску
     * В случае, если юзер залогинен,
     * то в таблицу user_favorites
     * @param array $favorites
     */
    private function setFavorites(array $favorites) : void
    {
        Redis::set('favorites', json_encode($favorites));

        if(Auth::check())
        {
            $user = Auth::user();

            $user->favorites()->detach();
            $user->favorites()->attach($favorites);
        }
    }
}
