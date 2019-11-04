<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_states')->insert([
            'id' => 1,
            'name' => 'Оплата заказа (от клиента)',
            'type' => 1
        ]);
        DB::table('payment_states')->insert([
            'id' => 2,
            'name' => 'Доставка',
            'type' => 2
        ]);
        $deliveries = [
            [
                'name' => 'Нова пошта (в отделение)'
            ],
            [
                'name' => 'Нова пошта (курьером по адресу)'
            ],
            [
                'name' => 'Нова пошта (в почтомат)'
            ],
            [
                'name' => 'Ин Тайм (в отделение)'
            ],
            [
                'name' => 'Ин Тайм (в почтомат)'
            ],
            [
                'name' => 'Ин Тайм (курьером по адресу)'
            ],
            [
                'name' => 'Мист Экспресс'
            ],
            [
                'name' => 'УкрПочта'
            ],
            [
                'name' => 'Курьером'
            ],
            [
                'name' => 'Самовывоз'
            ]
        ];
        $payment_types = [
            ['name' => 'Наличные'],
            ['name' => 'Наложенный платеж'],
            ['name' => 'Оплата на карту'],
            ['name' => 'Оплата на р/счет'],
            ['name' => 'Оплата на сайте']
        ];
        DB::table('deliveries')->insert($deliveries);
        DB::table('payment_types')->insert($payment_types);
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Петро',
            'surname' => 'Чупрун',
            'is_admin' => true,
            'login' => 'adminka',
            'password' => Hash::make('password')
        ]);
    }
}
