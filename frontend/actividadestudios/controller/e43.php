<?php

use frontend\shared\helpers\PayloadCoercion;
use frontend\actividadestudios\helpers\E43Payload;
use frontend\actividadestudios\helpers\ActividadestudiosPostInput;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');
$Qid_nom = ActividadestudiosPostInput::idNom()['id_nom'];

$navState = ListNavSupport::buildE43ParentReturnParametros();
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['id_nom' => $Qid_nom, 'id_activ' => $Qid_activ],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildE43ParentReturnParametros());

$d = E43Payload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/e43_data', [
    'id_nom' => $Qid_nom,
    'id_activ' => $Qid_activ,
])));
$msg_err = $d['msg_err'];
$nom = $d['nom'];
$txt_nacimiento = $d['txt_nacimiento'];
$dl_origen = $d['dl_origen'];
$dl_destino = $d['dl_destino'];
$txt_actividad = $d['txt_actividad'];
$matriculas = $d['matriculas'];
$aAsignaturasMatriculadas = $d['aAsignaturasMatriculadas'];

$oHash = new HashFront();
$oHash->setUrl(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/actividadestudios/controller/e43_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_activ');
$h = $oHash->linkSinVal();

if ($msg_err !== '') {
    echo \frontend\shared\helpers\PayloadCoercion::string($msg_err . '<br><br>');
}

$a_campos = ['oPosicion' => $oPosicion,
    'id_nom' => $Qid_nom,
    'h' => $h,
    'id_activ' => $Qid_activ,
    'dl_destino' => $dl_destino,
    'dl_origen' => $dl_origen,
    'nom' => $nom,
    'txt_nacimiento' => $txt_nacimiento,
    'txt_actividad' => $txt_actividad,
    'matriculas' => $matriculas,
    'aAsignaturasMatriculadas' => $aAsignaturasMatriculadas,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('e43.phtml', $a_campos);
