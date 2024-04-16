<?php

use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// el hppt_build_query no pasa los valores null

$goModificarPlantilla = Hash::link('apps/misas/controller/modificar_plantilla.php');
$goPrepararPlanDeMisas = Hash::link('apps/misas/controller/preparar_plan_de_misas.php');
$goModificarPlanDeMisas = Hash::link('apps/misas/controller/modificar_plan_de_misas.php');
$goBuscarPlanSacd = Hash::link('apps/misas/controller/buscar_plan_sacd.php');
$goBuscarPlanCtr = Hash::link('apps/misas/controller/buscar_plan_ctr.php');
$goModificarEncargos = Hash::link('apps/misas/controller/modificar_encargos.php');
$goIniciales = Hash::link('apps/misas/controller/modificar_iniciales_sacd_zona.php');

$a_campos = ['oPosicion' => $oPosicion,
    'goModificarPlantilla' => $goModificarPlantilla,
    'goPrepararPlanDeMisas' => $goPrepararPlanDeMisas,
    'goModificarPlanDeMisas' => $goModificarPlanDeMisas,
    'goBuscarPlanSacd' => $goBuscarPlanSacd,
    'goBuscarPlanCtr' => $goBuscarPlanCtr,
    'goModificarEncargos' => $goModificarEncargos,
    'goIniciales' => $goIniciales,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('misas_index.html.twig', $a_campos);