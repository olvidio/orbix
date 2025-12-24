<?php

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use web\ContestarJson;

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_cdc = (int)filter_input(INPUT_POST, 'id_cdc');

$error_txt = '';

// buscar un nombre (lugar f_ini-f_fin).
$oUbi = Ubi::NewUbi($Qid_cdc);
$nombre_ubi = empty($oUbi->getNombre_ubi()) ? 'sin determinar' : $oUbi->getNombre_ubi();

$a = 0;
$ids_activ = '';
$ActividadAllRepository = $GLOBALS['container']->get(ActividadAllRepositoryInterface::class);
foreach ($a_sel as $id_activ) {
    $ids_activ .= empty($ids_activ) ? '' : ',';
    $ids_activ .= $id_activ;
    $a++;
    $oActividad = $ActividadAllRepository->findById($id_activ);
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
ContestarJson::enviar($error_txt, $data);
