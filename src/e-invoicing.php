<?php

namespace Ibanity\Ponto;

require __DIR__ . '/../vendor/autoload.php';
require_once 'helpers/http-verbs.php';
require_once 'helpers/uri-path.php';
require_once 'schema.php';

use Dotenv\Dotenv;
use function Ibanity\Helpers\HttpVerbs\{delete, get, patch, post};
use function Ibanity\Helpers\UriPath\{configure_path, configure_query_parameters, remove_path_id};
use function Ibanity\Schema\get_einvoicing;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

$e_invoicing_schema = get_einvoicing();

/*
 * Authorization Resources
 * Token
 * */

function request_access_tokens(string $refresh_token, string $client_id)
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['oauth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "grant_type=client_credentials";
    return post($uri, $headers, $post_fields);
}