<?php

namespace kirksfletcher\pagespeed\filters;


class RemoveWhiteSpace
{

    /**
     * @param $view
     * @return null|string|string[]
     */
    public static function render($view) {
        $replace = [
            "/\n([\S])/" => '$1',
            "/\r/" => '',
            "/\n/" => '',
            "/\t/" => '',
            "/ +/" => ' ',
            "/> +</" => '><',
        ];

        return preg_replace(array_keys($replace), array_values($replace), $view);
    }

}