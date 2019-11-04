<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name'];

    public function create_values()
    {
        return $this->belongsToMany('App\AttributeValue', 'attribute_values', 'attribute_id', 'value');
    }
    public function values()
    {
        return $this->hasMany('App\AttributeValue');
    }
}
