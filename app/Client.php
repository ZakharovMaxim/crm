<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = ['name', 'surname', 'phone', 'fathername'];

    public function orders()
    {
        return $this->hasMany('App\Order', 'customer_phone', 'phone');
    }
}
