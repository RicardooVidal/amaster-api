<?php 

namespace App\Util;

class Money {

    // To elasticsearch
    public static function parseInt($value) 
    {
        $value = str_replace('.','', $value);
        $value = intval($value);

        return $value; 
    }
}