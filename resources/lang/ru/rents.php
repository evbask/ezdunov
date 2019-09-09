<?php

use App\Rent;

return [
    'show_more' => 'Показать еще',
    'title' => 'История аренд',
    'message' => 'В этой таблице Вы можете видеть историю совершенных Вами аренд.',
    'no_rents' => 'У Вас нет аренд.',
    'time' => 'время',
    'date' => 'дата',
    'card' => 'карта',
    'bonuses'  => 'бонусы',
    'cost'   => 'итого',
    'status' => 'статус',
    'tariff' => 'тариф',
    'type'   => 'тип',
    'minutes'    => 'мин',
    'min'     => 'мин',
    'r'     => 'р.',
    'rent_statuses' => [
        Rent::S_BEGIN => "Началась",
        Rent::S_END => "Завершилась",
        Rent::S_PROBLEM => "Проблемы"
    ],
    'rent_types' => [
        Rent::T_RENT => "Аренда",
        Rent::T_RESERVATION => "Бронь"
    ],
];