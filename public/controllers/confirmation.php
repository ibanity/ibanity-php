<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../src/ponto.php';

use Dotenv\Dotenv;
use function Ibanity\Ponto\request_initial_tokens;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../../config");
$dotenv->safeLoad();

// Title of the page
$heading = 'Ponto Connect - User Linking flow';

session_start();
$code_verifier = $_SESSION['code_verifier'];

$result = json_decode(request_initial_tokens($_GET['code'], $_ENV['CLIENT_ID'], $_ENV['REDIRECT_URI'], $_SESSION['code_verifier']));

require '../views/confirmation.view.php';
