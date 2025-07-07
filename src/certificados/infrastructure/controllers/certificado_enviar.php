<?php

// INICIO Cabecera global de URL de controlador *********************************
use src\certificados\domain\CertificadoEnviar;
use web\ContestarJson;

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
}

$error_txt = CertificadoEnviar::enviar($Qid_item);

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, 'ok');
ContestarJson::send($jsondata);