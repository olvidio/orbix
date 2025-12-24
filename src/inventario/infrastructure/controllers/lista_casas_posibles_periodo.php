<?php

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use web\ContestarJson;
use web\Periodo;

$Qperiodo = (string)filter_input(INPUT_POST, 'periodo');
$Qyear = (int)filter_input(INPUT_POST, 'year');
$Qempiezamin = (string)filter_input(INPUT_POST, 'empiezamin');
$Qempiezamax = (string)filter_input(INPUT_POST, 'empiezamax');
$Qinicio = (string)filter_input(INPUT_POST, 'inicio');
$Qfin = (string)filter_input(INPUT_POST, 'fin');

$error_txt = '';

// miro la sección
$miSfsv = ConfigGlobal::mi_sfsv();

// casa de las actividades organizadas por la dl en el periodo.
$any = empty($Qyear) ? (int)date('Y') + 1 : $Qyear;
$mes = date("m");
if (empty($Qperiodo) || $Qperiodo === 'otro') {
    $inicio = empty($Qinicio) ? $Qempiezamin : $Qinicio;
    $fin = empty($Qfin) ? $Qempiezamax : $Qfin;
} else {
    $oPeriodo = new Periodo();
    $oPeriodo->setAny($any);
    $oPeriodo->setPeriodo($Qperiodo);
    $inicio = $oPeriodo->getF_ini()->getIso();
    $fin = $oPeriodo->getF_fin()->getIso();
}

$aWhere['dl_org'] = ConfigGlobal::mi_dele();
$aWhere['id_tipo_activ'] = "^$miSfsv";
$aOperador['id_tipo_activ'] = "~";
$aWhere['f_ini'] = $fin;
$aOperador['f_ini'] = '<=';
$aWhere['f_fin'] = $inicio;
$aOperador['f_fin'] = '>=';
$aWhere['status'] = 4;
$aOperador['status'] = '<';
$ActividadRepository = $GLOBALS['container']->get(ActividadRepositoryInterface::class);
$aIdUbis = $ActividadRepository->getUbis($aWhere, $aOperador);

$aUbis = [];
foreach ($aIdUbis as $id_ubi) {
    $oUbi = Ubi::NewUbi($id_ubi);
    $nombre_ubi = empty($oUbi->getNombre_ubi()) ? 'sin determinar' : $oUbi->getNombre_ubi();
    $aUbis[$id_ubi] = $nombre_ubi;
}
asort($aUbis);

$data = [
    'a_opciones' => $aUbis,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
