<?php

use Dotenv\Dotenv;

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../../config");
$dotenv->safeLoad();

function generate_code_verifier($length): string
{
    $values = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-._~";
    $result = "";
    for ($i = 0; $i < $length; $i++) {
        $result .= substr($values, random_int(0, strlen($values)), 1);
    }
    return $result;
}

function hash_sha256($input): string
{
    return hash("sha256", $input, true, []);
}

function base64_urlencoded($input): string
{
    return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
}

function generate_code_challenge($code_verifier): string
{
    return base64_urlencoded(hash_sha256($code_verifier));
}

session_start();

$length = random_int(43, 128);
$code_verifier = generate_code_verifier($length);
$code_challenge = generate_code_challenge($code_verifier);

$_SESSION['code_verifier'] = $code_verifier;

$user_linking_attributes = [
    'client_id' => $_ENV['CLIENT_ID'],
    'redirect_uri' => $_ENV['REDIRECT_URI'],
    'response_type' => 'code',
    'scope' => 'ai pi name offline_access',
    'state' => 'sandbox-teste',
    'code_challenge' => $code_challenge,
    'code_challenge_method' => 'S256',
];

$uri = 'https://sandbox-authorization.myponto.com/oauth2/auth?' . http_build_query($user_linking_attributes);

// Title of the page
$heading = 'Ponto Connect - User Linking flow';

require 'views/index.view.php';
