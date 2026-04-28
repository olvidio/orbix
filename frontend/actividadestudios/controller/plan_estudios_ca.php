<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;

// INICIO Cabecera global de URL de controlador *********************************
require_once("frontend/shared/global_header_front.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $parts = explode('#', $a_sel[0]);
    $id_activ = (int)($parts[0] ?? 0);
}

$campos = ['id_activ' => $id_activ];
$d = PostRequest::getDataFromUrl('/src/actividadestudios/plan_estudios_ca_data', $campos);

$msg_err = $d['msg_err'] ?? '';
$nom_activ = $d['nom_activ'] ?? '';
$nom_director_est = $d['nom_director_est'] ?? '';
$aPreceptores = $d['aPreceptores'] ?? [];
$aProfesores = $d['aProfesores'] ?? [];
$aAlumnos = $d['aAlumnos'] ?? [];

if (!empty($msg_err)) {
    echo $msg_err;
}

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'aPreceptores' => $aPreceptores,
    'aProfesores' => $aProfesores,
    'aAlumnos' => $aAlumnos,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('plan_estudios_ca.phtml', $a_campos);
