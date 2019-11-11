<?php

namespace App\Services;

class Months
{
    private static $months = [
        1 => [
            'name' => 'Janeiro',
            'short' => 'Jan'
        ],
        2 => [
            'name' => 'Fevereiro',
            'short' => 'Fev'
        ],
        3 => [
            'name' => 'Marco',
            'short' => 'Mar'
        ],
        4 => [
            'name' => 'Abril',
            'short' => 'Abr'
        ],
        5 => [
            'name' => 'Maio',
            'short' => 'Mai'
        ],
        6 => [
            'name' => 'Junho',
            'short' => 'Jun'
        ],
        7 => [
            'name' => 'Julho',
            'short' => 'Jul'
        ],
        8 => [
            'name' => 'Agosto',
            'short' => 'Ago'
        ],
        9 => [
            'name' => 'Setembro',
            'short' => 'Set'
        ],
        10 => [
            'name' => 'Outubro',
            'short' => 'Out'
        ],
        11 => [
            'name' => 'Novembro',
            'short' => 'Nov'
        ],
        12 => [
            'name' => 'Dezembro',
            'short' => 'Dez'
        ]
    ];

    public static function find($number = null)
    {
        if (is_numeric($number)) {
            if (array_key_exists($number, self::$months)) {
                return self::$months[$number]['name'];
            }
        }
        return self::$months[date('n')]['name'];
    }

    public static function get($short = false)
    {
        return array_map(function ($month) use ($short) {
            return $short ? $month['short'] : $month['name'];
        }, self::$months);
    }
}
