<?php

namespace App\Support;

final class UtmUrl
{
    /**
     * Add or replace query parameters while preserving the URL fragment.
     *
     * @param  array<string, scalar>  $parameters
     */
    public static function add(?string $url, array $parameters): ?string
    {
        if (! filled($url)) {
            return $url;
        }

        $url = html_entity_decode((string) $url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        [$urlWithoutFragment, $fragment] = array_pad(explode('#', $url, 2), 2, null);
        [$baseUrl, $queryString] = array_pad(explode('?', $urlWithoutFragment, 2), 2, '');

        parse_str($queryString, $query);

        $query = array_replace($query, $parameters);
        $queryString = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        return $baseUrl
            .($queryString !== '' ? '?'.$queryString : '')
            .($fragment !== null ? '#'.$fragment : '');
    }
}
