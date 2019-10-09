<?php

namespace App\Imports;

use App\Models\Sales;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductSalesImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    private $model;

    public function __construct($sale_id){
        $this->model = Sales::find($sale_id);
        $this->model->price_product()->detach();
    }

    public function model(array $row)
    {
        $this->model->price_product()->attach([ $row[0] => ['new_price' => $row[2] ?? 0] ]);
    }
}
