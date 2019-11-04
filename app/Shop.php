<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use JWTAuth;
use PaymentState;
use App\Http\Controllers\AppController;

class Shop extends Model
{
    protected $fillable = ['name', 'novaposhta_id', 'roistat_id', 'turbosms_id', 'stock_id'];

    public function stock()
    {
        return $this->belongsTo('App\Stock');
    }
    public function getPaymentCategories($categories = [], $module_id)
    {
        $allowed = AppController::get_payment_states_by_module($module_id, $this->id);
        $res = [];
        if ($allowed) {
            foreach($allowed as $state_id)
            {
                foreach($categories as $cat)
                {
                    if ($cat->id == $state_id)
                    {
                        $res[] = $cat;
                    }
                }
            }
        } else {
            return $categories;
        }
        return $res;
    }
    public static function query($module_id = 0)
    {
        $query = self::where(['is_deleted' => 0]);
        if ($module_id) {
            $shop_ids = AppController::get_shops_by_module($module_id);
            if ($shop_ids) $query->whereIn('id', $shop_ids);
        }
        return $query;
    }
}
