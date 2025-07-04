<?php

use src\inventario\application\repositories\UbiInventarioRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$error_txt = '';

$UbiInventarioRepository = new UbiInventarioRepository();
$a_opciones = $UbiInventarioRepository->getArrayUbisInventario();

$data = [
    'a_opciones' => $a_opciones,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
