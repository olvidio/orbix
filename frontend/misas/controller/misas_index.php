<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

FrontBootstrap::boot();
// Index estatico: enlaces canonicos `frontend/misas/controller/...` (Slice 11).
// Los `apps/misas/controller/*.php` homonimos siguen como wrappers por enlaces viejos.
$goModificarPlantilla = HashF::link('frontend/misas/controller/modificar_plantilla.php');
$goPrepararPlanDeMisas = HashF::link('frontend/misas/controller/preparar_plan_de_misas.php');
$goModificarPlanDeMisas = HashF::link('frontend/misas/controller/modificar_plan_de_misas.php');
$goVerPlanDeMisas = HashF::link('frontend/misas/controller/ver_plan_de_misas.php');
$goBuscarPlanSacd = HashF::link('frontend/misas/controller/buscar_plan_sacd.php');
$goBuscarPlanCtr = HashF::link('frontend/misas/controller/buscar_plan_ctr.php');
$goModificarEncargos = HashF::link('frontend/misas/controller/modificar_encargos.php');
$goModificarEncargosCtr = HashF::link('frontend/misas/controller/modificar_encargos_centros.php');
$goIniciales = HashF::link('frontend/misas/controller/modificar_iniciales_sacd_zona.php');
$goStatus = HashF::link('frontend/misas/controller/cambiar_status.php');

$a_campos = [
    'goModificarPlantilla' => $goModificarPlantilla,
    'goPrepararPlanDeMisas' => $goPrepararPlanDeMisas,
    'goModificarPlanDeMisas' => $goModificarPlanDeMisas,
    'goVerPlanDeMisas' => $goVerPlanDeMisas,
    'goBuscarPlanSacd' => $goBuscarPlanSacd,
    'goBuscarPlanCtr' => $goBuscarPlanCtr,
    'goModificarEncargos' => $goModificarEncargos,
    'goModificarEncargosCtr' => $goModificarEncargosCtr,
    'goIniciales' => $goIniciales,
    'goStatus' => $goStatus,
];

$oView = new ViewNewPhtml('frontend\\misas\\controller');
$oView->renderizar('misas_index.phtml', $a_campos);
