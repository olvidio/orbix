<?php

namespace frontend\personas\controller;

use frontend\shared\PostRequest;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;

/**
 * Formulario para cambiar el `nivel_stgr` de una persona.
 *
 * Migrado desde `apps/personas/controller/stgr_cambio.php` (slice 1) y, en un
 * segundo paso, refactorizado conforme a `refactor.md`: la resolucion del
 * repositorio y la lectura de la persona viven ahora en
 * `src/personas/application/StgrCambioData.php` tras el endpoint
 * `/src/personas/stgr_cambio_data`. Este controlador no importa clases `src\`.
 */
require_once("frontend/shared/global_header_front.inc");


/** @var Posicion $oPosicion */
$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
} else {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

$campos = [
    'id_nom' => $id_nom,
    'id_tabla' => $id_tabla,
];

$data = PostRequest::getDataFromUrl('/src/personas/stgr_cambio_data', $campos);
$payload = is_array($data) ? $data : [];

$nom = (string)($payload['nom'] ?? '');
$stgr = (string)($payload['nivel_stgr'] ?? '');
$opciones = (array)($payload['opciones_nivel_stgr'] ?? []);

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
