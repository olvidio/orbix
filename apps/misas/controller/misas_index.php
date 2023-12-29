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
$goListaSacd = Hash::link('apps/misas/controller/lista_sacd.php?' . http_build_query($aQuery));
$goCrearPlantilla = Hash::link('apps/misas/controller/crear_plantilla.php?' . http_build_query($aQuery));
$goSelectZona = Hash::link('apps/misas/controller/seleccionar_zona.php?' . http_build_query($aQuery));
$goCuadricula = Hash::link('apps/misas/controller/cuadricula_zona.php?' . http_build_query($aQuery));
$goIniciales = Hash::link('apps/misas/controller/tabla_iniciales_sacd.php?' . http_build_query($aQuery));

$a_campos = ['oPosicion' => $oPosicion,
		'goListaSacd' => $goListaSacd,
    'goCrearPlantilla' => $goCrearPlantilla,
    'goSelectZona' => $goSelectZona,
    'goCuadricula' => $goCuadricula,
    'goIniciales' => $goIniciales,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('misas_index.html.twig', $a_campos);