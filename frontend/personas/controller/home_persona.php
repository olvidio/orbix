<?php

namespace frontend\personas\controller;

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use web\Hash;
use frontend\shared\web\Posicion;

/**
 * Pantalla de cabecera de una persona (datos basicos + acceso a dossiers y ficha).
 *
 * Migrado desde `apps/personas/controller/home_persona.php` (slice 3 del
 * modulo `personas`). Refactor conforme a `refactor.md`: la resolucion de
 * repositorios, el acceso a centros, telecos, traduccion del nivel_stgr y la
 * normalizacion de `Qobj_pau` se han movido a
 * `src/personas/application/HomePersonaData.php` tras el endpoint
 * `/src/personas/home_persona_data`. Este controlador no importa clases `src\`.
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

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

// Si vengo de planning_select u otros, la tabla puede ser mas generica.
if (isset($_SESSION['session_go_to']['sel']['tabla'])) {
    $_SESSION['session_go_to']['sel']['tabla'] = $Qobj_pau;
}

$pau = "p";

$campos = [
    'id_nom' => $id_nom,
    'id_tabla' => $id_tabla,
    'obj_pau' => $Qobj_pau,
];

$data = PostRequest::getDataFromUrl('/src/personas/home_persona_data', $campos);
$payload = is_array($data) ? $data : [];

$Qobj_pau = (string)($payload['Qobj_pau'] ?? $Qobj_pau);
$nom = (string)($payload['titulo'] ?? '');
$dl = (string)($payload['dl'] ?? '');
$f_nacimiento = (string)($payload['f_nacimiento'] ?? '');
$situacion = (string)($payload['situacion'] ?? '');
$f_situacion = (string)($payload['f_situacion'] ?? '');
$profesion = (string)($payload['profesion'] ?? '');
$stgr = (string)($payload['stgr'] ?? '');
$observ = (string)($payload['observ'] ?? '');
$ctr = (string)($payload['ctr'] ?? '');
$telfs = (string)($payload['telfs'] ?? '');
$mails = (string)($payload['mails'] ?? '');

$a_parametros = ['pau' => $pau, 'id_nom' => $id_nom, 'obj_pau' => $Qobj_pau];
$gohome = Hash::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$go_ficha = Hash::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/personas_editar.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => $pau, 'id_pau' => $id_nom, 'obj_pau' => $Qobj_pau];
$godossiers = Hash::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'oPosicion' => $oPosicion,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'go_ficha' => $go_ficha,
    'titulo' => $nom,
    'telfs' => $telfs,
    'mails' => $mails,
    'stgr' => $stgr,
    'profesion' => $profesion,
    'celebra' => '',
    'santo' => '',
    'f_nacimiento' => $f_nacimiento,
    'dl' => $dl,
    'ctr' => $ctr,
    'pau' => $pau,
    'id_pau' => $id_nom,
    'Qobj_pau' => $Qobj_pau,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('home_persona.phtml', $a_campos);
