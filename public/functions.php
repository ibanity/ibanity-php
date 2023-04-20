<?php
function dd($value)
{
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

function url_is($value) {
    return $_SERVER['REQUEST_URI'] === $value;
}