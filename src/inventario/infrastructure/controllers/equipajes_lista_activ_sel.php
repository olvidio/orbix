<?php

// INICIO Cabecera global de URL de controlador *********************************
use actividades\model\entity\ActividadAll;
use ubis\model\entity\Ubi;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');

$error_txt = '';

// buscar un nombre (lugar f_ini-f_fin).
$oUbi = Ubi::NewUbi($Qid_cdc);
$nombre_ubi = empty($oUbi->getNombre_ubi()) ? 'sin determinar' : $oUbi->getNombre_ubi();

$a = 0;
$ids_activ = '';
foreach ($a_sel as $id_activ) {
    $ids_activ .= empty($ids_activ) ? '' : ',';
    $ids_activ .= $id_activ;
    $a++;
    $oActividad = new ActividadAll($id_activ);
    $iso_ini = $oActividad->getF_ini()->getIso();
    $aF_ini[$iso_ini] = $oActividad->getF_ini()->getFromLocal();
    $iso_fin = $oActividad->getF_fin()->getIso();
    $aF_fin[$iso_fin] = $oActividad->getF_fin()->getFromLocal();
}
ksort($aF_ini);
$ini = reset($aF_ini);
ksort($aF_fin);
$fin = end($aF_fin);

$data = [
    'nombre_ubi' => $nombre_ubi,
    'ini' => $ini,
    'fin' => $fin,
    'ids_activ' => $ids_activ,
];

// envía una Response
$jsondata = ContestarJson::respuestaPhp($error_txt, $data);
ContestarJson::send($jsondata);
