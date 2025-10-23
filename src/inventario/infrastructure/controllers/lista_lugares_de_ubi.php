<?php

// INICIO Cabecera global de URL de controlador *********************************
use src\inventario\application\repositories\LugarRepository;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$error_txt = '';


$LugarRepository = new LugarRepository();
$cLugares = $LugarRepository->getLugares(['id_ubi' => $Qid_ubi, '_ordre' => 'nom_lugar']);
$a = 0;
$a_valores = [];
foreach ($cLugares as $oLugar) {
    $a++;
    $id_lugar = $oLugar->getId_lugar();
    $a_valores[] = ['value' => $id_lugar, 'text' => $oLugar->getNom_lugar()];
}

// envía una Response
ContestarJson::enviar($error_txt, $a_valores);