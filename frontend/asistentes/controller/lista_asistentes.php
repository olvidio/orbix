<?php

use frontend\asistentes\helpers\AsistentesPayload;
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
\frontend\shared\helpers\ListNavSupport::persistActividadSelectChildEntry($oPosicion);


$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_asistentes_data', $campos));

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('lista_asistentes.phtml', $a_campos);
