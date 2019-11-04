<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'sku', 'purchase_price', 'trademark_id', 'additional_info', 'min_count', 'selling_price', 'catalog_id', 'root_id', 'is_variation', 'parent_id', 'is_variant', 'description', 'is_deleted'];

    public function attributes()
    {
        return $this->belongsToMany('App\Attribute', 'product_attribute');
    }
    public function catalog()
    {
        return $this->belongsTo('App\Folder', 'catalog_id');
    }
    public function trademark()
    {
        return $this->belongsTo('App\Trademark');
    }
    public function attributesValues()
    {
        return $this->belongsToMany('App\AttributeValue', 'attribute_value_variation', 'product_id', 'attribute_values_id');
    }
    public function price_groups()
    {
        return $this->belongsToMany('App\PriceGroup', 'product_price_groups', 'product_id', 'price_group_id')->withPivot('value');
    }
    public function orders()
    {
        return $this->belongsToMany('App\Order', 'order_products', 'product_id', 'order_id')->withPivot('count','price', 'purchase_price');
    }
    public function variations()
    {
        return $this->hasMany('App\Product', 'parent_id', 'id')->where('products.is_deleted', '<>', '1')->where('products.is_variant', '1');
    }
    public function images()
    {
        return $this->belongsToMany('App\Image', 'products_images')->withPivot('order');
    }
    public function product_stats()
    {
        return $this->hasMany('App\ProductStats')->where(['is_deleted' => 0]);
    }
}
