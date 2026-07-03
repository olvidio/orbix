<?php
namespace frontend\personas\controller;

use frontend\personas\helpers\PersonasPayload;
use frontend\personas\helpers\PersonasPostInput;
use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Ficha de una persona: edicion (o alta si `$Qnuevo === 1`).
 */
require_once 'frontend/shared/FrontBootstrap.php';
require_once 'frontend/shared/web/func_web.php';
$oPosicion = FrontBootstrap::boot();

/** @var Posicion $oPosicion */

$Qnuevo = (int)filter_input(INPUT_POST, 'nuevo');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$obj = 'src\\personas\\domain\\entity\\' . $Qobj_pau;

\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$Qid_nom = 0;
$Qapellido1 = '';
$id_tabla_post = '';
if (!empty($Qnuevo)) {
    $Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
    $id_tabla_post = (string)filter_input(INPUT_POST, 'tabla');
} else {
    $ids = PersonasPostInput::idFromSelPost();
    $Qid_nom = $ids['id_nom'];
    $id_tabla_post = $ids['id_tabla'];
    $stack = PersonasPostInput::stackFromPost();
    if ($stack !== null && $stack !== 0) {
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
    'apellido1' => !empty($Qnuevo) ? $Qapellido1 : '',
];

$data = PostRequest::getDataFromUrl('/src/personas/personas_editar_data', $campos);
$payload = PersonasPayload::postPayload($data);
$form = PersonasPayload::editarFormFromPayload($payload, $Qid_nom, $Qobj_pau);

$Qid_nom = $form['id_nom'];
$Qobj_pau = $form['Qobj_pau'];
$trato = $form['trato'];
$nom = $form['nom'];
$apel_fam = $form['apel_fam'];
$nx1 = $form['nx1'];
$apellido1 = $form['apellido1'];
$nx2 = $form['nx2'];
$apellido2 = $form['apellido2'];
$lugar_nacimiento = $form['lugar_nacimiento'];
$f_nacimiento = $form['f_nacimiento'];
$f_situacion = $form['f_situacion'];
$profesion = $form['profesion'];
$sacd = $form['sacd'];
$eap = $form['eap'];
$inc = $form['inc'];
$f_inc = $form['f_inc'];
$ce = $form['ce'];
$ce_lugar = $form['ce_lugar'];
$ce_ini = $form['ce_ini'];
$ce_fin = $form['ce_fin'];
$observ = $form['observ'];
$titulo = $form['titulo'];
$nom_ctr = $form['nom_ctr'];
$id_ctr = $form['id_ctr'];
$id_tabla = $form['id_tabla'];
$dl = $form['dl'];
$idioma_preferido = $form['idioma_preferido'];
$situacion = $form['situacion'];
$nivel_stgr = $form['nivel_stgr'];
$edad = $form['edad'];
$opciones_dl = $form['opciones_dl'];
$opciones_centros = $form['opciones_centros'];
$opciones_situacion = $form['opciones_situacion'];
$opciones_lengua = $form['opciones_lengua'];
$opciones_stgr = $form['opciones_stgr'];
$opciones_inc = $form['opciones_inc'];

$oDesplDl = new Desplegable();
$oDesplDl->setNombre('dl');
$oDesplDl->setOpciones($opciones_dl);
$oDesplDl->setOpcion_sel($dl);
$oDesplDl->setBlanco(true);

$oDesplCentroDl = [];
if ($nom_ctr === '') {
    $oDesplCentroDl = new Desplegable();
    $oDesplCentroDl->setOpciones($opciones_centros);
    $oDesplCentroDl->setAction("fnjs_act_ctr('ctr')");
    $oDesplCentroDl->setNombre('id_ctr');
    $oDesplCentroDl->setBlanco(true);
}

$ok = 0;
$ok_txt = 0;
$presentacion = 'persona_form.phtml';
switch ($Qobj_pau) {
    case 'PersonaAgd':
        if (PersonasPayload::havePermOficina('agd')) {
            $ok = 1;
        }
        $presentacion = (PersonasPayload::havePermOficina('agd') || PersonasPayload::havePermOficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') {
            $ok_txt = 1;
        }
        break;
    case 'PersonaN':
        if (PersonasPayload::havePermOficina('sm')) {
            $ok = 1;
        }
        $presentacion = (PersonasPayload::havePermOficina('sm') || PersonasPayload::havePermOficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') {
            $ok_txt = 1;
        }
        break;
    case 'PersonaNax':
        if (PersonasPayload::havePermOficina('sm')) {
            $ok = 1;
        }
        $presentacion = (PersonasPayload::havePermOficina('sm') || PersonasPayload::havePermOficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') {
            $ok_txt = 1;
        }
        break;
    case 'PersonaS':
        if (PersonasPayload::havePermOficina('sg')) {
            $ok = 1;
        }
        $presentacion = (PersonasPayload::havePermOficina('sg') || PersonasPayload::havePermOficina('dtor'))
            ? 'persona_form.phtml'
            : 'p_public_personas.phtml';
        if ($presentacion === 'persona_form.phtml') {
            $ok_txt = 1;
        }
        break;
    case 'PersonaSSSC':
        if (PersonasPayload::havePermOficina('des') || PersonasPayload::havePermOficina('vcsd')) {
            $ok = 1;
        }
        $autorizado = PersonasPayload::havePermOficina('des')
            || PersonasPayload::havePermOficina('vcsd')
            || PersonasPayload::havePermOficina('dtor');
        $presentacion = $autorizado ? 'persona_sss_form.phtml' : 'p_public_personas.phtml';
        if ($autorizado) {
            $ok_txt = 1;
        }
        break;
    case 'PersonaEx':
        $presentacion = 'persona_de_paso.phtml';
        if (
            PersonasPayload::havePermOficina('agd')
            || PersonasPayload::havePermOficina('sm')
            || PersonasPayload::havePermOficina('des')
            || PersonasPayload::havePermOficina('est')
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

$botones = 0;
if ($ok === 1) {
    $botones = '1';
    if ($Qobj_pau === 'PersonaEx') {
        $botones .= ',2';
    }
}

$oDesplSituacion = new Desplegable();
$oDesplSituacion->setOpciones($opciones_situacion);
$oDesplSituacion->setNombre('situacion');
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
