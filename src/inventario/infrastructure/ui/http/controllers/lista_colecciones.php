<?php

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use frontend\shared\web\ContestarJson;

$error_txt = '';

$Repository = $GLOBALS['container']->get(ColeccionRepositoryInterface::class);
$a_opciones = $Repository->getArrayColecciones();

$data = [
    'a_opciones' => $a_opciones,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
