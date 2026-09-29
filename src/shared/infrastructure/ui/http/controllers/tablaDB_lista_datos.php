<?php

namespace src\shared;

use src\shared\domain\DatosTablaRepo;
use src\shared\infrastructure\DatosInfoRepoResolver;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FilterPostGet;

$Qclase_info_encoded = (string)\src\shared\domain\helpers\FilterPostGet::post('clase_info');
$Qk_buscar = (string)\src\shared\domain\helpers\FilterPostGet::post('k_buscar');
$Qpau = (string)\src\shared\domain\helpers\FilterPostGet::post('pau');
$Qid_pau = (integer)\src\shared\domain\helpers\FilterPostGet::post('id_pau');
$Qobj_pau = (integer)\src\shared\domain\helpers\FilterPostGet::post('obj_pau');

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
if (!empty($Qk_buscar)) {
    $oInfoClase->setK_buscar($Qk_buscar);
}
$oDatosTabla->setColeccion($oInfoClase->getColeccion());

// para el id_tabla, convierto los posibles '/' y '\' en '_' y también quito '.php'
$id_tabla = str_replace(array('/', '\\', '.php'), array('_', '_', ''), $obj);
$id_tabla = 'repo_tabla_sql_' . $id_tabla;

$error_txt = '';
$data['a_cabeceras'] = $oDatosTabla->getCabeceras();
$data['a_botones'] = $oDatosTabla->getBotones();
$aValores = $oDatosTabla->getValores();
if (is_array($aValores)) {
    foreach ($aValores as $k => $row) {
        if (!is_array($row) || !isset($row['sel']) || !is_scalar($row['sel'])) {
            continue;
        }
        $aValores[$k]['ctx_eliminar'] = HashB::sign('tablaDB_update', [
            'clase_info' => $Qclase_info_encoded,
            'mod' => 'eliminar',
            's_pkey' => (string) $row['sel'],
            'id_pau' => (string) $Qid_pau,
            'obj_pau' => (string) $Qobj_pau,
        ]);
    }
}
$data['a_valores'] = $aValores;
$data['id_tabla'] = $id_tabla;
$data['script'] = $oDatosTabla->getScript();
$data['titulo'] = $oInfoClase->getTxtTitulo();
$data['explicacion'] = $oInfoClase->getTxtExplicacion();

ContestarJson::enviar($error_txt, $data);