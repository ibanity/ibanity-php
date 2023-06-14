<?php

namespace Ibanity\IsabelConnect;

require __DIR__ . '/../vendor/autoload.php';
require_once 'helpers/http-verbs.php';
require_once 'helpers/uri-path.php';
require_once 'schema.php';

use Dotenv\Dotenv;
use function Ibanity\Helpers\HttpVerbs\{delete, get, patch, post};
use function Ibanity\Helpers\UriPath\{configure_path, configure_query_parameters, remove_path_id};
use function Ibanity\Schema\get_isabel_connect;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

$isabel_schema = get_isabel_connect();

/*
 * Authorization Resources
 * Token
 * */

function request_initial_tokens(string $code, string $redirect_uri)
{
    global $isabel_schema;
    $uri = $isabel_schema['oAuth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json;version=2",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded",
    ];
    $post_fields = "grant_type=authorization_code&code={$code}&redirect_uri={$redirect_uri}";
    return post($uri, $headers, $post_fields);
}

function request_access_tokens(string $refresh_token, string $client_id)
{
    global $isabel_schema;
    $uri = $isabel_schema['oAuth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "grant_type=refresh_token&refresh_token={$refresh_token}&client_id={$client_id}";
    return post($uri, $headers, $post_fields);
}

function revoke_refresh_token(string $token)
{
    global $isabel_schema;
    $uri = $isabel_schema['oAuth2']['revoke'];
    $headers = [
        "Accept: application/vnd.api+json;version=2",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "token=${$token}";
    return post($uri, $headers, $post_fields);
}

/*
 * Account Information Resources
 * Account
 * */

function list_accounts(array $parameters)
{
    global $isabel_schema;
    $uri = configure_query_parameters(remove_path_id($isabel_schema['accounts'], 'accountReport'), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_account(string $account_id)
{
    global $isabel_schema;
    $uri = configure_path($isabel_schema['accounts'], ['accountId' => $account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Account Information Resources
 * Balance
 * */

function list_balances(string $account_id, array $parameters)
{
    global $isabel_schema;
    $uri = configure_query_parameters(configure_path($isabel_schema['account']['balances'], ['accountId' => $account_id]), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Account Information Resources
 * Transaction
 * */

function list_transactions(string $account_id, array $parameters)
{
    global $isabel_schema;
    $uri = configure_query_parameters(configure_path($isabel_schema['account']['transactions'], ['accountId' => $account_id]), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Account Information Resources
 * Intraday Transaction
 * */

function list_intraday_transactions(string $account_id, array $parameters)
{
    global $isabel_schema;
    $uri = configure_query_parameters(configure_path($isabel_schema['account']['intradayTransactions'], ['accountId' => $account_id]), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Account Information Resources
 * Account Report
 * */

function list_account_reports(array $parameters)
{
    global $isabel_schema;
    $uri = configure_query_parameters(remove_path_id($isabel_schema['accountReports'], 'accountReport'), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_account_report(string $account_report_id, array $parameters)
{
    global $isabel_schema;
    $uri = configure_query_parameters(configure_path($isabel_schema['account']['intradayTransactions'], ['accountId' => $account_report_id]), $parameters);
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Payment Initiation Resources
 * Bulk Payment Initiation
 * */

function create_bulk_payment_initiation(string $filename, $payload, $is_shared = true, $is_hidden = false)
{
    global $isabel_schema;
    $uri = remove_path_id($isabel_schema['bulkPaymentInitiationRequests'], 'bulkPaymentInitiationRequest');
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Accept: application/vnd.api+json",
        "Content-Type: application/xml",
        "Content-Disposition: inline; filename=${$filename}",
        "Is-Shared: ${$is_shared}",
        "Hide-Details: ${$is_hidden}"
    ];
    return post($uri, $headers, $payload);
}

function get_bulk_payment_initiation(string $bulk_payment_initiation_request_id)
{
    global $isabel_schema;
    $uri = configure_path($isabel_schema['bulkPaymentInitiationRequests'], ["bulkPaymentInitiationRequestId" => $bulk_payment_initiation_request_id]);
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Accept: application/vnd.api+json"
    ];
    return get($uri, $headers);
}