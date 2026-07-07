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

$id_activ = ActividadestudiosPostInput::idFromSel();

$navState = [];
$aSel = ListNavSupport::selFromPost();
if ($aSel !== []) {
    $navState['sel'] = $aSel;
}
foreach (['queSel', 'mod', 'obj_pau', 'pau', 'permiso'] as $key) {
    $raw = filter_input(INPUT_POST, $key);
    if (is_scalar($raw) && (string) $raw !== '') {
        $navState[$key] = (string) $raw;
    }
}
$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    $navState,
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
if ($id_activ > 0) {
    $navState['id_activ'] = $id_activ;
}

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $id_activ > 0 ? ['id_activ' => $id_activ] : [],
    $navState,
);
ListNavSupport::syncActividadSelectParentSelection($oPosicion);

$d = ListaClasesCaPayload::fromPayload(ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/lista_clases_ca_data', ['id_activ' => $id_activ])));

$msg_err = $d['msg_err'];
$nom_activ = $d['nom_activ'];
$nom_director_est = $d['nom_director_est'];
$datos_asignatura = $d['datos_asignatura'];

if ($msg_err !== '') {
    echo PayloadCoercion::string($msg_err);
}

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'datos_asignatura' => $datos_asignatura,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('lista_clases_ca.phtml', $a_campos);
