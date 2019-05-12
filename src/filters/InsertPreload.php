<?php

namespace kirksfletcher\pagespeed\filters;


class InsertPreload
{

    public static function render($view)
    {
        preg_match_all('/(?i)"(.*)\/\/(.*).(js|css|jpg|jpeg|png|gif|webp)(.*)"/', $view, $matches);

        $preload = collect($matches[0])->map(function ($domain) {

            $domainArray = explode('"', $domain);

            if (isset($domainArray[1])) {

                $preloadItem = (substr(trim($domainArray[1]), 0, 2) === '//') ? 'https:' . strtolower($domainArray[1]) : strtolower($domainArray[1]);

                $type = '';

                if (Self::contains($preloadItem, ['js'])) {
                    $type = 'script';
                }

                if (Self::contains($preloadItem, ['css'])) {
                    $type = 'style';
                }

                if (Self::contains($preloadItem, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $type = 'image';
                }

                return "<link rel=\"preload\" href=\"{$preloadItem}\" as=\"{$type}\">";

            }else{
                return '';
            }

        })->unique()->implode("\n");

        $replace = ['#<head>(.*?)#' => "<head>\n{$preload}"];

        return preg_replace(array_keys($replace), array_values($replace), $view);
    }

    private static function contains($str, array $arr)
    {
        foreach($arr as $a) {
            if (stripos($str,$a) !== false) return true;
        }
        return false;
    }

}