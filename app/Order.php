<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AppController;

class Order extends Model
{
    protected $fillable = ['ttn', 'np_key', 'creator_id', 'manager_id', 'shop_id', 'user_comment', 'customer_fathername', 'customer_surname', 'customer_name', 'customer_phone', 'customer_email', 'payment_type_id', 'bill_id', 'delivery_id', 'delivery_payer', 'delivery_city', 'delivery_address', 'check_comment', 'roistat_visit_id', 'payment_source_id', 'payment_source_link', 'status', 'order_comment'];

    public function delivery() {
        return $this->belongsTo('App\Delivery');
    }
    public function shop() {
        return $this->belongsTo('App\Shop');
    }
    public function payment() {
        return $this->belongsTo('App\PaymentType', 'payment_type_id');
    }
    public function bill() {
        return $this->belongsTo('App\Bill');
    }
    public function creator() {
        return $this->belongsTo('App\User', 'creator_id');
    }
    public function manager() {
        return $this->belongsTo('App\User', 'manager_id');
    }
    public function payments() {
        // $categories = AppController::get_payment_states_shops(PaymentController::$module_id);
        $query = $this->hasMany('App\Payment')->where(['is_deleted' => 0]);
        // if ($categories) {
        //     $query->where(function ($query) use($categories) {
        //         foreach($categories as $shop_id => $cats)
        //         {
        //             $query->orWhere(function ($query) use($shop_id, $cats) {
        //                 $query->where('shop_id', '=', $shop_id);
        //                 $query->whereIn('payment_category_id', $cats);
        //             });
        //         }
        //     });
        // }
        return $query;
    }
    public function products() {
        return $this->belongsToMany('App\Product', 'order_products', 'order_id', 'product_id')->withPivot(['count', 'purchase_price', 'selling_price', 'price', 'discount']);
    }
}
