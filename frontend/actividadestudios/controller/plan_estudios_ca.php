<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\actividadestudios\helpers\PlanEstudiosCaPayload;
use frontend\actividadestudios\helpers\ActividadestudiosPostInput;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = ListNavSupport::stackFromPost();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

$id_activ = ActividadestudiosPostInput::idFromSel();

if ($stackFromPost !== 0) {
    ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    ListNavSupport::ensureAsistentesDossierBeforeActividadSelectChild($oPosicion, $id_activ);
    ListNavSupport::bootActividadSelectChildRecordar($oPosicion, $Qrefresh);
}
ListNavSupport::persistActividadSelectChildEntry(
    $oPosicion,
    $id_activ > 0 ? ['id_activ' => $id_activ] : [],
);

$d = PlanEstudiosCaPayload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/plan_estudios_ca_data', ['id_activ' => $id_activ])));

$msg_err = $d['msg_err'];
$nom_activ = $d['nom_activ'];
$nom_director_est = $d['nom_director_est'];
$aPreceptores = $d['aPreceptores'];
$aProfesores = $d['aProfesores'];
$aAlumnos = $d['aAlumnos'];

if ($msg_err !== '') {
    echo PayloadCoercion::string($msg_err);
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
