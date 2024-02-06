<?php

use web\Hash;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$aQuery = [ 'pau' => 'a',
    'id_zona' => 3,
    ];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) {
    array_walk($aQuery, 'core\poner_empty_on_null');
}

$goModificarPlantilla = Hash::link('apps/misas/controller/modificar_plantilla.php?' . http_build_query($aQuery));
$goPrepararPlanDeMisas = Hash::link('apps/misas/controller/preparar_plan_de_misas.php?' . http_build_query($aQuery));
$goModificarPlanDeMisas = Hash::link('apps/misas/controller/modificar_plan_de_misas.php?' . http_build_query($aQuery));
$goModificarEncargosZona = Hash::link('apps/misas/controller/modificar_encargos_zona.php?' . http_build_query($aQuery));
$goIniciales = Hash::link('apps/misas/controller/tabla_iniciales_sacd.php?' . http_build_query($aQuery));

$a_campos = ['oPosicion' => $oPosicion,
    'goModificarPlantilla' => $goModificarPlantilla,
    'goPrepararPlanDeMisas' => $goPrepararPlanDeMisas,
    'goModificarPlanDeMisas' => $goModificarPlanDeMisas,
    'goModificarEncargosZona' => $goModificarEncargosZona,
    'goIniciales' => $goIniciales,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('misas_index.html.twig', $a_campos);