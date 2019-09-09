<?php
use App\Rent;

return [
  'show_more' => 'Show more',
  'title' => 'Rent history',
  'message' => 'You can see your rent history here',
  'no_rents' => 'There is no rents',
  'time' => 'trip time',
  'date' => 'date',
  'card' => 'card',
  'bonuses'  => 'bonuses',
  'cost'   => 'total cost',
  'status' => 'status',
  'tariff' => 'tariff',
  'type'   => 'rent type',
  'minutes'  => 'minutes',
  'r'   => 'r.',
  'min'     => 'min',
  'rent_statuses' => [
      Rent::S_BEGIN => "Began",
      Rent::S_END => "Ended",
      Rent::S_PROBLEM => "Problems"
  ],
  'rent_types' => [
      Rent::T_RENT => "Rent",
      Rent::T_RESERVATION => "Reservation"
  ],
];