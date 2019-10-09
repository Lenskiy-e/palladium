<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ProductDescription;
use App\Models\Product;

class SaleController extends Controller
{


    /**
     * Типы скидок для админки
     * @param Request $request
     * @return array
     */
    public function getType(Request $request) : array
    {
        //формируем массив типов скидок
        $fields = [
            'content' => [
                [
                    'value' => 'gift',
                    'text' => 'Подарочные'
                ],
                [
                    'value' => 'discounts',
                    'text' => 'Скидочные'
                ],
                [
                    'value' => 'complect',
                    'text' => 'Комплекты'
                ],
                [
                    'value' => 'shipping',
                    'text' => 'Бесплатная доставка'
                ],
                [
                    'value' => 'credit',
                    'text' => 'Кредитная'
                ]
            ],
            'empty' => []
        ];
        return $fields[$request->input('type')];
    }
}
