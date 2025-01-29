<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use web\Hash;

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
$url_activacion = Hash::link($url . '?' . http_build_query($aQuery));

// ----------- nombre actividad -------------------
$url = 'apps/pasarela/controller/nombre_lista.php';
$aQuery = ['que' => 'nombre'];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_nombre = Hash::link($url . '?' . http_build_query($aQuery));

// ----------- contribución no duerme -------------------
$url = 'apps/pasarela/controller/contribucion_no_duerme_lista.php';
$aQuery = ['que' => 'contribucion_no_duerme'];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_contribucion_no_duerme = Hash::link($url . '?' . http_build_query($aQuery));

// ----------- contribución reserva -------------------
$url = 'apps/pasarela/controller/contribucion_reserva_lista.php';
$aQuery = ['que' => 'contribucion_reserva'];
// el hppt_build_query no pasa los valores null
array_walk($aQuery, 'core\poner_empty_on_null');
$url_contribucion_reserva = Hash::link($url . '?' . http_build_query($aQuery));

$a_campos = ['oPosicion' => $oPosicion,
    'url_activacion' => $url_activacion,
    'url_nombre' => $url_nombre,
    'url_contribucion_no_duerme' => $url_contribucion_no_duerme,
    'url_contribucion_reserva' => $url_contribucion_reserva,
];

$oView = new ViewTwig('pasarela/controller');
$oView->renderizar('parametros_menu.html.twig', $a_campos);