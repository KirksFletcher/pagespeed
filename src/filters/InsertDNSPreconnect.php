<?php

namespace kirksfletcher\pagespeed\filters;

class InsertDNSPreconnect
{

    /**
     * @param $view
     * @return null|string|string[]
     */
    public static function render($view)
    {
        preg_match_all('/"(.*)\/\/(.*)"([^\/s])/', $view, $matches);

        $dnsPreconnect = collect($matches[0])->map(function ($domain) {
            $domainArray = explode('"', $domain);

            if (isset($domainArray[1])) {
                $domainParts = parse_url($domainArray[1]);

                if (array_key_exists('host', $domainParts)) {
                    $url = (array_key_exists('scheme', $domainParts)) ? $domainParts['scheme'] . '://' . $domainParts['host'] : 'https://' . $domainParts['host'];

                    return "<link rel=\"preconnect\" href=\"{$url}\">";
                } else {
                    return '';
                }
            } else {
                return '';
            }
        })->unique()->implode("\n");

        $replace = ['#<head>(.*?)#' => "<head>\n{$dnsPreconnect}"];

        return preg_replace(array_keys($replace), array_values($replace), $view);
    }
}
