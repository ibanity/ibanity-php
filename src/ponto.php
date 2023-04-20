<?php

namespace Ibanity\Ponto;

require __DIR__ . '/../vendor/autoload.php';
require_once 'helpers/http-verbs.php';
require_once 'helpers/uri-path.php';
require_once 'schema.php';

use Dotenv\Dotenv;
use function Ibanity\Helpers\HttpVerbs\{delete, get, patch, post};
use function Ibanity\Helpers\UriPath\{configure_path, configure_query_parameters, remove_path_id};
use function Ibanity\Schema\get_ponto_connect;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

$ponto_connect_schema = get_ponto_connect();

/*
 * Authorization Resources
 * Token
 * */

function request_initial_tokens(string $code, string $client_id, string $redirect_uri, string $code_verifier)
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['oauth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "grant_type=authorization_code&code={$code}&client_id={$client_id}&redirect_uri={$redirect_uri}&code_verifier={$code_verifier}";
    return post($uri, $headers, $post_fields);
}

function request_access_tokens(string $refresh_token, string $client_id)
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['oauth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "grant_type=refresh_token&refresh_token={$refresh_token}&client_id={$client_id}";
    return post($uri, $headers, $post_fields);
}

function request_client_access_token()
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

function revoke_refresh_token(string $token)
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['oauth2']['revoke'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "token=${token}";
    return post($uri, $headers, $post_fields);
}

/*
 * Account Information Resources
 * Financial Institution
 * */

function list_financial_institutions(array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(remove_path_id($ponto_connect_schema['financialInstitutions'], 'financialInstitution'), $parameters);
    $headers = [
        "Accept: application/vnd.api+json"
    ];
    return get($uri, $headers);
}

function get_financial_institution(string $institution_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['financialInstitutions'], ['financialInstitutionId' => $institution_id]);
    $headers = [
        "Accept: application/vnd.api+json"
    ];
    return get($uri, $headers);
}

