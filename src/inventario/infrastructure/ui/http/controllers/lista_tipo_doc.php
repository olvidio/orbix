<?php

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$Repository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
$a_opciones = $Repository->getArrayTipoDoc();

$data = ['a_opciones' => $a_opciones,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
