<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\actividades\domain\contracts\ActividadAllRepositoryInterface;
use src\shared\domain\value_objects\DateTimeLocal;
use src\ubis\domain\entity\Ubi;
use src\shared\web\ContestarJson;

$a_sel = (array)filter_post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_cdc = input_int($_POST, 'id_cdc');

$error_txt = '';

// buscar un nombre (lugar f_ini-f_fin).
$oUbi = Ubi::NewUbi($Qid_cdc);
$nombreUbiVo = $oUbi?->getNombreUbiVo();
$nombre_ubi = ($nombreUbiVo === null || empty($nombreUbiVo->value())) ? 'sin determinar' : $nombreUbiVo->value();

$a = 0;
$aF_ini = [];
$aF_fin = [];
$ids_activ = '';
/** @var ActividadAllRepositoryInterface $ActividadAllRepository */
$ActividadAllRepository = DependencyResolver::get(ActividadAllRepositoryInterface::class);
foreach ($a_sel as $id_activ_raw) {
    if (!is_numeric($id_activ_raw)) {
        continue;
    }
    $id_activ = (int) $id_activ_raw;
    $ids_activ .= empty($ids_activ) ? '' : ',';
    $ids_activ .= $id_activ;
    $a++;
    $oActividad = $ActividadAllRepository->findById($id_activ);
    if ($oActividad === null) {
        continue;
    }
    $fIni = $oActividad->getF_ini();
    $fFin = $oActividad->getF_fin();
    if (!$fIni instanceof DateTimeLocal || !$fFin instanceof DateTimeLocal) {
        continue;
    }
    $iso_ini = $fIni->getIso();
    $aF_ini[$iso_ini] = $fIni->getFromLocal();
    $iso_fin = $fFin->getIso();
    $aF_fin[$iso_fin] = $fFin->getFromLocal();
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
