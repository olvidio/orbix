<?php

use frontend\asistentes\helpers\AsistentesPayload;
use frontend\asistentes\helpers\AsistentesPostInput;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();

$idActiv = AsistentesPostInput::idFromSelPost('id_pau');
if ($idActiv === 0) {
    $idActiv = AsistentesPostInput::idFromSelPost('id_activ');
}

$navState = [];
$aSel = ListNavSupport::selFromPost();
if ($aSel !== []) {
    $navState['sel'] = $aSel;
}
foreach (['queSel', 'mod', 'obj_pau', 'pau', 'permiso', 'listar_asistentes'] as $key) {
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

$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $idActiv > 0 ? ['id_activ' => $idActiv] : [],
    $navState,
);

$campos = array_merge($_GET, $_POST);
$payload = AsistentesPayload::postData(PostRequest::getDataFromUrl('/src/asistentes/lista_asistentes_data', $campos));

$a_campos = array_merge($payload, ['oPosicion' => $oPosicion]);

(new ViewNewPhtml('frontend\\asistentes\\controller'))
    ->renderizar('lista_asistentes.phtml', $a_campos);
