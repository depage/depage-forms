<?php

/**
 * @file    Validators/Url.php
 * @brief   url validator
 **/

namespace Depage\HtmlForm\Validators;

/**
 * @brief default validator for url input elements
 **/
class Url extends Validator
{
    // {{{ validate()
    /**
     * @brief   url validator
     *
     * @param  string $url        url to be validated
     * @param  array  $parameters validation parameters
     * @return bool   validation result
     **/
    public function validate($url, $parameters = []): bool
    {
        return (bool) filter_var(self::normalizeUrl($url), FILTER_VALIDATE_URL);
    }
    // }}}

    // {{{ normalizeUrl()
    /**
     * @brief   normalizes url
     *
     * @param  string $url url to be normalized
     * @return string normalized url
     **/
    public static function normalizeUrl($url): string
    {
        $info = parse_url(trim($url));

        $scheme   = isset($info['scheme']) ? $info['scheme'] . '://' : '';
        $host     = $info['host'] ?? '';
        $host     = idn_to_ascii($host);
        $port     = isset($info['port']) ? ':' . $info['port'] : '';
        $user     = isset($info['user']) ? rawurlencode($info['user']) : '';
        $pass     = isset($info['pass']) ? ':' . rawurlencode($info['pass']) : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $info['path'] ?? '';
        $query    = isset($info['query']) ? '?' . $info['query'] : '';
        $fragment = isset($info['fragment']) ? '#' . $info['fragment'] : '';


        $encodedPath = implode('/', array_map('Depage\HtmlForm\Validators\Url::encodeUrlPath', explode('/', $path)));

        return "$scheme$user$pass$host$port$encodedPath$query$fragment";
    }
    // }}}

    // {{{ humanReadableUrl()
    /**
     * @brief   changes encoded url to a human readable format
     *
     * @param  string $url url to be displayed
     * @return string normalized url
     **/
    public static function humanReadableUrl($url): string
    {
        $info = parse_url(trim($url));

        $scheme   = isset($info['scheme']) ? $info['scheme'] . '://' : '';
        $host     = $info['host'] ?? '';
        $host     = idn_to_utf8($host);
        $port     = isset($info['port']) ? ':' . $info['port'] : '';
        $user     = isset($info['user']) ? rawurldecode($info['user']) : '';
        $pass     = isset($info['pass']) ? ':' . rawurldecode($info['pass']) : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $info['path'] ?? '';
        $query    = isset($info['query']) ? '?' . rawurldecode($info['query']) : '';
        $fragment = isset($info['fragment']) ? '#' . rawurldecode($info['fragment']) : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }
    // }}}

    // {{{ encodeUrlPath()
    /**
     * @brief   Encodes the path of an URL
     *
     * @param   string $path
     *
     * @return  string
     **/
    protected static function encodeUrlPath($path): string
    {
        if (rawurlencode($path) != str_replace(['%','+'], ['%25','%2B'], $path)) {
            $path = rawurlencode($path);
        }

        return $path;
    }
    // }}}
}
