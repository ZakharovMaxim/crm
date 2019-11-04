<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductStats extends Model
{
    protected $fillable = ['product_id', 'stock_id', 'in_stock', 'in_reserve', 'in_prepare'];

    public function stock()
    {
        return $this->belongsTo('App\Stock');
    }
}
