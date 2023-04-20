<?php

namespace Ibanity\Helpers\HttpSignature;

use Dotenv\Dotenv;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\RSA;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../config");
$dotenv->safeLoad();

function generate_http_signature_headers($payload, $path, $method, $optional_headers, $key_id): array
{
    $created = time();
    $digest = "SHA-512=" . base64_encode(hash('sha512', $payload, true));
    $request_target = strtolower($method) . " http-signature.php" . $path;
    $signing_string = "(request-target): $request_target\nhost: api.ibanity.com\ndigest: $digest\n(created): $created\n";
    $signed_headers = "(request-target) host digest (created)";
    $signing_strings = formatter($optional_headers, $signing_string, $signed_headers);
    $signature = base64_encode(rsa_signing($signing_strings['signing_string']));
    $signature_header = "keyId=\"$key_id\",created=$created,algorithm=\"hs2019\",headers=\"{$signing_strings['signed_headers']}\",signature=\"$signature\"";
    return array('digest' => $digest, 'signature' => $signature_header);
}

function formatter($optional_headers, $signing_string, $signed_headers): array
{
    foreach ($optional_headers as $key => $value) {
        if (end($optional_headers) === $value) {
            $signing_string .= $key . ": " . $value;
            $signed_headers .= " " . $key;
        } else {
            $signing_string .= $key . ": " . $value . "\n";
            $signed_headers .= " " . $key . ", ";
        }
    }
    return array('signing_string' => $signing_string, 'signed_headers' => $signed_headers);
}

function rsa_signing($signing_string): bool|string
{
    $private_key = PublicKeyLoader::loadPrivateKey(file_get_contents('private_key.pem'), $_ENV["SSL_PASSWORD"]);
    return RSA::createKey()->
    loadPrivateKey($private_key, $_ENV["SSL_PASSWORD"])->
    withHash('sha256')->
    withMGFHash('sha256')->
    withSaltLength(32)->
    sign($signing_string);
}