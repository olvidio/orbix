<?php

/**
 * Casas con actividades de la dl en un periodo, para preparar un equipaje.
 * La lógica está en {@see \src\inventario\application\EquipajeActividadesData::casasConActividades()};
 * aquí solo se resuelve el rango de fechas del periodo.
 */

use frontend\shared\web\Periodo;
use src\inventario\application\EquipajeActividadesData;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\DependencyResolver;
use src\shared\web\ContestarJson;

$Qperiodo = FuncTablasSupport::inputString($_POST, 'periodo');
$Qyear = FuncTablasSupport::inputInt($_POST, 'year');
$Qinicio = FuncTablasSupport::inputString($_POST, 'inicio');
$Qfin = FuncTablasSupport::inputString($_POST, 'fin');

if ($Qperiodo === '' || $Qperiodo === 'otro') {
    $inicio = $Qinicio !== '' ? $Qinicio : FuncTablasSupport::inputString($_POST, 'empiezamin');
    $fin = $Qfin !== '' ? $Qfin : FuncTablasSupport::inputString($_POST, 'empiezamax');
    if ($inicio === '' || $fin === '') {
        ContestarJson::enviar(sprintf(_("Faltan parámetros: %s"), 'periodo / empiezamin / empiezamax'), 'none');
        return;
    }
} else {
    $oPeriodo = new Periodo();
    $oPeriodo->setAny(empty($Qyear) ? (int) date('Y') + 1 : $Qyear);
    $oPeriodo->setPeriodo($Qperiodo);
    $inicio = $oPeriodo->getF_ini()->getIso();
    $fin = $oPeriodo->getF_fin()->getIso();
}

/** @var EquipajeActividadesData $useCase */
$useCase = DependencyResolver::get(EquipajeActividadesData::class);
ContestarJson::enviar('', $useCase->casasConActividades($inicio, $fin));
