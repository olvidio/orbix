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
 * Ficha de una persona: edicion (o alta si `$Qnuevo === 1`).
 *
 * Migrado desde `apps/personas/controller/personas_editar.php` (slice 3 del
 * modulo `personas`) y, en un segundo paso, refactorizado conforme a
 * `refactor.md`: toda la carga de la persona, la resolucion del repositorio,
 * la lectura de centros/delegaciones/situaciones/lenguas y la resolucion del
 * `id_tabla` viven ahora en `src/personas/application/PersonasEditarData.php`
 * tras el endpoint `/src/personas/personas_editar_data`. Aqui no se importan
 * clases de dominio de `src\` ni se resuelve el contenedor de dependencias.
 *
 * La seleccion de plantilla y la habilitacion de botones siguen aqui porque
 * dependen de `$_SESSION['oPerm']` y de la vista a renderizar.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
require_once("frontend/shared/web/func_web.php");


/** @var Posicion $oPosicion */

$Qnuevo = (int)filter_input(INPUT_POST, 'nuevo'); // 0 -> existe, 1 -> nuevo
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$obj = 'src\\personas\\domain\\entity\\' . $Qobj_pau;

$oPosicion->recordar();

$Qid_nom = 0;
if (!empty($Qnuevo)) {
    $Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
    $id_tabla_post = (string)filter_input(INPUT_POST, 'tabla');
} else {
    $a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    if (!empty($a_sel)) {
        $Qid_nom = (int)strtok($a_sel[0], "#");
        $id_tabla_post = (string)strtok("#");
    } else {
        $Qid_nom = (int)filter_input(INPUT_POST, 'id_nom');
        $id_tabla_post = (string)filter_input(INPUT_POST, 'tabla');
    }
    // Si vengo por Posicion, borro la ultima.
    $stack = isset($_POST['stack']) ? filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT) : '';
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $oPosicion2->olvidar($stack);
        }
    }
}

$campos = [
    'nuevo' => $Qnuevo,
    'obj_pau' => $Qobj_pau,
    'id_nom' => $Qid_nom,
    'tabla' => $id_tabla_post,
    'apellido1' => !empty($Qnuevo) ? ($Qapellido1 ?? '') : '',
];

$data = PostRequest::getDataFromUrl('/src/personas/personas_editar_data', $campos);
$payload = is_array($data) ? $data : [];

$Qid_nom = (int)($payload['id_nom'] ?? $Qid_nom);
$Qobj_pau = (string)($payload['Qobj_pau'] ?? $Qobj_pau);
$trato = (string)($payload['trato'] ?? '');
$nom = (string)($payload['nom'] ?? '');
$apel_fam = (string)($payload['apel_fam'] ?? '');
$nx1 = (string)($payload['nx1'] ?? '');
$apellido1 = (string)($payload['apellido1'] ?? '');
$nx2 = (string)($payload['nx2'] ?? '');
$apellido2 = (string)($payload['apellido2'] ?? '');
$lugar_nacimiento = (string)($payload['lugar_nacimiento'] ?? '');
$f_nacimiento = (string)($payload['f_nacimiento'] ?? '');
$f_situacion = (string)($payload['f_situacion'] ?? '');
$profesion = (string)($payload['profesion'] ?? '');
$sacd = (string)($payload['sacd'] ?? '');
$eap = (string)($payload['eap'] ?? '');
$inc = (string)($payload['inc'] ?? '');
$f_inc = (string)($payload['f_inc'] ?? '');
$ce = (string)($payload['ce'] ?? '');
$ce_lugar = (string)($payload['ce_lugar'] ?? '');
$ce_ini = (string)($payload['ce_ini'] ?? '');
$ce_fin = (string)($payload['ce_fin'] ?? '');
$observ = (string)($payload['observ'] ?? '');
$titulo = (string)($payload['titulo'] ?? '');
$nom_ctr = (string)($payload['nom_ctr'] ?? '');
$id_ctr = (string)($payload['id_ctr'] ?? '');
$id_tabla = (string)($payload['id_tabla'] ?? '');
$dl = (string)($payload['dl'] ?? '');
$idioma_preferido = (string)($payload['idioma_preferido'] ?? '');
$situacion = (string)($payload['situacion'] ?? '');
$nivel_stgr = (string)($payload['nivel_stgr'] ?? '');
$edad = (string)($payload['edad'] ?? '');

$opciones_dl = (array)($payload['opciones_dl'] ?? []);
$opciones_centros = (array)($payload['opciones_centros'] ?? []);
$opciones_situacion = (array)($payload['opciones_situacion'] ?? []);
$opciones_lengua = (array)($payload['opciones_lengua'] ?? []);
$opciones_stgr = (array)($payload['opciones_stgr'] ?? []);
$opciones_inc = (array)($payload['opciones_inc'] ?? []);

$oDesplDl = new Desplegable();
$oDesplDl->setNombre('dl');
$oDesplDl->setOpciones($opciones_dl);
$oDesplDl->setOpcion_sel($dl);
$oDesplDl->setBlanco(true);

$oDesplCentroDl = [];
if (empty($nom_ctr)) {
    $oDesplCentroDl = new Desplegable();
    $oDesplCentroDl->setOpciones($opciones_centros);
    $oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
    $oDesplCentroDl->setNombre("id_ctr");
    $oDesplCentroDl->setBlanco(true);
}

$ok = 0;
$ok_txt = 0;
$presentacion = 'persona_form.phtml';
switch ($Qobj_pau) {
    case 'PersonaAgd':
        if ($_SESSION['oPerm']->have_perm_oficina('agd')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('agd') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaN':
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaNax':
        if ($_SESSION['oPerm']->have_perm_oficina('sm')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('sm') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaS':
        if ($_SESSION['oPerm']->have_perm_oficina('sg')) $ok = 1;
        $presentacion = ($_SESSION['oPerm']->have_perm_oficina('sg') || $_SESSION['oPerm']->have_perm_oficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') $ok_txt = 1;
        break;
    case 'PersonaSSSC':
        if ($_SESSION['oPerm']->have_perm_oficina('des') || $_SESSION['oPerm']->have_perm_oficina('vcsd')) $ok = 1;
        $autorizado = $_SESSION['oPerm']->have_perm_oficina('des')
            || $_SESSION['oPerm']->have_perm_oficina('vcsd')
            || $_SESSION['oPerm']->have_perm_oficina('dtor');
        $presentacion = $autorizado ? 'persona_sss_form.phtml' : 'p_public_personas.phtml';
        if ($autorizado) $ok_txt = 1;
        break;
    case 'PersonaEx':
        $presentacion = 'persona_de_paso.phtml';
        if (
            $_SESSION['oPerm']->have_perm_oficina('agd') ||
            $_SESSION['oPerm']->have_perm_oficina('sm') ||
            $_SESSION['oPerm']->have_perm_oficina('des') ||
            $_SESSION['oPerm']->have_perm_oficina('est')
        ) {
            $ok = 1;
        }
        $ok_txt = 1;
        break;
}

$ir_a_traslado = '';
if (empty($Qnuevo)) {
    $ir_a_traslado = HashFront::link(
        AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/traslado_form.php?'
        . http_build_query(['pau' => 'p', 'id_pau' => $Qid_nom, 'obj_pau' => $Qobj_pau])
    );
}

/*
 * botones:
 *   1: guardar cambios
 *   2: eliminar
 *   3: formato texto
 */
$botones = 0;
if ($ok === 1) {
    $botones = '1';
    if ($Qobj_pau === 'PersonaEx') {
        $botones .= ',2';
    }
}

$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($opciones_situacion);
$oDesplSituacion->setNombre("situacion");
$oDesplSituacion->setOpcion_sel($situacion);

$oDesplLengua = new Desplegable();
$oDesplLengua->setOpciones($opciones_lengua);
$oDesplLengua->setNombre('idioma_preferido');
$oDesplLengua->setOpcion_sel($idioma_preferido);

$oDesplStgr = new Desplegable();
$oDesplStgr->setNombre('nivel_stgr');
$oDesplStgr->setOpciones($opciones_stgr);
$oDesplStgr->setOpcion_sel($nivel_stgr);
$oDesplStgr->setBlanco(true);

$oDesplInc = new Desplegable();
$oDesplInc->setNombre('inc');
$oDesplInc->setOpciones($opciones_inc);
$oDesplInc->setOpcion_sel($inc);
$oDesplInc->setBlanco(true);

$oHash = new HashFront();
$campos_chk = 'sacd';
$camposForm = 'id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento!ce!ce_lugar!ce_ini!ce_fin';
if ($Qobj_pau === 'PersonaSSSC') {
    $camposForm = 'id_ctr!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato!lugar_nacimiento';
}
if ($Qobj_pau === 'PersonaEx') {
    $campos_chk = 'sacd!profesor_stgr';
    $camposForm = 'id_tabla!apel_fam!apellido1!apellido2!dl!eap!f_inc!f_nacimiento!lugar_nacimiento!edad!f_situacion!inc!idioma_preferido!nom!nx1!nx2!observ!profesion!situacion!nivel_stgr!trato';
}
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo($campos_chk);
$oHash->setArraycamposHidden([
    'campos_chk' => $campos_chk,
    'obj_pau' => $Qobj_pau,
    'id_nom' => $Qid_nom,
]);

$a_parametros = ['pau' => 'p', 'id_nom' => $Qid_nom, 'obj_pau' => $Qobj_pau];
$gohome = HashFront::link(AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => 'p', 'id_pau' => $Qid_nom, 'obj_pau' => $Qobj_pau];
$godossiers = HashFront::link('frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$a_campos = [
    'obj_txt' => $obj,
    'oPosicion' => $oPosicion,
    'pau' => 'p',
    'id_pau' => $Qid_nom,
    'Qobj_pau' => $Qobj_pau,
    'nuevo' => $Qnuevo,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'ir_a_traslado' => $ir_a_traslado,
    'titulo' => $titulo,
    'oHash' => $oHash,
    'id_nom' => $Qid_nom,
    'id_tabla' => $id_tabla,
    'dl' => $dl,
    'id_ctr' => $id_ctr,
    'nom_ctr' => $nom_ctr,
    'oDesplDl' => $oDesplDl,
    'oDesplCentro' => $oDesplCentroDl,
    'oDesplSituacion' => $oDesplSituacion,
    'oDesplLengua' => $oDesplLengua,
    'oDesplStgr' => $oDesplStgr,
    'oDesplInc' => $oDesplInc,
    'trato' => $trato,
    'nom' => $nom,
    'apel_fam' => $apel_fam,
    'nx1' => $nx1,
    'apellido1' => $apellido1,
    'nx2' => $nx2,
    'apellido2' => $apellido2,
    'lugar_nacimiento' => $lugar_nacimiento,
    'f_nacimiento' => $f_nacimiento,
    'f_situacion' => $f_situacion,
    'profesion' => $profesion,
    'sacd' => $sacd,
    'eap' => $eap,
    'inc' => $inc,
    'f_inc' => $f_inc,
    'ce' => $ce,
    'ce_lugar' => $ce_lugar,
    'ce_ini' => $ce_ini,
    'ce_fin' => $ce_fin,
    'observ' => $observ,
    'edad' => $edad,
    'botones' => $botones,
    'obj' => $obj,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar($presentacion, $a_campos);
