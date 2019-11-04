<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AppController;
use App\Http\Controllers\PaymentController;
class Bill extends Model
{
    protected $fillable = ['name', 'info'];

    public function payments()
    {
        $shops_in = AppController::get_shops_by_module(PaymentController::$module_id);
        $query = $this->hasMany('App\Payment')->where(['is_deleted' => 0]);
        if ($shops_in) {
            $query->whereIn('shop_id', $shops_in);
        }
        return $query;
    }
}
