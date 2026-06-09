<?php

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/actividadestudios_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$oPosicion->recordar();

$id_activ = actividadestudios_id_from_sel_post();

$d = actividadestudios_lista_clases_ca_from_payload(actividadestudios_post_data(PostRequest::getDataFromUrl('/src/actividadestudios/lista_clases_ca_data', ['id_activ' => $id_activ])));

$msg_err = $d['msg_err'];
$nom_activ = $d['nom_activ'];
$nom_director_est = $d['nom_director_est'];
$datos_asignatura = $d['datos_asignatura'];

if ($msg_err !== '') {
    actividadestudios_echo_string($msg_err);
}

$a_campos = ['oPosicion' => $oPosicion,
    'nom_activ' => $nom_activ,
    'nom_director_est' => $nom_director_est,
    'datos_asignatura' => $datos_asignatura,
];

$oView = new ViewNewPhtml('frontend\\actividadestudios\\controller');
$oView->renderizar('lista_clases_ca.phtml', $a_campos);
