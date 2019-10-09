<?php

namespace App\Http\Controllers;

use App\Models\CategoryDescription;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\ProductDescription;
use App\Models\Profile;
use App\Models\SearchableParameter;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AutocompleteController extends Controller
{
    public function getCity(Request $request)
    {

    }

    public function getStreet(Request $request)
    {

    }

    public function getNP(Request $request)
    {

    }

    /**
     * Поиск товаров по назваию
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchProducts(Request $request) : LengthAwarePaginator
    {
        $word = $request->input('q');
        $upper_word = $this->mb_ucfirst($word);

        $products = ProductDescription
                                    ::where('title->' . app()->getLocale(), 'like', '%'.$word.'%')
                                    ->orWhere('title->' . app()->getLocale(), 'like', '%'.$upper_word.'%')
                                    ->paginate(10);
        return $products;
    }

    /**
     * Поиск товаров для таблицы
     * в админ панеле
     * @param Request $request
     * @return array
     */
    public function searchProductsTable(Request $request) : array
    {

        $word = $request->input('q');
        $upper_word = $this->mb_ucfirst($word);

        $data = [];

        $products = ProductDescription
                                    ::where('title->' . app()->getLocale(), 'like', '%'.$word.'%')
                                    ->orWhere('title->' . app()->getLocale(), 'like', '%'.$upper_word.'%')
                                    ->get();

        if($products->count())
        {
            foreach ($products as $product) {
                $data[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->title,
                    'product_price' => $product->getPrice()['price']
                ];
            }
        }
        return $data;
    }


    /**
     * @param $id
     * @return Collection
     */
    public function showProduct($id) : Collection
    {
        return ProductDescription::find($id);
    }


    /**
     * Поиск категории для админки
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchCategory(Request $request) : LengthAwarePaginator
    {
        // Поиск категорий по имени
        $word = $request->input('q');
        $upper_word = $this->mb_ucfirst($word);

        $categories = CategoryDescription
                        ::where('title->' . app()->getLocale(), 'like', '%'.$word.'%')
                        ->orWhere('title->' . app()->getLocale(), 'like', '%'.$upper_word.'%')
                        ->paginate(10);
        return $categories;
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchManufacturer(Request $request) : LengthAwarePaginator
    {
        // Поиск категорий по имени
        $word = $request->input('q');
        $upper_word = $this->mb_ucfirst($word);

        $manufacturers = Manufacturer
                                    ::where('title', 'like', '%'.$word.'%')
                                    ->orWhere('title', 'like', '%'.$upper_word.'%')
                                    ->paginate(10);

        return $manufacturers;
    }


    /**
     * @param $id
     * @return Collection
     */
    public function showCategory($id) : Collection
    {
        return CategoryDescription::find($id);
    }


    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function searchParameter(Request $request) : LengthAwarePaginator
    {
        // Поиск параметров по имени
        $word = $request->input('q');
        $upper_word = $this->mb_ucfirst($word);

        $parameters = SearchableParameter
                                        ::where('title->' . app()->getLocale(), 'like', '%'.$word.'%')
                                        ->orWhere('title->' . app()->getLocale(), 'like', '%'.$upper_word.'%')
                                        ->paginate(10);

        // Добавляем к параметру навзвание его спецификации
        foreach ($parameters as $param) {
            $param->title =  $param->title . ' : ' . $param->attribute->title;
        }
        return $parameters;
    }


    /**
     * @param Request $request
     * @return Collection
     */
    public function getUserByLastName(Request $request) : Collection
    {
        $profile = new Profile();
        $query = $request->get('term');

        return $profile->where('last_name', 'like', $query . '%')->get()->pluck('last_name', 'user_id');
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function getUserByPhone(Request $request) : Collection
    {
        $user_obj = new User();

        $query = $request->get('term');

        $users = $user_obj->select('id','phone')->where('phone', 'like',  $query . '%')->get()->pluck('phone', 'id');

        return $users;
    }

    /**
     * @param Request $request
     * @return Collection
     */
    public function getCategoryByTitle(Request $request) : Collection
    {
        $query = $request->get('term');

        $results = CategoryDescription
                                    ::where('title->' . app()->getLocale(), 'like',  '%' . mb_ucfirst($query) . '%')
                                    ->get()->pluck('title', 'id');

        return $results;
    }

    /**
     * @param string $text
     * @return string
     */
    private function mb_ucfirst(string $text) : string
    {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }
}
