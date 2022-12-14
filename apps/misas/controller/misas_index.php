<?php


// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$a_campos = ['oPosicion' => $oPosicion,
		
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('misas_index.html.twig', $a_campos);