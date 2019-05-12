<?php

namespace kirksfletcher\pagespeed\filters;

class RemoveComments
{

    /**
     * @param $view
     * @return null|string|string[]
     */
    public static function render($view)
    {
        $replace = [
            '/<!--[^]><!\[](.*?)[^\]]-->/s' => ''
        ];

        return preg_replace(array_keys($replace), array_values($replace), $view);
    }
}
