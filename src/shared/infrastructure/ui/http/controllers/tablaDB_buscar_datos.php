<?php

namespace src\shared;

use src\shared\domain\DatosTablaRepo;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\web\ContestarJson;

$Qclase_info_encoded = (string)filter_post('clase_info');
$Qk_buscar = (string)filter_post('k_buscar');
$QaSerieBuscar = (string)filter_post('aSerieBuscar');
$Qpau = (string)filter_post('pau');
$Qid_pau = (integer)filter_post('id_pau');
$Qobj_pau = (string)filter_post('obj_pau');

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info_encoded);
$oInfoClase = DatosInfoRepoResolver::resolve($obj);
if (method_exists($oInfoClase, 'setObj_pau')) {
    $oInfoClase->setObj_pau($Qobj_pau);
}
$oInfoClase->setPau($Qpau);
$oInfoClase->setId_pau($Qid_pau);

$oDatosTabla = new DatosTablaRepo();
$oDatosTabla->setExplicacion_txt($oInfoClase->getTxtExplicacion());
$oDatosTabla->setEliminar_txt($oInfoClase->getTxtEliminar());
$oInfoClase->setK_buscar($Qk_buscar);
$oDatosTabla->setColeccion($oInfoClase->getColeccion());

$camposForm = $oInfoClase->addCamposFormBuscar();

$a_campos = [
    'script' => $oDatosTabla->getScript(),
    'txt_buscar' => $oInfoClase->getTxtBuscar(),
    'k_buscar' => $Qk_buscar,
    'camposForm' => $camposForm,
];

$data['a_campos'] = $oInfoClase->addCampos($a_campos);
if (!empty($oInfoClase->getBuscar_view())) {
    $data['buscar_view'] = $oInfoClase->getBuscar_view();
    $data['namespace_view'] = $oInfoClase->getBuscar_namespace();
}

$error_txt = '';

ContestarJson::enviar($error_txt, $data);