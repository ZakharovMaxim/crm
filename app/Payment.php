<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['shop_id', 'payment_category_id', 'bill_id', 'order_id', 'type', 'is_deleted', 'sum', 'date', 'comment'];

    public function category()
    {
        return $this->belongsTo('App\PaymentState', 'payment_category_id');
    }
    public function bill()
    {
        return $this->belongsTo('App\Bill', 'bill_id');
    }
    public function shop()
    {
        return $this->belongsTo('App\Shop');
    }
}
