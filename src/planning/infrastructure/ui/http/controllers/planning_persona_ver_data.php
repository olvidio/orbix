<?php

use src\shared\web\ContestarJson;
use frontend\shared\web\Periodo;
use src\planning\application\PlanningPersonaVerData;

$error = '';
$data = [];
try {
    $post = $_POST;
    $a_sel = (array)($post['sel'] ?? []);
    $aid_nom = [];
    if (!empty($a_sel)) {
        if (count($a_sel) > 1) {
            foreach ($a_sel as $nom_sel) {
                $aid_nom[] = (string)$nom_sel;
            }
        } else {
            $aid_nom[] = (string)$a_sel[0];
        }
    }

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

    $data = PlanningPersonaVerData::execute($post, $aid_nom, $oIniPlanning, $inicio_local, $fin_iso, $inicio_iso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
