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

/**
 * Formulario para cambiar el `nivel_stgr` de una persona.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());

ListNavSupport::persistSelectionToPosicion($oPosicion, 1);

$ids = PersonasPostInput::idFromSelPost();
$id_nom = $ids['id_nom'];
$id_tabla = $ids['id_tabla'];

$campos = [
    'id_nom' => $id_nom,
    'id_tabla' => $id_tabla,
];

$data = PostRequest::getDataFromUrl('/src/personas/stgr_cambio_data', $campos);
$payload = PersonasPayload::postPayload($data);
$view = PersonasPayload::stgrCambioFromPayload($payload);

$nom = $view['nom'];
$stgr = $view['nivel_stgr'];
$opciones = $view['opciones_nivel_stgr'];

$oDespl = new Desplegable();
$oDespl->setNombre('nivel_stgr');
$oDespl->setOpciones($opciones);
$oDespl->setOpcion_sel($stgr);
$oDespl->setBlanco(true);

$oHash = new HashFront();
$oHash->setCamposForm('nivel_stgr');
$oHash->setArraycamposHidden([
    'id_tabla' => $id_tabla,
    'id_nom' => $id_nom,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'nom' => $nom,
    'oDespl' => $oDespl,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('stgr_cambio.phtml', $a_campos);
