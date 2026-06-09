<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

// INICIO Cabecera global de URL de controlador *********************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $parts = explode('#', $a_sel[0]);
    $id_activ = (int)($parts[0] ?? 0);
}

$d = PostRequest::getDataFromUrl('/src/actividadestudios/lista_clases_ca_data', ['id_activ' => $id_activ]);

$msg_err = $d['msg_err'] ?? '';
$nom_activ = $d['nom_activ'] ?? '';
$nom_director_est = $d['nom_director_est'] ?? '';
$datos_asignatura = $d['datos_asignatura'] ?? [];

if (!empty($msg_err)) {
    echo $msg_err;
}

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'datos_asignatura' => $datos_asignatura,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('lista_clases_ca.phtml', $a_campos);
