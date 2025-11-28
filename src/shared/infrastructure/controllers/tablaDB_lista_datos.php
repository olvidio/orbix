<?php

namespace src\shared;

use src\shared\domain\DatosTablaRepo;
use web\ContestarJson;

$Qclase_info = (string)filter_input(INPUT_POST, 'clase_info');
$Qk_buscar = (string)filter_input(INPUT_POST, 'k_buscar');
$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (integer)filter_input(INPUT_POST, 'id_pau');

// Tiene que ser en dos pasos.
$obj = urldecode($Qclase_info);
$oInfoClase = new $obj();

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
$data['a_valores'] = $oDatosTabla->getValores();
$data['id_tabla'] = $id_tabla;
$data['script'] = $oDatosTabla->getScript();
$data['titulo'] = $oInfoClase->getTxtTitulo();
$data['explicacion'] = $oInfoClase->getTxtExplicacion();

ContestarJson::enviar($error_txt, $data);