<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Product;
use App\Shop;
use App\Delivery;
use App\Bill;
use App\PaymentType;
use App\PaymentState;
use App\Payment;
use App\Plugin;
use App\Client;
use App\SMS;
use App\ProductStats;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\GetTTNStatusesRequest;
use App\Http\Requests\StoreTTNRequest;
use App\Http\Controllers\AppController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PluginController;
use JWTAuth;
use DateTime;
use PDO;

class OrderController extends Controller
{
    static private $module_id = 6;
    static private $module_id_report = 7;
    // dont change ids of existing statuses
    static public function getStatuses() {
        $result = [];
        foreach(self::$statuses as $key => $status) {
            $result[] = [
                    'id' => $key,
                    'name' => $status['label'],
                    'to_status' => isset($status['to_status'])
                        ? $status['to_status']
                        : null
                ];
        }
        return $result;
    }
    static public function getStatuseToStatus($id) {
        $status = self::$statuses[$id];
        return isset($status['to_status']) ? $status['to_status'] : null;
    }
    // to prevent stock issues dont use editable and to_status options in the same instance
    static public function setup () {
        self::$statuses = [
            1 => [
                'slug' => 'new',
                'label' => 'Новые',
                'icon' => 'basket',
                'type' => 'is-info',
                'statuses' => [
                    ['id' => 2, 'label' => 'Принять', 'type' => 'is-success']
                ],
                'editable' => true
            ],
            2 => [
                'slug' => 'accepted',
                'label' => 'В обработке',
                'icon' => 'magnify',
                'statuses' => [
                    ['id' => 7, 'label' => 'Подтвердить', 'type' => 'is-success'],
                    ['id' => 3, 'label' => 'Перезвонить', 'type' => 'is-info'],
                    ['id' => 4, 'label' => 'Ожидают оплаты', 'type' => 'is-info'],
                    ['id' => 5, 'label' => 'Ожидают закупки', 'type' => 'is-info'],
                    ['id' => 6, 'label' => 'Ожидают поступления', 'type' => 'is-info'],
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary'],
                    ['id' => 15, 'label' => 'Отменить', 'type' => 'is-link']
                ],
                'editable' => true
            ],
            3 => [
                'slug' => 'callback',
                'label' => 'Перезвонить',
                'icon' => 'phone',
                'is_sub' => true,
                'statuses' => [
                    ['id' => 7, 'label' => 'Подтвердить', 'type' => 'is-success'],
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-info'],
                    ['id' => 4, 'label' => 'Ожидают оплаты', 'type' => 'is-info'],
                    ['id' => 5, 'label' => 'Ожидают закупки', 'type' => 'is-info'],
                    ['id' => 6, 'label' => 'Ожидают поступления', 'type' => 'is-info'],
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary'],
                    ['id' => 15, 'label' => 'Отменить', 'type' => 'is-link']
                ],
                'editable' => true
            ],
            4 => [
                'slug' => 'waitforpay',
                'label' => 'Ожидают оплаты',
                'icon' => 'cash',
                'is_sub' => true,
                'statuses' => [
                    ['id' => 7, 'label' => 'Подтвердить', 'type' => 'is-success'],
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-info'],
                    ['id' => 3, 'label' => 'Перезвонить', 'type' => 'is-info'],
                    ['id' => 5, 'label' => 'Ожидают закупки', 'type' => 'is-info'],
                    ['id' => 6, 'label' => 'Ожидают поступления', 'type' => 'is-info'],
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary'],
                    ['id' => 15, 'label' => 'Отменить', 'type' => 'is-link']
                ],
                'editable' => true,
                'validator' => function ($order) {
                    if (!$order->bill_id) {
                        return 'Не указан счет';
                    }
                    return null;
                }
            ],
            5 => [
                'slug' => 'waitforpurchase',
                'label' => 'Ожидают закупки',
                'icon' => 'magnify',
                'is_sub' => true,
                'statuses' => [
                    ['id' => 7, 'label' => 'Подтвердить', 'type' => 'is-success'],
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-info'],
                    ['id' => 3, 'label' => 'Перезвонить', 'type' => 'is-info'],
                    ['id' => 4, 'label' => 'Ожидают оплаты', 'type' => 'is-info'],
                    ['id' => 6, 'label' => 'Ожидают поступления', 'type' => 'is-info'],
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary'],
                    ['id' => 15, 'label' => 'Отменить', 'type' => 'is-link']
                ],
                'editable' => true
            ],
            6 => [
                'slug' => 'waitforstock',
                'label' => 'Ожидают поступления',
                'icon' => 'magnify',
                'is_sub' => true,
                'statuses' => [
                    ['id' => 7, 'label' => 'Подтвердить', 'type' => 'is-success'],
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-info'],
                    ['id' => 3, 'label' => 'Перезвонить', 'type' => 'is-info'],
                    ['id' => 5, 'label' => 'Ожидают закупки', 'type' => 'is-info'],
                    ['id' => 4, 'label' => 'Ожидают оплаты', 'type' => 'is-info'],
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary'],
                    ['id' => 15, 'label' => 'Отменить', 'type' => 'is-link']
                ],
                'editable' => true
    
            ],
            7 => [
                'slug' => 'approved',
                'label' => 'Подтвержденные / На упаковке',
                'icon' => 'magnify',
                'type' => 'is-success',
                'statuses' => [
                    ['id' => 8, 'label' => 'Упакован', 'type' => 'is-info'],
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-default'],
                    ['id' => 15, 'label' => 'Отменить', 'type' => 'is-link']
                ],
                'to_status' => 'in_reserve'
            ],
            8 => [
                'slug' => 'packed',
                'label' => 'Готовые к отгрузке / Упакованные',
                'icon' => 'magnify',
                'statuses' => [
                    ['id' => 9, 'label' => 'Отправлен', 'type' => 'is-info'],
                    ['id' => 10, 'label' => 'На реализации', 'type' => 'is-info'],
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-default'],
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary']
                ],
                'to_status' => 'in_package'
            ],
            9 => [
                'slug' => 'shiped',
                'label' => 'Отправленные',
                'icon' => 'magnify',
                'statuses' => [
                    ['id' => 13, 'label' => 'Выполнен', 'type' => 'is-primary'],
                    ['id' => 12, 'label' => 'На реализации', 'type' => 'is-info'],
                    ['id' => 10, 'label' => 'Возврат', 'type' => 'is-info'],
                    ['id' => 11, 'label' => 'Возврат средств', 'type' => 'is-info'],
                    ['id' => 8, 'label' => 'Назад в упакованные', 'type' => 'is-default'],
                ],
                'to_status' => 'in_delivery',
                'after_save' => function ($order) {
                    if ($order->bill_id && $order->np_key && $order->ttn) {
                        $ttns = self::get_orders_statuses_list_np([
                            [
                                'key' => $order->np_key,
                                'ttn' => $order->ttn
                            ]
                        ]);
                        if (isset($ttns[0])) {
                            $payment = Payment::create([
                                'shop_id' => $order->shop_id,
                                'payment_category_id' => 2,
                                'order_id' =>$order->id,
                                'bill_id' =>$order->bill_id,
                                'sum' => $ttns[0]['cost'],
                                'type' => '2'
                            ]);
                        }
                    }
                //     if (!$order->customer_phone) return 'Не указан номер телефона, смс не было отправлено';
                //     if (!$order->bill_id) return 'Не указан счет для оплаты, смс не было отправлено';
                //     if (!$order->bill->info) return 'Не указано описание счета, смс не было отправлено';
                //     $number = $order->customer_phone;
                //     $message = $order->bill->info;
                //     $sign = 'MINIMAL';
                //     $DB = array(
                //         'Port' => 3306,
                //         'Host' => '94.249.146.189',
                //         'User' => 'Test',
                //         'Pass' => 'password',
                //         'Name' => 'users'
                //     );
                //     $DB['DSN'] = "mysql:host={$DB['Host']};port={$DB['Port']};dbname={$DB['Name']};charset=utf8";
                //     $res = 'nope';
                //     try
                //     {
                //         $PDO = new PDO($DB['DSN'], $DB['User'], $DB['Pass']);
                //         $PDO->exec("SET NAMES 'utf8'");
                //         $PDO->exec("SET CHARACTER SET 'utf8'");
                //         $sql = "INSERT INTO `Test` SET `sign`='$sign', `number`='$number', `message`='$message'";
                //         $res = $PDO->prepare($sql);
                //         // $res->execute();
                //     }
				// catch(PDOException $Exception){}
                //     return null;
                }
            ],
            10 => [
                'slug' => 'waitforreturn',
                'label' => 'Ожидают возврата посылки',
                'icon' => 'magnify',
                'is_sub' => true,
                'statuses' => [
                    ['id' => 13, 'label' => 'Возврат совершен', 'type' => 'is-primary'],
                    ['id' => 11, 'label' => 'Возврат средств', 'type' => 'is-info'],
                    ['id' => 9, 'label' => 'В отправленные', 'type' => 'is-default'],
                ]
            ],
            11 => [
                'slug' => 'cashback',
                'label' => 'Ожидают возврата средств',
                'icon' => 'magnify',
                'is_sub' => true,
                'statuses' => [
                    ['id' => 13, 'label' => 'Возврат совершен', 'type' => 'is-primary'],
                    ['id' => 9, 'label' => 'В отправленные', 'type' => 'is-default'],
                ]
            ],
            12 => [
                'slug' => 'realization',
                'label' => 'На реализации',
                'icon' => 'magnify'
            ],
            13 => [
                'slug' => 'completed',
                'label' => 'Выполненные',
                'icon' => 'magnify',
                'type' => 'is-success',
                'statuses' => [
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-default']
                ],
                'to_status' => 'in_sold',
                'before_save' => function ($order) {
                    if ($order->customer_phone && $order->customer_name && $order->customer_surname) {
                        $client = Client::updateOrCreate(['phone' => $order->customer_phone], [
                            'name' => $order->customer_name,
                            'surname' => $order->customer_surname,
                            'fathername' => $order->customer_fathername,
                            'phone' => $order->customer_phone
                        ]);
                        if ($client->id) $order->customer_id = $client->id;
                        return $order;
                    }
                }
            ],
            14 => [
                'slug' => 'returned',
                'label' => 'Возвраты',
                'icon' => 'magnify',
                'type' => 'is-danger',
                'statuses' => [
                    ['id' => 2, 'label' => 'В принятые', 'type' => 'is-default'],
                ]
            ],
            15 => [
                'slug' => 'canceled',
                'label' => 'Отмененные',
                'icon' => 'magnify',
                'type' => 'is-danger',
                'statuses' => [
                    ['id' => 2, 'label' => 'Активировать', 'type' => 'is-default'],
                ]
            ]
        ];
    }
    static public function getStatus($id)
    {
        return isset(self::$statuses[$id]) ? self::$statuses[$id] : null;
    }
    static private $statuses = array();
    private $channels = [
        'default' => [
            ['id' => null, 'title' => '---'],
            ['id' => 1, 'title' => 'Прямой канал']
        ],
        'Звонки' => [
            ['id' => 2, 'title' => 'Входящий звонок'],
            ['id' => 3, 'title' => 'Исходящий звонок']
        ],
        'Поисковые системы' => [
            ['id' => 4, 'title' => 'Google поиск (Organic)'],
            ['id' => 5, 'title' => 'Яндекс поиск (Organic)']
        ],
        'Социальные сети' => [
            ['id' => 6, 'title' => 'Instagram'],
            ['id' => 7, 'title' => 'Facebook'],
            ['id' => 8, 'title' => 'ВКонтакте'],
            ['id' => 9, 'title' => 'Youtube']
        ],
        'Мессенджеры' => [
            ['id' => 10, 'title' => 'WhatsApp'],
            ['id' => 11, 'title' => 'Viber'],
            ['id' => 12, 'title' => 'Telegram'],
            ['id' => 13, 'title' => 'Skype']
        ],
        'Рассылки' => [
            ['id' => 14, 'title' => 'Cмс рассылки'],
            ['id' => 15, 'title' => 'Email рассылки'],
        ],
        'Рекламные площадки' => [
            ['id' => 16, 'title' => 'Реклама в Google Adwords'],
            ['id' => 17, 'title' => 'Реклама в Яндекс Директ'],
            ['id' => 18, 'title' => 'Реклама в Instagram'],
            ['id' => 19, 'title' => 'Реклама в Facebook'],
            ['id' => 20, 'title' => 'Реклама во ВКонтакте'],
        ],
        'Другие каналы' => [
            ['id' => 21, 'title' => 'Другой канал']
        ],
    ];
    public function stats (Request $request) {
        return 'ok';
    }
    public function info (Request $request) {
        $shopId = $request->query('shop');
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        
        $stats = Order::where(function ($query) use ($shopId, $date_from, $date_to) {
            $allowed_shops_ids = AppController::get_shops_by_module(self::$module_id);
            if ($allowed_shops_ids) $query->whereIn('shop_id', $allowed_shops_ids);
            if ($shopId) {
                $query->where(['shop_id' => $shopId]);
            }

        // [date_from, date_to => Всего поступило - по дате создания
        // Выполненные, отмененные - по дате приведения
        // Остальные без даты

        $query->where(function ($query) use ($date_from, $date_to){
            $query->where(function ($query) use($date_from, $date_to) {
                if ($date_from) {
                    $query->whereDate('status_updated_at', '>=', $date_from);
                    $query->whereIn('status', [13, 15]);
                }
                if ($date_to) {
                    $query->whereDate('status_updated_at', '<=', $date_to);
                    
                }
                $query->whereIn('status', [13, 15]);
            });
            $query->orWhere(function ($query) use($date_from, $date_to) {
                $query->whereNotIn('status', [13, 15]);
            });
            
        });
                
        })->groupBy('status')->selectRaw('count(*) as count, status')
        ->pluck('count', 'status');
        $statuses = [];
        foreach(self::$statuses as $key => $status) {
            $count = isset($stats[$key]) ? $stats[$key] : 0;
            $statuses[$key] = [
                'slug' => $status['slug'],
                'id' => $key,
                'label' => $status['label'],
                'icon' => $status['icon'],
                'is_sub' => isset($status['is_sub']),
                'type' => isset($status['type']) ? $status['type'] : '',
                'count' => $count
            ];
        }
        $stats = $statuses;
        $query = Order::whereNotIn('status', [13, 15]);
        if ($date_from) {
            $query->whereDate('created_at', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('created_at', '<=', $date_to);
        }
        $stats['total'] = $query->count();
        return ['shops' => Shop::query(self::$module_id)->get(), 'stats' => $stats];
    }
    private function get_index(Request $request)
    {
        $per_page = $request->query('per_page');
        $status_slug = $request->query('status');
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        $created_at_from = $request->query('created_at_from');
        $created_at_to = $request->query('created_at_to');
        $status_updated_at_from = $request->query('status_updated_at_from');
        $status_updated_at_to = $request->query('status_updated_at_to');
        $per_page = isset($per_page) ? $per_page : 20;
        $page = $request->query('page') ? $request->query('page') : 1;
        $shop = $request->query('shop');
        $shops = $request->query('shops');
        $bills = $request->query('bills');
        $channels = $request->query('channels');
        $statuses = $request->query('statuses');
        $deliveries = $request->query('deliveries');
        $payments = $request->query('payments');
        $queryString = $request->query('query');
        $name = $request->query('name') ? $request->query('name') : 'id';
        $orderBy = $request->query('order_by') ? $request->query('order_by') : 'created_at';
        $direction = $request->query('order_direction') ? $request->query('order_direction') : 'desc';
        $chart = $request->query('chart');
        $report = $request->query('report');
        $export = $request->query('export');
        $query = Order::whereNotNull('id');
        $module_id = $report ? self::$module_id_report : self::$module_id;
        $allowed_shops_ids = AppController::get_shops_by_module($module_id);
        $product_id = $request->query('product_id');
        if ($allowed_shops_ids) $query->whereIn('shop_id', $allowed_shops_ids);
        $query->where(function ($query) use ($date_from, $date_to){
            $query->where(function ($query) use($date_from, $date_to) {
                if ($date_from) {
                    $query->whereDate('status_updated_at', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('status_updated_at', '<=', $date_to);
                }
                $query->whereIn('status', [13, 15]);
            });
            $query->orWhere(function ($query) use($date_from, $date_to) {
                $query->whereNotIn('status', [13, 15]);
            });
            
        });
        
        if ($shop) {
            $query->where(['shop_id' => $shop]);
        }
        if ($queryString) {
            if ($name == 'customer_name') {
                $query->where(DB::raw("CONCAT(`customer_name`, ' ', `customer_surname`)"), 'like', '%'.$queryString.'%');
            } else {
                $query->where($name, 'like', '%'.$queryString.'%');

            }
        }
        if ($status_slug) {
            $status_id;
            foreach(self::$statuses as $key => $status) {
                if ($status['slug'] == $status_slug) {
                    $status_id = $key;
                }
            }
            if (isset($status_id)) {
                $query->where('status', $status_id);
            } 
        } else {
            if ($date_from) {
                $query->whereDate('created_at', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('created_at', '<=', $date_to);
            }
        }
        if ($created_at_from) {
            $query->whereDate('created_at', '>=', $created_at_from);
        }
        if ($created_at_to) {
            $query->whereDate('created_at', '<=', $created_at_to);
        }
        if ($status_updated_at_from) {
            $query->whereDate('status_updated_at', '>=', $status_updated_at_from);
        }
        if ($status_updated_at_to) {
            $query->whereDate('status_updated_at', '<=', $status_updated_at_to);
        }
        $stats = [];
        if ($page == 1 && !$report) {
            $statsQuery = clone $query;
            $chartQuery = clone $query;
            $statsDB = Payment::where(['is_deleted' => 0])->whereIn('order_id', $statsQuery->select('id'))->groupBy('type')
                ->selectRaw('sum(`sum`) as sum, type')
                ->pluck('sum','type');
            if (isset($statsDB['1'])) $stats['income'] = $statsDB['1'];
            if (isset($statsDB['2'])) $stats['outcome'] = $statsDB['2'];
            $orders = $statsQuery->with('products')->get();
            $stats['order_total'] = 0;
            foreach($orders as $order) {
                foreach($order->products as $prod) {
                    $stats['order_total'] += $prod->pivot['count'] * $prod->pivot['price'];
                }
            }
            if ($chart) {
                $stats['chart'] = $chartQuery->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as views'))
                ->groupBy('date')
                ->get();
            }
        }
        $stats['total'] = $query->count();
        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        }
        $query->with(['products' => function ($query) use($report, $product_id) {
            if ($report && $product_id) {
                $query->where('product_id', '=', $product_id);
            }
        }])
                ->with('delivery')
                ->with('payment')
                ->with('payments');
        if ($report) {
            $query->with('shop')->with('bill')->with('creator')->with('manager');
        } else {
            $query->with('products.images');
        }
        if ($per_page) {
            $query->take($per_page)->skip(($page - 1) * $per_page);
        }
        
        if ($report) {
            if (!$export) {
                $response['bills'] = Bill::where(['is_deleted' => 0])->get();
                $response['channels'] = $this->channels;
                $response['statuses'] = self::$statuses;
                $response['payments'] = PaymentType::all();
                $response['deliveries'] = Delivery::all();
                $response['shops'] = Shop::query($module_id)->get();
            }

            if ($shops) {
                $shops = explode(',', $shops);
                $query->whereIn('shop_id', $shops);
            }
            if ($bills) {
                $bills = explode(',', $bills);
                $query->whereIn('bill_id', $bills);
            }
            if ($channels) {
                $channels = explode(',', $channels);
                $query->whereIn('payment_source_id', $channels);
            }
            if ($deliveries) {
                $deliveries = explode(',', $deliveries);
                $query->whereIn('delivery_id', $deliveries);
            }
            if ($statuses) {
                $statuses = explode(',', $statuses);
                $query->whereIn('status', $statuses);
            }
        }
        $data = $query->get();
        $response['data'] = $data;
        if ($report && $product_id) {
            $filtered = [];
            foreach($data as $row)
            {
                if (count($row->products)) {
                    $filtered[] = $row;
                }
            }
            $response['data'] = $filtered;
        }
        // $query->orderBy('payments.date', 'DESC');
        foreach($data as $key => $row) {
            $status = self::$statuses[$row['status']];
            $row['status'] = $status['label'];
            $row['status_slug'] = $status['slug'];
            foreach($row->payments as $payment) {
                if (!isset($row['payment_income'])) {
                    $row['payment_income'] = 0;
                }
                if (!isset($row['payment_outcome'])) {
                    $row['payment_outcome'] = 0;
                }
                if ($payment->type == 1) {
                    $row['payment_income'] += $payment->sum;
                } else {
                    $row['payment_outcome'] += $payment->sum;
                }
            }
            unset($data[$key]['payments']);
            if ($report) {
                foreach($this->channels as $channel_group) {
                    foreach($channel_group as $channel) {
                        if ($channel['id'] == $row['payment_source_id']) {
                            $row['channel'] = $channel;
                        }
                    } 
                }
            }
        }
        if ($export) {
            $columns = ['Дата создания', 'Заказ', 'Cтатус', 'Магазин', 'Канал-продаж', 'Валюта', 'Получатель', 'Телефон', 'Сумма', 'Входящие', 'Исходящие', 'Способ оплаты', 'Счёт для оплаты', 'Способ доставки', 'Адрес доставки', 'Дата отгрузки', 'Номер ТТН', 'Заметки к заказу', 'Создатель', 'Менеджер'];
            $date = date_create();
            $filename = date_timestamp_get($date) . '_orders_report.csv';
            $file = fopen($filename, 'w');
            fputcsv($file, $columns);
            foreach($data as $row) {
                $sum = 0; 
                foreach($row->products as $product) {
                    $sum += $product->pivot->price * $product->pivot->count;
                }
                $channel = isset($row['channel']) ? $row['channel']['title'] : '';
                $payment = isset($row->payment) ? $row->payment->name : '';
                $bill = isset($row->bill) ? $row->bill->name : '';
                $delivery = isset($row->delivery) ? $row->delivery->name : '';
                $name = $row->customer_name . ' '. $row->customer_surname;
                $creator = $row->creator ? ($row->creator->name.' '.$row->creator->surname) : '';
                $manager = $row->manager ? ($row->manager->name.' '.$row->manager->surname) : '';
                fputcsv($file, [$row->created_at, 'Заказ #' . $row->id, $row->status, $row->shop->name, $channel, 'UAH', $name, $row->customer_phone, $sum, $row->payment_income, $row->payment_outcome, $payment, $bill, $delivery, $row->delivery_address, $row->created_at, $row->tnn, $row->order_comment, $creator, $manager]);
            }
            fclose($file);
            return $filename;
        } else {
            return ['data' => $response,
                    'stats' => $stats,
                    'isOver' => $stats['total'] <= $page * $per_page];
        }
    }
    public function index(Request $request)
    {
        return $this->get_index($request);   
    }
    public function index_report(Request $request)
    {
        return $this->get_index($request);   
    }
    public function show(Order $order, Request $request)
    {
        $order['products'] = $order->products()->with(['product_stats' => function ($query) use($order){
            $query->where(['stock_id' => $order->shop->stock_id]);
        }])->with('images')->get();
        $response = ['order' => $order];
        if ($request->query('info')) {
            $response['shops'] = Shop::query(self::$module_id)->get();
            $categories = PaymentState::where(['is_deleted' => 0])->get();
            $response['shops']->each(function($shop) use($categories) {
                $shop['categories'] = $categories;
            });
            $response['payments'] = PaymentType::all();
            $response['delivery'] = Delivery::all();
            $response['bills'] = Bill::where(['is_deleted' => 0])->get();
            $response['channels'] = $this->channels;
            $status_obj = self::$statuses[$response['order']->status];
            $response['order']['customer_orders'] = Order::whereNotNull('customer_phone')->where([
                ['customer_phone', '=', $order->customer_phone],
                ['id', '!=', $order->id]
            ])->with('products')->with('products.images')->get();
            foreach($response['order']['customer_orders'] as $row) {
                $row['status'] = self::$statuses[$row['status']]['label'];
            }
            $response['order']['status'] = [
                'statuses' => isset($status_obj['statuses']) ? $status_obj['statuses'] : [],
                'id' => $order->status,
                'editable' => isset($status_obj['editable']) && $status_obj['editable']
            ];
            $order['payments'] = $order->payments()->with('bill')->with('category')->get();
        }
        $response['payment_access'] = AppController::has_module_access(PaymentController::$module_id);
        $shop = Shop::find($order->shop_id);
        $plugin = Plugin::where(['is_deleted' => 0, 'enabled' => 1, 'id' => $shop->novaposhta_id])->get();
        $response['novaposhta_enabled'] = isset($plugin[0]);
        return $response;
    }
    public function store(StoreOrderRequest $request)
    {
        $data = $request->all();
        $user = JWTAuth::user();
        $data['creator_id'] = $user->id;
        $order = Order::create($data);
        $order['products'] = [];
        return $order;
    }
    public function update_field(Order $order, Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');
        $order[$field] = $value;
        $order->save();
        return 'ok';
    }
    public function update_status(Order $order, Request $request)
    {
        $from_status = $order->status;
        $to_status = $request->input('to');
        if (!isset(self::$statuses[$to_status])) abort(422);
        $from_status_obj = self::$statuses[$from_status];
        $to_status_obj = self::$statuses[$to_status];
        
        if (isset($to_status_obj['validator'])) {
            $error = $to_status_obj['validator']($order);
            if ($error) {
                return response($error, 422);
            }
        }

        $move_from_old_status = false;
        $move_to_new_status = false;
        if (isset($from_status_obj['to_status'])) {
            if ((!isset($to_status_obj['to_status'])) || (isset($to_status_obj['to_status']) && $from_status_obj['to_status'] !== $to_status_obj['to_status'])) {
                // move from old state
                $move_from_old_status = true;
            }
        }
        if (isset($to_status_obj['to_status'])) {
            if ((!isset($from_status_obj['to_status'])) || (isset($from_status_obj['to_status']) && $from_status_obj['to_status'] !== $to_status_obj['to_status'])) {
                // move to new state
                $move_to_new_status = true;
            }
        }
        $response = [];
        if ($move_from_old_status || $move_to_new_status) {
            $products = [];
            $error = null;
            foreach($order->products as $product) {
                $stats = ProductStats::where(['is_deleted' => 0, 'stock_id' => $order->shop->stock_id, 'product_id' => $product->id])->get()->first();
                $product['stats'] = $stats;
                if ($move_to_new_status) {
                    if (!$stats) {
                        $error = 'Некоторые товары не имеют зачислений на складе';
                        break;
                    }
                    $count = $product->pivot->count;
                    $in_stock = $stats['in_stock'];
                    if ($count > $in_stock) {
                        $error = 'Некоторые товары не имеют зачислений на складе';
                    }
                }
            }
            if ($error) {
                return response($error, 422);
            }
            foreach($order->products as $product) {
                $stats = $product['stats'];
                if (!$stats) continue;
                if ($move_from_old_status) {
                    $diff = $stats[$from_status_obj['to_status']] - $product->pivot->count;
                    $stats[$from_status_obj['to_status']] = $diff;
                    $stats['in_stock'] = $stats['in_stock'] + $product->pivot->count;
                    if ($stats[$from_status_obj['to_status']] < 0) $stats[$from_status_obj['to_status']] = 0;
                }
                if ($move_to_new_status) {
                    $count = $product->pivot->count;
                    $in_stock = $stats['in_stock'];
                    if ($in_stock < $count) continue;

                    $stats[$to_status_obj['to_status']] = $stats[$to_status_obj['to_status']] + $count;
                    $stats['in_stock'] = $stats['in_stock'] - $count;
                }
                $products[$product->id] = $stats;
                $stats->save();
            }
            $response['products'] = $products;
        }
        $order->status = $to_status;
        $user = JWTAuth::user();
        $order->manager_id = $user->id;
        $now = new DateTime();
        $order->status_updated_at = $now;

        if (isset($to_status_obj['before_save'])) {
            $to_status_obj['before_save']($order);
        }
        $order->save();
        $response['notices'] = [];
        if (isset($to_status_obj['after_save'])) {
            $res = $to_status_obj['after_save']($order);
            if ($res) $response['notices'][] = $res;
        }
        // $to_status_obj = ['statuses' => isset(self::$statuses[$to_status]['statuses']) ? self::$statuses[$to_status]['statuses'] : [], 'id' => $to_status, 'editable' => isset($to_status['editable'])];
        $to_status_obj['id'] = $to_status;
        $response['new_status'] = $to_status_obj;
        return $response;
    }
    public function add_products(Order $order, Request $request)
    {
        $result = [];
        foreach($request->input('rows') as $row)
        {
            $result[$row['id']] = [
                'count' => $row['count'],
                'price' => $row['price'],
                'discount' => $row['discount'],
                'selling_price' => $row['selling_price'],
                'purchase_price' => $row['purchase_price'],
            ];
        }
        $order->products()->attach($result);
        return $order->products()->with(['product_stats' => function ($query) use($order){
            $query->where(['stock_id' => $order->shop->stock_id]);
        }])->with('images')->get();
    }
    public function update_product($order, Request $request)
    {
        $update = [$request->input('prop') => $request->input('value')];
        if ($request->input('discount')) {
            $update['discount'] = $request->input('discount');
        }
        return DB::table('order_products')->where(['order_id' => $order, 'product_id' => $request->input('productId')])
                                            ->update($update);
    }
    public function set_discount ($order, Request $request) {
        $discount = $request->input('discount');
        DB::table('order_products')->where(['order_id' => $order])->update([
            'discount' => $discount,
            'price' => DB::raw("`selling_price` - (`selling_price` / 100 * $discount)")
        ]);
    }
    public function set_ttn (Order $order, Request $request) {
        $shop = Shop::find($order->shop_id);
        $plugin = Plugin::where(['is_deleted' => 0, 'enabled' => 1, 'id' => $shop->novaposhta_id])->with('settings')->get();
        if (!isset($plugin)) return abort(422);
        $setting;
        foreach($plugin[0]['settings'] as $sett)
        {
            if ($sett['name'] === 'api_key') {
                $setting = $sett['value'];
            }
        }
        $key = isset($setting) ? $setting : '';
        if (!$key) return abort(422);
        $order->np_key = $key;
        $order->ttn = $request->input('ttn');
        $order->save();
        return compact('key');
    }
    public function delete_product(Order $order, $product, Request $request)
    {
        $order->products()->detach($product);
        return 'ok';
    }
    public function find(Order $order)
    {
        $order['products'] = $order->products()->with('images')->with(['product_stats' => function ($query) use($order){
            $query->where(['stock_id' => $order->shop->stock_id]);
        }])->get();
        return $order;
    }
    public function update(Order $order, StoreOrderRequest $request)
    {
        $order->update($request->all());
        return 'ok';
    }
    public function destroy(Order $action)
    {
        return 'ok';
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return ['delivery' => Delivery::all(), 'payments' => PaymentType::all()];
    }
    public function get_warehouses ($code, Request $request)
    {
        $response = \App\MakeRequest::request('Address', 'getWarehouses', $request->input('key'), array('CityRef' => $code));
        $data = [];
        foreach($response['data'] as $row)
        {
            $data[] = [
                'name' => $row['DescriptionRu'],
                'code' => $row['Ref']
            ];
        }
        return ['warehouses' => $data];
    }
    public function create_ttn(Order $order, Request $request)
    {
        $shop = Shop::find($order->shop_id);
        $plugin = Plugin::where(['is_deleted' => 0, 'enabled' => 1, 'id' => $shop->novaposhta_id])->with('settings')->get();
        if (!isset($plugin)) return abort(422);
        $setting;
        foreach($plugin[0]['settings'] as $sett)
        {
            if ($sett['name'] === 'api_key') {
                $setting = $sett['value'];
            }
        }
        $key = isset($setting) ? $setting : '';
        if (!$key) return abort(422);
        $order->np_key = $key;
        $order->save();
        $cities = \App\MakeRequest::request("Address", "getCities", $key);
        $senders = \App\MakeRequest::request("Counterparty", "getCounterparties", $key, array("CounterpartyProperty" => "Sender"));
        $response['cities'] = $cities['data'];
        $result = [];
        foreach($senders['data'] as $id => $sender)
        {
            $answer = \App\MakeRequest::request('Counterparty', 'getCounterpartyContactPersons', $key, array('Ref' => $sender['Ref']));
            if(!empty($answer['data']))
            {
                foreach($answer['data'] as $pid => $person)
                {
                    $result[] = [
                        'contact_ref' => $person['Ref'],
                        'name' => $person['Description'],
                        'phone' => $person['Phones'],
                        'ref' => $sender['Ref']
                    ];
                }
            }
        }
        $response['senders'] = $result;
        $response['key'] = $key;
        return $response;
    }

    public function store_ttn(Order $order, StoreTTNRequest $request)
    {
        $properties = [];
        $result = [];
        $data = $request->all();
        $key = $order->np_key;
        $result['key'] = $order->np_key;
        $delivery_type = $data['delivery_type'] == 1 ? 'WarehouseWarehouse' : 'WarehouseDoors';
        $properties['DateTime'] = $data['date'];
        $properties['ServiceType'] = $delivery_type;
        $properties['PayerType'] = $data['payer'];
        $properties['Cost'] = str_replace(',', '.', $data['estimated_price']);
        $properties['PaymentMethod'] = 'Cash';
        $properties['SeatsAmount'] = '1';
        if($delivery_type == 'WarehouseWarehouse')
        {
            # ------------------
            # Создаем ПОЛУЧАТЕЛЯ
            # ------------------
            $response = \App\MakeRequest::request('Counterparty', 'save', $key, array(
                'CityRef' => $data['recipient_city_ref'],
                'FirstName' => $data['recipient_name'],
                'MiddleName' => $data['recipient_surname'],
                'LastName' => $data['recipient_fathername'],
                'Phone' => $data['recipient_phone'],
                'Email' => '',
                'CounterpartyType' => 'PrivatePerson',
                'CounterpartyProperty' => 'Recipient'
            ));
            
            if(!empty($response['success']) && $response['success'])
            {
                if(!empty($response['data']))
                {
                    $recipient['Ref'] = $response['data'][0]['Ref'];
                    $recipient['ContactRef'] = $response['data'][0]['ContactPerson']['data'][0]['Ref'];
                    
                    $properties['Recipient'] = $response['data'][0]['Ref'];
                    $properties['ContactRecipient'] = $response['data'][0]['ContactPerson']['data'][0]['Ref'];
                    $properties['CityRecipient'] = $data['recipient_city_ref'];
                    $properties['RecipientAddress'] = $data['recipient_warehouse_ref'];
                    $properties['RecipientsPhone'] = $data['recipient_phone'];
                }
            }
            if(!empty($response['errors'])) $result['errors'] = $response['errors'];
        }
        # -------------------------------------------------------------------------------------------
        if($delivery_type == 'WarehouseDoors')
        {
            $properties['NewAddress'] = "1";
            $properties['RecipientType'] = 'PrivatePerson';
            $properties['RecipientArea'] = '';
            $properties['RecipientAreaRegions'] = '';
            
            $properties['RecipientName'] = $data['recipient_name'] . ' ' . $data['recipient_surname'] . ' ' . $data['recipient_fathername'];
            $properties['RecipientsPhone'] = $data['recipient_phone'];

            $properties['RecipientCityName'] = $data['recipient_city_name'];
            $properties['RecipientAddressName'] = $data['recipient_address'];
            $properties['RecipientHouse'] = $data['recipient_house'];
            $properties['RecipientFlat'] = $data['recipient_flat'];
        }
        # -------------------------------------------------------------------------------------------    
        $properties['CargoType'] = 'Cargo';
        $properties['Description'] = $data['description'];
        $properties['Weight'] = $data['weight'];
        
        $properties['OptionsSeat'][] = array(
            'weight' => str_replace(',', '.', $data['weight']),
            'volumetricWidth' => $data['size_x'] * 0.1,
            'volumetricHeight' => $data['size_y'] * 0.1,
            'volumetricLength' => $data['size_z'] * 0.1,
            'volumetricVolume' => round(($data['size_x'] * $data['size_y'] * $data['size_z']) * 0.001 / 4000, 2)
        );
        if($data['backward_payer'] && $data['backward_price'])
        {
            $properties['BackwardDeliveryData'][] = array('CargoType' => 'Money', 'PayerType' => $data['backward_payer'], 'RedeliveryString' => $data['backward_price']);
        }
        $properties['Sender'] = $data['sender'];
        $properties['ContactSender'] = $data['sender_contact_ref'];
        $properties['CitySender'] = $data['sender_city_ref'];
        $properties['SenderAddress'] = $data['sender_warehouse_ref'];
        $properties['SendersPhone'] = $data['sender_phone'];
        $response = \App\MakeRequest::request('InternetDocument', 'save', $key, $properties);
        if(!empty($response['success']) && $response['success'])
        {
            if(!empty($response['data']))
            {
                $result['waybill']['PayerType'] = $properties['PayerType'];
                $result['waybill']['number'] = $response['data'][0]['IntDocNumber'];
                $result['waybill']['cost'] = $response['data'][0]['CostOnSite'];
                $result['waybill']['date'] = $response['data'][0]['EstimatedDeliveryDate'];
                $result['waybill']['APIKey'] = $key;
                
                $result['code'] = 1;
                $order->ttn = $result['waybill']['number'];
                $order->save();
            }
        }
        if(!empty($response['errors'])) $result['errors'] = $response['errors'];
        return $result;
    }
    public function get_orders_statuses_np(GetTTNStatusesRequest $request)
    {
        return self::get_orders_statuses_list_np($request->input('ttns'));
    }
    static private function get_orders_statuses_list_np($ttns)
    {
        $req = [];
        $result = [];
        foreach($ttns as $order)
        {
            $phone = isset($order['phone']) ? $order['phone'] : '';
            if (!isset($req[$order['key']])) {
                $req[$order['key']] = [['DocumentNumber' => $order['ttn'], 'Phone' => $phone]];
            } else {
                $req[$order['key']][] = ['DocumentNumber' => $order['ttn'], 'Phone' => $phone];
            }
        }
        foreach($req as $key => $row)
        {
            // return [
            //     'key' => $key,
            //     'enabled' => PluginController::isPluginEnabledByKey($key)
            // ];
            if (!PluginController::isPluginEnabledByKey($key)) continue;
            $length = count($row);
            for($i = 0; $i < ceil($length / 100); $i++)
            {
                $start = $i * 100;
                $end = ($i + 1) * 100;
                $ttns = array_slice($row, $start, $start + $end);
                $response = \App\MakeRequest::request('TrackingDocument', 'getStatusDocuments', $key, array('Documents' => $ttns));
                $data = [];
                // return array('Documents' => $ttns);
                foreach($response['data'] as $row)
                {
                    $data[] = [
                        'ttn' => $row['Number'],
                        'status' => $row['Status'],
                        'address' => isset($row['WarehouseRecipient']) ? $row['WarehouseRecipient'] : '',
                        'delivery_date' => isset($row['ScheduledDeliveryDate']) ? $row['ScheduledDeliveryDate'] : '',
                        'cost' => isset($row['DocumentCost']) ? $row['DocumentCost'] : '',
                        // 'created_date' => $row['CreateTime'],
                        'sender_city' => isset($row['CitySender']) ? $row['CitySender'] : '',
                        'recipient_city' => isset($row['CityRecipient']) ? $row['CityRecipient'] : '',
                        'status_code' => isset($row['StatusCode']) ? $row['StatusCode'] : '',
                    ];
                }
                $result = array_merge($result, $data);
            }
        }
        return $result;
    }
}

OrderController::setup();