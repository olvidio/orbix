<?php

use frontend\shared\web\ContestarJson;
use frontend\shared\web\Periodo;
use src\planning\application\PlanningCtrSelectData;

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
    $oIniPlanning = $oPeriodo->getF_ini();
    $inicio_local = $oIniPlanning->getFromLocal();

    $data = PlanningCtrSelectData::execute($post, $oIniPlanning, $inicio_local, $fin_iso, $inicio_iso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
