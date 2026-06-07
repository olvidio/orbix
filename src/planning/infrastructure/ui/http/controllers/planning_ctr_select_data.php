<?php

use frontend\shared\web\Periodo;
use src\planning\application\PlanningCtrSelectData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

$error = '';
$data = [];
try {
    $input = $_POST;
    $Qyear = input_int($input, 'year');
    $Qperiodo = input_string($input, 'periodo');
    $Qempiezamin = input_string($input, 'empiezamin');
    $Qempiezamax = input_string($input, 'empiezamax');

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