function list_organization_financial_institutions(array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(remove_path_id($ponto_connect_schema['financialInstitutions'], 'financialInstitution'), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_organization_financial_institution(string $institution_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['financialInstitutions'], ['financialInstitutionId' => $institution_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Account Information Resources
 * Account
 * */

function list_accounts(array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(remove_path_id($ponto_connect_schema['accounts'], 'account'), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_account(string $account_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['accounts'], ['accountId' => $account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function revoke_account(string $account_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['accounts'], ['accountId' => $account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return delete($uri, $headers);
}

/*
 * Account Information Resources
 * Transaction
 * */

function list_transactions(string $account_id, array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(configure_path(remove_path_id($ponto_connect_schema['account']['transactions'], 'transaction'), ['accountId' => $account_id]), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_transaction(string $account_id, string $transaction_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['account']['transactions'], ['accountId' => $account_id, 'transactionId' => $transaction_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function list_updated_transactions_for_synchronization(string $synchronization_id, array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(configure_path($ponto_connect_schema['synchronization']['updatedTransactions'], ['synchronizationId' => $synchronization_id]), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Account Information Resources
 * Reauthorization Request
 * */

function request_account_reauthorization(string $account_id, $payload)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['account']['reauthorizationRequests'], ['accountId' => $account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

/*
 * Payment Initiation Resources
 * Create Payment
 * */

function create_payment(string $account_id, $payload)
{
    global $ponto_connect_schema;
    $uri = configure_path(remove_path_id($ponto_connect_schema['account']['payments'], 'payment'), ['accountId' => $account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

function get_payment(string $account_id, string $payment_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['account']['payments'], ['accountId' => $account_id, 'paymentId' => $payment_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function delete_payment(string $account_id, string $payment_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['account']['payments'], ['accountId' => $account_id, 'paymentId' => $payment_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return delete($uri, $headers);
}

/*
 * Payment Initiation Resources
 * Bulk Payment
 * */

function create_bulk_payment(string $account_id, string $payload)
{
    global $ponto_connect_schema;
    $uri = configure_path(remove_path_id($ponto_connect_schema['account']['bulkPayments'], 'bulkPayment'), ['accountId' => $account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

function get_bulk_payment(string $account_id, string $bulk_payment_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['account']['bulkPayments'], ['accountId' => $account_id, 'bulkPaymentId' => $bulk_payment_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function delete_bulk_payment(string $account_id, string $bulk_payment_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['account']['bulkPayments'], ['accountId' => $account_id, 'bulkPaymentId' => $bulk_payment_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return delete($uri, $headers);
}

/*
 * Synchronization Resources
 * Synchronization
 * */

function create_synchronization($payload)
{
    global $ponto_connect_schema;
    $uri = remove_path_id($ponto_connect_schema['synchronizations'], 'synchronization');
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

function get_synchronization(string $synchronization_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['synchronizations'], ['synchronizationId' => $synchronization_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Sandbox Resources
 * Financial Institution Account
 * */

function list_financial_institution_accounts(string $financial_institution_id, array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(configure_path(remove_path_id($ponto_connect_schema['sandbox']['financialInstitution']['financialInstitutionAccounts'], 'financialInstitutionAccount'), ['financialInstitutionId' => $financial_institution_id]), $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_financial_institution_accounts(string $financial_institution_id, string $financial_institution_account_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['sandbox']['financialInstitution']['financialInstitutionAccounts'], ['financialInstitutionId' => $financial_institution_id, 'financialInstitutionAccountId' => $financial_institution_account_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Sandbox Resources
 * Financial Institution Transaction
 * */

function list_financial_institution_transactions(string $financial_institution_id, string $financial_institution_account_id, array $parameters)
{
    global $ponto_connect_schema;
    $uri = configure_query_parameters(
        configure_path(
            remove_path_id($ponto_connect_schema['sandbox']['financialInstitution']['financialInstitutionAccount']['financialInstitutionTransactions'], 'financialInstitutionTransaction'),
            ['financialInstitutionId' => $financial_institution_id, 'financialInstitutionAccountId' => $financial_institution_account_id]
        ),
        $parameters
    );
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function get_financial_institution_transaction(string $financial_institution_id, string $financial_institution_account_id, string $financial_institution_transaction_id)
{
    global $ponto_connect_schema;
    $uri = configure_path(
        $ponto_connect_schema['sandbox']['financialInstitution']['financialInstitutionAccounts']['financialInstitutionTransactions'],
        [
            'financialInstitutionId' => $financial_institution_id,
            'financialInstitutionAccountId' => $financial_institution_account_id,
            'financialInstitutionTransactionId' => $financial_institution_transaction_id
        ]
    );
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function create_financial_institution_transaction(string $financial_institution_id, string $financial_institution_account_id, $payload)
{
    global $ponto_connect_schema;
    $uri = configure_path(
        remove_path_id($ponto_connect_schema['sandbox']['financialInstitution']['financialInstitutionAccounts']['financialInstitutionTransactions'], 'financialInstitutionTransaction'),
        [
            'financialInstitutionId' => $financial_institution_id,
            'financialInstitutionAccountId' => $financial_institution_account_id
        ]
    );
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

function update_financial_institution_transaction(string $financial_institution_id, string $financial_institution_account_id, string $financial_institution_transaction_id, $payload)
{
    global $ponto_connect_schema;
    $uri = configure_path(
        $ponto_connect_schema['sandbox']['financialInstitution']['financialInstitutionAccounts']['financialInstitutionTransactions'],
        ['financialInstitutionId' => $financial_institution_id, 'financialInstitutionAccountId' => $financial_institution_account_id, 'financialInstitutionTransactionId' => $financial_institution_transaction_id]
    );
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return patch($uri, $headers, $json_payload);
}

/*
 * Organization Resources
 * Onboarding Details
 * */

function create_onboarding_details($payload)
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['onboardingDetails'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

/*
 * Organization Resources
 * User Info
 * */

function get_user_info()
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['userinfo'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Organization Resources
 * Payment Activation Request
 * */

function request_payment_activation($payload)
{
    global $ponto_connect_schema;
    $uri = $ponto_connect_schema['paymentActivationRequests'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

/*
 * Organization Resources
 * Usage
 * */

function get_organization_usage(string $organization_id, string $month)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['organizations']['usage'], ['organizationId' => $organization_id, 'month' => $month]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

/*
 * Organization Resources
 * Integration
 * */

function delete_organization_integration(string $organization_id)
{
    global $ponto_connect_schema;
    $uri = configure_path($ponto_connect_schema['organizations']['integration'], ['organizationId' => $organization_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return delete($uri, $headers);
}