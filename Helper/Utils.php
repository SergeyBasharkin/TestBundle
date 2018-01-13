<?php
/**
 * Created by PhpStorm.
 * User: sergey
 * Date: 13.01.18
 * Time: 17:14
 */

namespace Test\TestBundle\Helper;


class Utils
{
    public static function parsePath(string $path){
        $a = explode('/', $path);
        $b = array();
        foreach ($a as $c){
            if ($c) $b[] = $c;
        }
        return $b;
    }
}