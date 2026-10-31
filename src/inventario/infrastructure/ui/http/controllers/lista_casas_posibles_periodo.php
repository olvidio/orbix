<?php

use src\shared\infrastructure\DependencyResolver;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\actividades\domain\criteria\ActividadAllField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\ubis\domain\entity\Ubi;
use src\shared\web\ContestarJson;
use frontend\shared\web\Periodo;

$Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'periodo');
$Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'year');
$Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamin');
$Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'empiezamax');
$Qinicio = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'inicio');
$Qfin = \src\shared\domain\helpers\FuncTablasSupport::inputString($_POST, 'fin');

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

$criteria = Criteria::create()
    ->where(Filter::eq(ActividadAllField::DlOrg, ConfigGlobal::mi_dele()))
    ->andWhere(Filter::matches(ActividadAllField::IdTipoActiv, "^$miSfsv"))
    ->andWhere(Filter::lte(ActividadAllField::FIni, $fin))
    ->andWhere(Filter::gte(ActividadAllField::FFin, $inicio))
    ->andWhere(Filter::lt(ActividadAllField::Status, 4));
/** @var ActividadRepositoryInterface $ActividadRepository */
$ActividadRepository = DependencyResolver::get(ActividadRepositoryInterface::class);
$aIdUbis = $ActividadRepository->getUbis($criteria);

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
