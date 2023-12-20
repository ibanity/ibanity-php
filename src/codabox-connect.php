<?php

namespace Ibanity\Ponto;

require __DIR__ . '/../vendor/autoload.php';
require_once 'helpers/http-verbs.php';
require_once 'helpers/uri-path.php';
require_once 'schema.php';

use Dotenv\Dotenv;
use function Ibanity\Helpers\HttpVerbs\{get, post};
use function Ibanity\Helpers\UriPath\{configure_path, remove_path_id};
use function Ibanity\Schema\get_codabox;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

$codabox_schema = get_codabox();

/*
 * Authorization Resources
 * Token
 * */

function request_access_token()
{
    global $codabox_schema;
    $uri = $codabox_schema['oauth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "grant_type=client_credentials";
    return post($uri, $headers, $post_fields);
}

/*
 * Accounting Office Resources
 * Accounting Office Consent
 * */

function create_accounting_office_consent(string $access_token, $payload)
{
    global $codabox_schema;
    $uri = remove_path_id($codabox_schema['accountingOfficeConsents'], 'accountingOfficeConsent');
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer $access_token",
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

function get_accounting_office_consent(string $access_token, string $accounting_office_consent_id)
{
    global $codabox_schema;
    $uri = configure_path($codabox_schema['accountingOfficeConsents'], ['accountingOfficeConsentId' => $accounting_office_consent_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer $access_token"
    ];
    return get($uri, $headers);
}

/*
 * Accounting Office Resources
 * Document Search
 * */

function create_document_search(string $access_token, $payload)
{
    global $codabox_schema;
    $uri = remove_path_id($codabox_schema['accountingOfficeConsents'], 'accountingOfficeConsent');
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer $access_token",
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

/*
 * Accounting Office Resources
 * Bank Account Statement
 * */

function get_bank_account_statement(string $access_token, string $accounting_office_id, string $client_id, string $bank_account_statement_id, string $response_format)
{
    //TODO: Missing address in schema
    $uri = "https://api.ibanity.com/codabox-connect/accounting-offices/$accounting_office_id/clients/$client_id/bank-account-statements/$bank_account_statement_id";
    $headers = [
        "Accept: $response_format",
        "Authorization: Bearer $access_token"
    ];
    return get($uri, $headers);
}

/*
 * Accounting Office Resources
 * Payroll Statement
 * */

function get_payroll_statement(string $access_token, string $accounting_office_id, string $client_id, string $payroll_statement_id, string $response_format = "application/vnd.api+json")
{
    //TODO: Missing address in schema
    $uri = "https://api.ibanity.com/codabox-connect/accounting-offices/$accounting_office_id/clients/$client_id/payroll-statements/$payroll_statement_id";
    $headers = [
        "Accept: $response_format",
        "Authorization: Bearer $access_token"
    ];
    return get($uri, $headers);
}

/*
 * Accounting Office Resources
 * Credit Card Statement
 * */

function get_credit_card_statement(string $access_token, string $accounting_office_id, string $client_id, string $credit_card_statement_id, string $response_format = "application/vnd.api+json")
{
    global $codabox_schema;
    $uri = configure_path($codabox_schema['accountingOffices']['creditCardStatements'], ['accountingOfficeId' => $accounting_office_id, 'clientId' => $client_id, 'creditCardStatementId' => $credit_card_statement_id]);
    $headers = [
        "Accept: $response_format",
        "Authorization: Bearer $access_token"
    ];
    return get($uri, $headers);
}

/*
 * Accounting Office Resources
 * Sales Invoice
 * */

function get_sales_invoice(string $access_token, string $accounting_office_id, string $client_id, string $sales_invoice_id, string $response_format = "application/vnd.api+json")
{
    //TODO: Missing address in schema
    $uri = "https://api.ibanity.com/codabox-connect/accounting-offices/$accounting_office_id/clients/$client_id/sales-invoices/$sales_invoice_id";
    $headers = [
        "Accept: $response_format",
        "Authorization: Bearer $access_token"
    ];
    return get($uri, $headers);
}

/*
 * Accounting Office Resources
 * Purchase Invoice
 * */

function get_purchase_invoice(string $access_token, string $accounting_office_id, string $client_id, string $purchase_invoice_id, string $response_format = "application/vnd.api+json")
{
    //TODO: Missing address in schema
    $uri = "https://api.ibanity.com/codabox-connect/accounting-offices/$accounting_office_id/clients/$client_id/purchase-invoices/$purchase_invoice_id";
    $headers = [
        "Accept: $response_format",
        "Authorization: Bearer $access_token"
    ];
    return get($uri, $headers);
}