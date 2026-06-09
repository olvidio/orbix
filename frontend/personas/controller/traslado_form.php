<?php

namespace frontend\personas\controller;

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;

/**
 * Formulario para trasladar una persona de centro y/o delegacion.
 *
 * Migrado desde `apps/personas/controller/traslado_form.php` (slice 5) y
 * refactorizado conforme a `refactor.md`: la localizacion de la persona, la
 * construccion de listas de centros, delegaciones y situaciones, y el calculo
 * del centro/dl actuales viven ahora en
 * `src/personas/application/TrasladoFormData.php` tras el endpoint
 * `/src/personas/traslado_form_data`. Este controlador no importa clases `src\`.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
$oPosicion->recordar();

$Qcabecera = (string)filter_input(INPUT_POST, 'cabecera');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_pau = (int)strtok($a_sel[0], "#");
} else {
    $id_pau = (int)filter_input(INPUT_POST, 'id_pau');
}

$campos = [
    'id_pau' => $id_pau,
];

$data = PostRequest::getDataFromUrl('/src/personas/traslado_form_data', $campos);
$payload = is_array($data) ? $data : [];

$titulo = (string)($payload['titulo'] ?? '');
$id_ctr = $payload['id_ctr'] ?? '';
$nombre_ctr = (string)($payload['nombre_ctr'] ?? '');
$dl = (string)($payload['dl'] ?? '');
$hoy = (string)($payload['hoy'] ?? '');
$opciones_centros = (array)($payload['opciones_centros'] ?? []);
$opciones_dl = (array)($payload['opciones_dl'] ?? []);
$opciones_situacion = (array)($payload['opciones_situacion'] ?? []);

$oDesplCentroDl = new Desplegable();
$oDesplCentroDl->setNombre('new_ctr');
$oDesplCentroDl->setOpciones($opciones_centros);
$oDesplCentroDl->setBlanco(true);

$oDesplDlyR = Desplegable::desdeOpciones($opciones_dl, 'new_dl');

$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($opciones_situacion);
$oDesplSituacion->setNombre('situacion');

$oHash = new HashFront();
$oHash->setCamposForm('new_ctr!f_ctr!new_dl!f_dl!situacion');
$oHash->setArraycamposHidden([
    'obj_pau' => $Qobj_pau,
    'id_pau' => $id_pau,
    'id_ctr_o' => $id_ctr,
    'ctr_o' => $nombre_ctr,
    'dl' => $dl,
]);

$a_parametros = ['pau' => 'p', 'id_nom' => $id_pau, 'obj_pau' => $Qobj_pau];
$gohome = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => 'p', 'id_pau' => $id_pau, 'obj_pau' => $Qobj_pau];
$godossiers = HashFront::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'titulo' => $titulo,
    'nombre_ctr' => $nombre_ctr,
    'hoy' => $hoy,
    'dl' => $dl,
    'oDesplDlyR' => $oDesplDlyR,
    'oDesplCentros' => $oDesplCentroDl,
    'oDesplSituacion' => $oDesplSituacion,
    'Qcabecera' => $Qcabecera,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('traslado_form.phtml', $a_campos);
