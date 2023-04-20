<?php

use Dotenv\Dotenv;
use function Ibanity\Helpers\HttpVerbs\{get, post};

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

/**
 * Testing section
 */

/**
 * Example URI
 * https://api.ibanity.com/ponto-connect/einvoicing/oauth2/token
 */
function post_test()
{
    $url = "https://api.ibanity.com";
    $api = "/einvoicing";
    $urn = "/oauth2/token";
    $uri = $url . $api . $urn;

    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];

    $post_fields = "grant_type=client_credentials";

    var_dump(post($uri, $headers, $post_fields));
}

/**
 * Example URI
 * https://api.ibanity.com/ponto-connect/accounts/078810ab-d44f-455b-9988-3dae526ae19f
 */
function get_test()
{
    $url = "https://api.ibanity.com";
    $api = "/ponto-connect";
    $account_id = "078810ab-d44f-455b-9988-3dae526ae19f";
    $urn = "/accounts/" . $account_id;
    $uri = $url . $api . $urn;

    $access_token = "7kqr0VY0uEvTE0TZtmT4Zj9_vSoaGu-32m1Ivc6rvHY.gTFbbdbmHTmXMBmKC1DkIfbhaE26XJ8Mp3rDkZ-q7ME";
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . $access_token,
    ];

    var_dump(get($uri, $headers));
}
