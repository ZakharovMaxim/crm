<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SMS extends Model
{
    protected $connection = 'mysql2';
    protected $fillable = ['number', 'message', 'sign'];
}
