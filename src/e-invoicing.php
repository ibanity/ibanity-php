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
    global $e_invoicing_schema;
    $uri = $e_invoicing_schema['oauth2']['token'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Basic " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $post_fields = "grant_type=client_credentials";
    return post($uri, $headers, $post_fields);
}

/*
 * Supplier Resources
 * Supplier
 * */

function get_supplier(string $supplier_id)
{
    global $e_invoicing_schema;
    $uri = $e_invoicing_schema['suppliers'] . "/${supplier_id}";
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return get($uri, $headers);
}

function create_supplier($payload)
{
    global $e_invoicing_schema;
    $uri = $e_invoicing_schema['suppliers'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

function update_supplier(string $supplier_id, $payload)
{
    global $e_invoicing_schema;
    $uri = $e_invoicing_schema['suppliers'] . "/${supplier_id}";
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return patch($uri, $headers, $json_payload);
}

function delete_supplier(string $supplier_id)
{
    global $e_invoicing_schema;
    $uri = $e_invoicing_schema['suppliers'] . "/${supplier_id}";
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"])
    ];
    return delete($uri, $headers);
}

/*
 * Peppol Resources
 * Peppol Customer Search
 * */

function create_peppol_customer_search($payload)
{
    global $e_invoicing_schema;
    $uri = $e_invoicing_schema['peppol']['customers'];
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

/*
 * Peppol Resources
 * Peppol Invoice
 * */

function get_peppol_invoice(string $supplier_id, string $invoice_id)
{
    global $e_invoicing_schema;
    $uri = configure_path($e_invoicing_schema['peppol']['suppliers']['invoice'], ['supplierId' => $supplier_id, 'invoiceId' => $invoice_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
    ];
    return get($uri, $headers);
}

function create_peppol_invoice(string $supplier_id, string $filename, $payload)
{
    global $e_invoicing_schema;
    $uri = configure_path(remove_path_id($e_invoicing_schema['peppol']['suppliers']['invoice'], 'invoice'), ['supplierId' => $supplier_id]);
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/xml",
        "Content-Disposition: inline; filename=${$filename}"
    ];
    return post($uri, $headers, $payload);
}

/*
 * Peppol Resources
 * Peppol Credit Note
 * */

function get_peppol_credit_note(string $supplier_id, string $credit_note_id)
{
    global $e_invoicing_schema;
    $uri = configure_path($e_invoicing_schema['peppol']['suppliers']['creditNote'], ['supplierId' => $supplier_id, 'creditNoteId' => $credit_note_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
    ];
    return get($uri, $headers);
}

function create_peppol_credit_note(string $supplier_id, string $filename, $payload)
{
    global $e_invoicing_schema;
    $uri = configure_path(remove_path_id($e_invoicing_schema['peppol']['suppliers']['creditNote'], 'invoice'), ['supplierId' => $supplier_id]);
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/xml",
        "Content-Disposition: inline; filename=${$filename}"
    ];
    return post($uri, $headers, $payload);
}

/*
 * Peppol Resources
 * Peppol Document
 * */

function get_peppol_documents(array $parameters)
{
    global $e_invoicing_schema;
    $uri = configure_query_parameters($e_invoicing_schema['peppol']['documents'], $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
    ];
    return get($uri, $headers);
}

/*
 * Zoomit Resources
 * Zoomit Customer Search
 * */

function create_zoomit_customer_search(string $supplier_id, $payload)
{
    global $e_invoicing_schema;
    $uri = configure_path($e_invoicing_schema['zoomit']['suppliers']['customers'], ['supplierId' => $supplier_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/vnd.api+json"
    ];
    $json_payload = json_encode($payload);
    return post($uri, $headers, $json_payload);
}

/*
 * Zoomit Resources
 * Zoomit Invoice
 * */

function get_zoomit_invoice(string $supplier_id, string $invoice_id)
{
    global $e_invoicing_schema;
    $uri = configure_path($e_invoicing_schema['zoomit']['suppliers']['invoice'], ['supplierId' => $supplier_id, 'invoiceId' => $invoice_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
    ];
    return get($uri, $headers);
}

function create_zoomit_invoice(string $supplier_id, string $filename, $payload)
{
    global $e_invoicing_schema;
    $uri = configure_path(remove_path_id($e_invoicing_schema['zoomit']['suppliers']['invoice'], 'invoice'), ['supplierId' => $supplier_id]);
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/xml",
        "Content-Disposition: inline; filename=${$filename}"
    ];
    return post($uri, $headers, $payload);
}

/*
 * Zoomit Resources
 * Zoomit Credit Note
 * */

function get_zoomit_credit_note(string $supplier_id, string $credit_note_id)
{
    global $e_invoicing_schema;
    $uri = configure_path($e_invoicing_schema['zoomit']['suppliers']['creditNote'], ['supplierId' => $supplier_id, 'creditNoteId' => $credit_note_id]);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
    ];
    return get($uri, $headers);
}

function create_zoomit_credit_note(string $supplier_id, string $filename, $payload)
{
    global $e_invoicing_schema;
    $uri = configure_path(remove_path_id($e_invoicing_schema['zoomit']['suppliers']['creditNote'], 'invoice'), ['supplierId' => $supplier_id]);
    $headers = [
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
        "Content-Type: application/xml",
        "Content-Disposition: inline; filename=${$filename}"
    ];
    return post($uri, $headers, $payload);
}

/*
 * Zoomit Resources
 * Zoomit Document
 * */

function get_zoomit_documents(array $parameters)
{
    global $e_invoicing_schema;
    $uri = configure_query_parameters($e_invoicing_schema['zoomit']['documents'], $parameters);
    $headers = [
        "Accept: application/vnd.api+json",
        "Authorization: Bearer " . base64_encode($_ENV["CLIENT_ID"] . ":" . $_ENV["CLIENT_SECRET"]),
    ];
    return get($uri, $headers);
}