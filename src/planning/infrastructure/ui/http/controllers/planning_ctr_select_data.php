<?php

use frontend\shared\web\Periodo;
use src\planning\application\PlanningCtrSelectData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$error = '';
$data = [];
try {
    $input = $_POST;
    $Qyear = \src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'year');
    $Qperiodo = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'periodo');
    $Qempiezamin = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamin');
    $Qempiezamax = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'empiezamax');

    $oPeriodo = Periodo::conCalendarioDesdeBackend();
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $inicio_iso = $oPeriodo->getF_ini_iso() ?? '';
    $fin_iso = $oPeriodo->getF_fin_iso() ?? '';
    if ($inicio_iso === '' || $fin_iso === '') {
        throw new \RuntimeException(_('Faltan fechas de periodo'));
    }
    $oIniPlanning = new DateTimeLocal($inicio_iso);
    $inicio_local = $oIniPlanning->getFromLocal();

    /** @var PlanningCtrSelectData $useCase */
    $useCase = DependencyResolver::get(PlanningCtrSelectData::class);
    $data = $useCase->execute($input, $oIniPlanning, $inicio_local, $fin_iso, $inicio_iso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
