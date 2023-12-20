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

function configure_query_parameters(string $uri, array $parameters): string
{
    if (!empty($parameters)) {
        $paramMappings = [
            'page_limit' => 'page[limit]',
            'page_before' => 'page[before]',
            'page_after' => 'page[after]',
            'from_status_changed' => 'fromStatusChanged',
            'to_status_changed' => 'toStatusChanged',
            'page_number' => 'page[number]',
            'page_size' => 'page[size]',
            'offset' => 'offset',
            'size' => 'size',
            'from' => 'from',
            'to' => 'to',
            'after' => 'after',
        ];

        foreach ($parameters as $key => $value) {
            if (array_key_exists($key, $paramMappings)) {
                $uri .= urlencode("{$paramMappings[$key]}=$value");
            } elseif (in_array($key, ['country', 'name', 'paymentsEnabled', 'bulkPaymentsEnabled', 'sharedBrandName', 'sharedBrandReference'])) {
                // Handle filter conditions for specific fields with operators
                $operator = is_array($value) ? 'in' : ($key === 'name' ? 'contains' : 'eq'); // Default is 'eq'
                $uri .= urlencode("filter[$key][$operator]=$value");
            }
        }
    }

    return $uri;
}