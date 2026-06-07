<?php
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$error_txt = '';

/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
$a_opciones = $UbiInventarioRepository->getArrayUbisInventario();

$data = [
    'a_opciones' => $a_opciones,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
