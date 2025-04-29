<?php

use src\inventario\domain\repositories\EgmRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_grupo = (integer)filter_input(INPUT_POST, 'id_grupo');
$Qid_equipaje = (integer)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

// Nuevo egm:
$EgmRepository = new EgmRepository();
$aWhere = [
    'id_equipaje' => $Qid_equipaje,
    'id_grupo' => $Qid_grupo,
];
$cEgm = $EgmRepository->getEgmes($aWhere);
if (!empty($cEgm)) {
    $oEgm = $cEgm[0];
    if ($EgmRepository->Eliminar($oEgm) === FALSE) {
        $error_txt .= _("hay un error, no se ha eliminado");
        $error_txt .= "\n" . $EgmRepository->getErrorTxt();
    }
    // los docs en whereis deberían eliminarse por la base de datos, al tener una foreign key.
}

ContestarJson::enviar($error_txt, 'ok');

