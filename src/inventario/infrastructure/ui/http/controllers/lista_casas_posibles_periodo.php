<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\ubis\domain\entity\Ubi;
use src\shared\web\ContestarJson;
use frontend\shared\web\Periodo;

$Qperiodo = input_string($_POST, 'periodo');
$Qyear = input_int($_POST, 'year');
$Qempiezamin = input_string($_POST, 'empiezamin');
$Qempiezamax = input_string($_POST, 'empiezamax');
$Qinicio = input_string($_POST, 'inicio');
$Qfin = input_string($_POST, 'fin');

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
/** @var ActividadRepositoryInterface $ActividadRepository */
$ActividadRepository = DependencyResolver::get(ActividadRepositoryInterface::class);
$aIdUbis = $ActividadRepository->getUbis($aWhere, $aOperador);

$aUbis = [];
foreach ($aIdUbis as $id_ubi) {
    if ($id_ubi === null) {
        continue;
    }
    $oUbi = Ubi::NewUbi($id_ubi);
    $nombre_ubi = $oUbi?->getNombreUbiVo()?->value()?? 'sin determinar';
    $aUbis[$id_ubi] = $nombre_ubi;
}
asort($aUbis);

$data = [
    'a_opciones' => $aUbis,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
