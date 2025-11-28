<?php

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use web\ContestarJson;

$error_txt = '';

$UbiInventarioRepository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
$a_opciones = $UbiInventarioRepository->getArrayUbisInventario();

$data = [
    'a_opciones' => $a_opciones,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
