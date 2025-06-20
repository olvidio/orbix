<?php

namespace src\shared;

use core\ConfigGlobal;
use src\shared\domain\DatosTablaRepo;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$Qk_buscar = (string)filter_input(INPUT_POST, 'k_buscar');
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

// Tiene que ser en dos pasos.
$obj = $Qclase_info;
$oInfoClase = new $obj();

$oInfoClase->setPau($Qpau);
$oInfoClase->setId_pau($Qid_pau);
$oInfoClase->setObj_pau($Qobj_pau);

$oDatosTabla = new DatosTablaRepo();
$oDatosTabla->setExplicacion_txt($oInfoClase->getTxtExplicacion());
$oDatosTabla->setEliminar_txt($oInfoClase->getTxtEliminar());
$oInfoClase->setK_buscar($Qk_buscar);
$oDatosTabla->setColeccion($oInfoClase->getColeccion());


$url = ConfigGlobal::getWeb() . "/frontend/shared/controller/tablaDB_lista_datos.php";
$a_campos = [
    'oPosicion' => $oPosicion,
    'script' => $oDatosTabla->getScript(),
    'url' => $url,
    'txt_buscar' => $oInfoClase->getTxtBuscar(),
    'k_buscar' => $Qk_buscar,
];

//$data['a_campos'] = $oInfoClase->addCampos($a_campos);
$data['a_campos'] = $a_campos;
if (!empty($oInfoClase->getBuscar_view())) {
    $data['datos_buscar'] = $oInfoClase->getBuscar_view();
    $data['namespace'] = $oInfoClase->getBuscar_namespace();
}

$error_txt = '';

ContestarJson::enviar($error_txt, $data);