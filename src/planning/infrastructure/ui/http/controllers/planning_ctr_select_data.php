<?php

use src\shared\web\ContestarJson;
use frontend\shared\web\Periodo;
use src\planning\application\PlanningCtrSelectData;
use src\shared\domain\value_objects\DateTimeLocal;

$error = '';
$data = [];
try {
    $post = $_POST;
    $Qyear = (int)($post['year'] ?? 0);
    $Qperiodo = (string)($post['periodo'] ?? '');
    $Qempiezamin = (string)($post['empiezamin'] ?? '');
    $Qempiezamax = (string)($post['empiezamax'] ?? '');

    $oPeriodo = Periodo::conCalendarioDesdeBackend();
    $oPeriodo->setDefaultAny('next');
    $oPeriodo->setAny($Qyear);
    $oPeriodo->setEmpiezaMin($Qempiezamin);
    $oPeriodo->setEmpiezaMax($Qempiezamax);
    $oPeriodo->setPeriodo($Qperiodo);

    $inicio_iso = $oPeriodo->getF_ini_iso();
    $fin_iso = $oPeriodo->getF_fin_iso();
    $oIniPlanning = new DateTimeLocal($inicio_iso);
    $inicio_local = $oIniPlanning->getFromLocal();

    $data = PlanningCtrSelectData::execute($post, $oIniPlanning, $inicio_local, $fin_iso, $inicio_iso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
