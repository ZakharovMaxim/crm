<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockAction extends Model
{
    protected $fillable = ['notice', 'to_stock', 'from_stock', 'type'];

    public function products()
    {
        return $this->belongsToMany('App\Product', 'action_has_products', 'action_id', 'product_id')->withPivot(['count', 'purchase_price']);
    }
    public function to_stock()
    {
        return $this->belongsTo('App\Stock', 'to_stock');
    }
    public function from_stock()
    {
        return $this->belongsTo('App\Stock', 'from_stock');
    }
}
