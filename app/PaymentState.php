<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentState extends Model
{
    protected $fillable = ['name', 'type', 'parent_id'];
}
