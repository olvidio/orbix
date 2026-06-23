<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = list_nav_stack_from_post();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

$id_activ = actividadestudios_id_from_sel_post();

if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_ensure_asistentes_dossier_before_actividad_select_child($oPosicion, $id_activ);
    list_nav_boot_actividad_select_child_recordar($oPosicion, $Qrefresh);
}
list_nav_persist_actividad_select_child_entry(
    $oPosicion,
    $id_activ > 0 ? ['id_activ' => $id_activ] : [],
);

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
