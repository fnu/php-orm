<?php

namespace G;

/**
 * 一小些函数
 *
 * @author ghost
 */
class Fun
{

    public static function toCamelCase($name)
    {
        return preg_replace_callback(
                '/(_[a-z])/i', function($matches) {
            return strtoupper(trim($matches[1], '_'));
        }, trim(strtolower($name), '_'));
    }

}
