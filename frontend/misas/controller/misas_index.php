<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;

require_once("frontend/shared/global_header_front.inc");

// Index estatico: enlaces canonicos `frontend/misas/controller/...` (Slice 11).
// Los `apps/misas/controller/*.php` homonimos siguen como wrappers por enlaces viejos.
$goModificarPlantilla = HashFront::link('frontend/misas/controller/modificar_plantilla.php');
$goPrepararPlanDeMisas = HashFront::link('frontend/misas/controller/preparar_plan_de_misas.php');
$goModificarPlanDeMisas = HashFront::link('frontend/misas/controller/modificar_plan_de_misas.php');
$goVerPlanDeMisas = HashFront::link('frontend/misas/controller/ver_plan_de_misas.php');
$goBuscarPlanSacd = HashFront::link('frontend/misas/controller/buscar_plan_sacd.php');
$goBuscarPlanCtr = HashFront::link('frontend/misas/controller/buscar_plan_ctr.php');
$goModificarEncargos = HashFront::link('frontend/misas/controller/modificar_encargos.php');
$goModificarEncargosCtr = HashFront::link('frontend/misas/controller/modificar_encargos_centros.php');
$goIniciales = HashFront::link('frontend/misas/controller/modificar_iniciales_sacd_zona.php');
$goStatus = HashFront::link('frontend/misas/controller/cambiar_status.php');

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
