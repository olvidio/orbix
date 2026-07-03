<?php

use frontend\shared\web\Periodo;
use src\planning\application\PlanningPersonaVerData;
use src\shared\domain\value_objects\DateTimeLocal;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;
use src\shared\domain\helpers\FuncTablasSupport;

$error = '';
$data = [];
try {
    $input = $_POST;
    $sSeleccionados = FuncTablasSupport::inputString($input, 'sSeleccionados');
    if ($sSeleccionados !== '') {
        $aid_nom = array_values(array_filter(
            array_map('trim', explode(',', $sSeleccionados)),
            static fn (string $v): bool => $v !== ''
        ));
    } else {
        $aid_nom = FuncTablasSupport::inputStringList($input, 'sel');
    }

    $Qyear = FuncTablasSupport::inputInt($input, 'year');
    $Qperiodo = FuncTablasSupport::inputString($input, 'periodo');
    $Qempiezamin = FuncTablasSupport::inputString($input, 'empiezamin');
    $Qempiezamax = FuncTablasSupport::inputString($input, 'empiezamax');

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

    /** @var PlanningPersonaVerData $useCase */
    $useCase = DependencyResolver::get(PlanningPersonaVerData::class);
    $data = $useCase->execute($input, $aid_nom, $oIniPlanning, $inicio_local, $fin_iso, $inicio_iso);
} catch (\Throwable $e) {
    $error = $e->getMessage();
}

ContestarJson::enviar($error, $data);
