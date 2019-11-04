<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shop;
use JWTAuth;
use Illuminate\Support\Facades\DB;
class AppController extends Controller
{
  static public function has_module_access($module_id)
  {
    $allowed = self::get_allowed_modules();
    return in_array($module_id, $allowed);
  }
  static public function get_allowed_modules()
  {
    $user = JWTAuth::user();
    if ($user->is_admin) {
      $res = [];
      foreach(self::$modules as $module)
      {
        $res[] = $module['id'];
      }
      return $res;
    }

    $role = unserialize($user->role);
    if (!$role) {
      return [];
    }
    $allowed_modules = [];
    foreach($role['modules'] as $module)
    {
      $module_id = explode('.', $module)[1];
      if (!in_array($module_id, $allowed_modules)) {
        $allowed_modules[] = $module_id;
      }
    }
    return $allowed_modules;
  } 
  public function index ()
  {
    $allowed_modules = self::get_allowed_modules();
    $modules = [];
    foreach(self::$modules as $module)
    {
      if (in_array($module['id'], $allowed_modules)) {
        $modules[] = $module;
      }
    }
    return $modules;
  }
  static public function get_rule_catalogs_by_module ($module)
  {
    $user = JWTAuth::user();
    if ($user->is_admin) {
      return null;
    }

    $role = unserialize($user->role);
    if (!$role) {
      return [];
    }
    $allowed_shops = [];
    foreach($role['rules'] as $rule)
    {
      $shop_id = explode('.', $rule)[0];
      $module_id = explode('.', $rule)[1];
      $rule_id = explode('.', $rule)[2];
      if ($module_id == $module && $rule_id == 'r1') {
        $allowed_shops[] = $shop_id;
      }
    }
    $stocks = Shop::where(['is_deleted' => 0])->whereIn('id', $allowed_shops)->pluck('stock_id');
    return DB::table('stocks_folders')->whereIn('stock_id', $stocks)->pluck('folder_id')->toArray();
  }
  static public function get_stocks_by_module($module_id)
  {
    $shops_ids = self::get_shops_by_module($module_id);
    if ($shops_ids) {
      $shops = Shop::where(['is_deleted' => 0])->whereIn('id', $shops_ids)->get();
      $stocks = [];
      foreach($shops as $shop)
      {
        $stocks[] = $shop->stock_id;
      }
      return $stocks;
    }
    return null;
  }
  static public function get_payment_states_by_module($module_id, $shop_id)
  {
      $user = JWTAuth::user();
      if ($user->is_admin) return null;
      $role = unserialize($user->role);
      if (!$role) return [];
      
      $rules = $role['rules'];
      $res = [];
      foreach($rules as $rule)
      {
        $s_id = explode('.', $rule)[0];
        $mod_id = explode('.', $rule)[1];
        $rule_id = explode('.', $rule)[2];
        if ($rule_id[0] !== 'r' && $s_id == $shop_id && $module_id == $mod_id)
        {
          $res[] = $rule_id;
        }
      }
      return $res;
  }
  static public function get_payment_states_shops($module_id)
  {
      $user = JWTAuth::user();
      if ($user->is_admin) return null;
      $role = unserialize($user->role);
      if (!$role) return [];
      
      $rules = $role['rules'];
      $res = [];
      foreach($rules as $rule)
      {
        $shop_id = explode('.', $rule)[0];
        $mod_id = explode('.', $rule)[1];
        $rule_id = explode('.', $rule)[2];
        if ($rule_id[0] !== 'r' && $module_id == $mod_id)
        {
          if (isset($res[$shop_id])) {
            $res[$shop_id][] = $rule_id;
          } else {
            $res[$shop_id] = [$rule_id];

          }
        }
      }
      return $res;
  }
  static public function get_shops_by_module($module_id)
  {
    $user = JWTAuth::user();
    if ($user->is_admin) {
      return null;
    }
    $shops = [];
    $role = unserialize($user->role);
    if (!$role) return [];
    foreach($role['modules'] as $module)
    {
      $shop_id = explode('.', $module)[0];
      $mod_id = explode('.', $module)[1];
      if ($mod_id == $module_id && !in_array($shop_id, $shops)) {
        $shops[] = $shop_id;
      }
    }
    return $shops;
  }
  static public function get_allowed_catalogs () {
    $role = JWTAuth::user()->role;
    if (!$role) return null;
    $rules = unserialize($role)['rules'];
    $catalogs = [];
    foreach($rules as $rule)
    {
      $module_id = explode('.', $rule)[1];
      if ($module_id == 2) {
        $catalog_id =  explode('.', $rule)[2];
        if ($catalog_id[0] != 'r') {
          $catalogs[] = $catalog_id;
        }
      }
    }
    return $catalogs;
  }
  static public function get_all_modules()
  {
    $modules = [];
    foreach(self::$modules as $module)
    {
      if (!isset($module['private'])) {
        $rules = [];
        if (isset($module['rules'])) {
          $rules = array_merge($rules, $module['rules']);
        }
        if (isset($module['db_rules'])) {
          $rules = array_merge($rules, $module['db_rules']->get()->toArray());
        }
        $module['rules'] = $rules;
        $modules[] = $module;
      }
    }
    return $modules;
  }
  static public function setup () {
    self::$modules = [
      [
        'id' => 1,
        'name' => 'Магазины',
        'icon' => 'store',
        'to' => '/shops'
      ],
      [
        'id' => 2,
        'name' => 'Каталог',
        'icon' => 'folder-multiple-outline',
        'to' => '/catalogs',
        'rules' => [
          [
            'id' => 'r1',
            'name' => 'Закупочная цена'
          ]
        ],
        'db_rules' => 'App\Folder'::where(['is_deleted' => 0])->whereNull('parent_id')
      ],
      [
        'id' => 3,
        'name' => 'Пользователи',
        'icon' => 'account',
        'to' => '/users',
        'private' => true
      ],
      [
        'id' => 13,
        'name' => 'Плагины',
        'icon' => 'toolbox',
        'to' => '/plugins',
        'private' => true
      ],
      [
        'id' => 4,
        'name' => 'Склад',
        'icon' => 'package-variant-closed',
        'to' => '/stocks'
      ],
      [
        'id' => 5,
        'name' => 'Финансы',
        'icon' => 'cash-multiple',
        'to' => '/finance',
        'db_rules' => 'App\PaymentState'::where(['is_deleted' => 0])
      ],
      [
        'id' => 6,
        'name' => 'Заказы',
        'icon' => 'basket',
        'to' => '/orders'
      ],
      [
        'id' => 12,
        'name' => 'Клиенты',
        'icon' => 'account-card-details',
        'to' => '/clients'
      ],
      [
        'id' => 7,
        'name' => 'Отчеты по заказам',
        'icon' => 'file-chart',
        'to' => '/report-orders'
      ],
      [
        'id' => 8,
        'name' => 'Отчеты об остатках',
        'icon' => 'file-chart',
        'to' => '/report-stock'
      ],
      [
        'id' => 9,
        'name' => 'Отчеты по товарам',
        'icon' => 'file-chart',
        'to' => '/report-products'
      ],
      [
        'id' => 10,
        'name' => 'Отчеты по финансам',
        'icon' => 'file-chart',
        'to' => '/report-finance'
      ],
      [
        'id' => 11,
        'name' => 'Отчеты по финансам(по категориям)',
        'icon' => 'file-chart',
        'to' => '/report-finance-category?type=1'
      ]
    ];
  }
  static public $modules = [];
    
}

AppController::setup();