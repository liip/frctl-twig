<?php

require __DIR__ . '/../../vendor/autoload.php';

function render($entry, $options = array())
{
    return \Frctl\Twig::render($entry, $options);
}
