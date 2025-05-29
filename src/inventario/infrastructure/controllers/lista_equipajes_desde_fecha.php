<?php

use src\inventario\application\repositories\EquipajeRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qf_ini_iso = (string)filter_input(INPUT_POST, 'f_ini_iso');

$error_txt = '';

$EquipajeRepository = new EquipajeRepository();
$aOpciones = $EquipajeRepository->getArrayEquipajes($Qf_ini_iso);

$data = [
    'a_opciones' => $aOpciones,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
