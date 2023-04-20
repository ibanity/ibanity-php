<?php

namespace Ibanity\Helpers\UriPath;

use Dotenv\Dotenv;
use Exception;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

function get_uri_path_from_urn($urn)
{
    try {
        $host = "https://{$_ENV["HOST"]}";
        if (empty($urn)) {
            throw new Exception("URN shouldn't empty");
        } else if (str_starts_with($urn, "/")) {
            return $host . substr($urn, 1);
        } else if (filter_var($urn, FILTER_VALIDATE_URL)) {
            return $urn;
        } else {
            return "$host/$urn";
        }
    } catch (Exception $e) {
        echo 'Caught exception: ', $e->getMessage();
        return null;
    }
}

function remove_path_id($uri, $resource): string
{
    $pathId = "/{{$resource}Id}";
    if (str_ends_with($uri, $pathId)) {
        $uri = substr($uri, 0, strpos($uri, $pathId));
    }
    return $uri;
}

function configure_path($uri, $attributes = []): string
{
    foreach ($attributes as $key => $value) {
        $uri = str_replace("{" . $key . "}", $value, $uri);
    }
    return $uri;
}

function configure_query_parameters($uri, array $parameters): string
{
    foreach ($parameters as $key => $value) {
        if ($key == 'page_limit') {
            $uri .= urlencode("page[limit]=${$value}");
        } else if ($key == 'page_before') {
            $uri .= urlencode("page[before]=${$value}");
        } else if ($key == 'page_after') {
            $uri .= urlencode("page[after]=${$value}");
        } else {
// TODO: implement a class for the filter object
            $uri .= urlencode("filter[][]=${$value}");
        }
    }
    return $uri;
}