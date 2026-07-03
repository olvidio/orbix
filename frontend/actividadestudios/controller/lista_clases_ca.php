<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\actividadestudios\helpers\ListaClasesCaPayload;
use frontend\actividadestudios\helpers\ActividadestudiosPostInput;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = \frontend\shared\helpers\ListNavSupport::stackFromPost();
if ($stackFromPost !== 0 && $oPosicion->goStack($stackFromPost)) {
    $oPosicion->olvidar($stackFromPost);
}

if ($stackFromPost !== 0) {
    \frontend\shared\helpers\ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    \frontend\shared\helpers\ListNavSupport::bootActividadSelectChildRecordar($oPosicion, $Qrefresh);
}
$id_activ = ActividadestudiosPostInput::idFromSel();
\frontend\shared\helpers\ListNavSupport::persistActividadSelectChildEntry(
    $oPosicion,
    $id_activ > 0 ? ['id_activ' => $id_activ] : [],
);

$d = ListaClasesCaPayload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/lista_clases_ca_data', ['id_activ' => $id_activ])));

$msg_err = $d['msg_err'];
$nom_activ = $d['nom_activ'];
$nom_director_est = $d['nom_director_est'];
$datos_asignatura = $d['datos_asignatura'];

if ($msg_err !== '') {
    echo \frontend\shared\helpers\PayloadCoercion::string($msg_err);
}

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'datos_asignatura' => $datos_asignatura,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('lista_clases_ca.phtml', $a_campos);
