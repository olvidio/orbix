<?php

// INICIO Cabecera global de URL de controlador *********************************
require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
//

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_propuestas = web\Hash::link('apps/encargossacd/controller/propuestas_lista.php?'.http_build_query($aQuery));

$aQuery = ['que' => 'crear_tabla'];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_new_tabla = web\Hash::link('apps/encargossacd/controller/propuestas_ajax.php?'.http_build_query($aQuery));

$aQuery = ['sf' => 0];
// el hppt_build_query no pasa los valores null
if (is_array($aQuery)) { array_walk($aQuery, 'core\poner_empty_on_null'); }
$url_aprobar = web\Hash::link('apps/encargossacd/controller/propuestas_aprobar.php?'.http_build_query($aQuery));


$a_campos = ['oPosicion' => $oPosicion,
    'url_propuestas' => $url_propuestas,
    'url_new_tabla' => $url_new_tabla,
    'url_aprobar' => $url_aprobar,
];

$oView = new core\ViewTwig('encargossacd/controller');
echo $oView->render('propuestas_menu.html.twig',$a_campos);