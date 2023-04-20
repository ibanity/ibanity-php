<?php

namespace Ibanity\Schema;

require_once 'helpers/http-verbs.php';

use function Ibanity\Helpers\HttpVerbs\get;

function api_schema($product)
{
    return json_decode(get($product), true)['links'];
}

function get_ponto_connect(): array
{
    return api_schema('ponto-connect');
}

function get_xs2a(): array
{
    return api_schema('xs2a');
}

function get_isabel_connect(): array
{
    return api_schema('isabel-connect');
}

function get_einvoicing(): array
{
    return api_schema('einvoicing');
}

function get_codabox(): array
{
    return api_schema('codabox-connect');
}