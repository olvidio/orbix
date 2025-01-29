<?php
// INICIO Cabecera global de URL de controlador *********************************

use web\DateTimeLocal;
use tablonanuncios\domain\AnuncioId;
use tablonanuncios\domain\repositories\AnuncioRepository;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// El delete es via POST!!!";

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Quuid_item = (string)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Quuid_item = (string)filter_input(INPUT_POST, 'uuid_item');
}

$error_txt = '';
if (!empty($Quuid_item)) {
    $uuid_item = new AnuncioId($Quuid_item);
    $AnuncioRepository = new AnuncioRepository();
    $oAnuncio = $AnuncioRepository->findById($uuid_item);
    if (!empty($oAnuncio)) {
        $oAnuncio->setTeliminado(new DateTimeLocal());
        if ($AnuncioRepository->Guardar($oAnuncio) === FALSE) {
            $error_txt .= _("error al borrar el anuncio");
        }
    }
} else {
    $error_txt = _("No se encuentra el anuncio");
}

if (!empty($error_txt)) {
    $jsondata['success'] = FALSE;
    $jsondata['mensaje'] = $error_txt;
} else {
    $jsondata['success'] = TRUE;
}
//Aunque el content-type no sea un problema en la mayoría de casos, es recomendable especificarlo
header('Content-type: application/json; charset=utf-8');
echo json_encode($jsondata);
exit();