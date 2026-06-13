<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$id_activ = actividadestudios_id_from_sel_post();

$d = actividadestudios_plan_estudios_ca_from_payload(actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/plan_estudios_ca_data', ['id_activ' => $id_activ])));

$msg_err = $d['msg_err'];
$nom_activ = $d['nom_activ'];
$nom_director_est = $d['nom_director_est'];
$aPreceptores = $d['aPreceptores'];
$aProfesores = $d['aProfesores'];
$aAlumnos = $d['aAlumnos'];

if ($msg_err !== '') {
    actividadestudios_echo_string($msg_err);
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
