<?php

namespace frontend\personas\controller;

use frontend\personas\helpers\PersonasPayload;
use frontend\personas\helpers\PersonasPostInput;
use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

/**
 * Formulario para publicar una persona hacia otras DL (caso B).
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$ids = PersonasPostInput::idFromSelPost();
$id_nom = $ids['id_nom'];
$id_tabla = $ids['id_tabla'];

$navIdentity = $id_nom > 0 ? ['id_nom' => $id_nom] : [];
$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    array_merge(
        ListNavSupport::buildPersonasSelectReturnParametros(),
        ListNavSupport::buildSelectionStatePatchFromPost(),
    ),
);

$campos = [
    'id_nom' => $id_nom,
    'id_tabla' => $id_tabla,
];

$data = PostRequest::getDataFromUrl('/src/personas/persona_publicar_form_data', $campos);
$payload = PersonasPayload::postPayload($data);
$view = PersonasPayload::personaPublicarFromPayload($payload);

if (($view['error'] ?? '') !== '') {
    echo htmlspecialchars((string) $view['error'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    return;
}

$nom = $view['nom'];
$id_schema = $view['id_schema'];
$opciones = $view['opciones_dl'];

$oDespl = new Desplegable();
$oDespl->setNombre('dl');
$oDespl->setOpciones($opciones);
$oDespl->setBlanco(true);

$oHash = new HashFront();
$oHash->setCamposForm('dl');
$oHash->setArraycamposHidden([
    'id_tabla' => $id_tabla,
    'id_nom' => $id_nom,
    'id_schema' => $id_schema,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nom' => $nom,
    'oDespl' => $oDespl,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('persona_publicar.phtml', $a_campos);
