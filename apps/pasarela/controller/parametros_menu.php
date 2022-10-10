<?php

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************



// ----------- fecha activación -------------------
$url = 'apps/pasarela/controller/activacion_lista.php';
$aQuery = ['que' => 'fecha_activacion'];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_activacion = web\Hash::link($url . '?' . http_build_query($aQuery));

// ----------- nombre actividad -------------------
$url = 'apps/pasarela/controller/nombre_lista.php';
$aQuery = ['que' => 'nombre'];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_nombre = web\Hash::link($url . '?' . http_build_query($aQuery));

// ----------- contribucion no duerme -------------------
$url = 'apps/pasarela/controller/contribucion_no_duerme_lista.php';
$aQuery = ['que' => 'contribucion_no_duerme'];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_contribucion_no_duerme = web\Hash::link($url . '?' . http_build_query($aQuery));

$a_campos = ['oPosicion' => $oPosicion,
    'url_activacion' => $url_activacion,
    'url_nombre' => $url_nombre,
    'url_contribucion_no_duerme' => $url_contribucion_no_duerme,
];

$oView = new core\ViewTwig('pasarela/controller');
echo $oView->render('parametros_menu.html.twig', $a_campos);