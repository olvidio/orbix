<?php

/**
 * Form de alta / edicion de una `ActividadAsignatura` desde el dossier
 * `asignaturas_de_una_actividad` (3005).
 *
 * Sucesor de `apps/actividadestudios/controller/form_3005.php`. URL canonica.
 */

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

require_once 'frontend/shared/global_header_front.inc';

$obj = 'ActividadAsignatura';

$oPosicion->recordar();

$Qpau = (string) filter_input(INPUT_POST, 'pau');

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$camposAsig = [
    'pau' => $Qpau,
    'id_pau' => (int)filter_input(INPUT_POST, 'id_pau'),
    'id_activ' => (int)filter_input(INPUT_POST, 'id_activ'),
    'id_asignatura' => (int)filter_input(INPUT_POST, 'id_asignatura'),
];
if (!empty($a_sel)) {
    $camposAsig['sel'] = $a_sel;
}

$d = PostRequest::getDataFromUrl('/src/actividadestudios/form_asignaturas_de_una_actividad_data', $camposAsig);

$mod = $d['mod'] ?? 'nuevo';
$Qid_activ = (int)($d['id_activ'] ?? 0);
$Qid_asignatura = (int)($d['id_asignatura'] ?? 0);
$nombre_corto = $d['nombre_corto'] ?? '';
$chk_avisado = $d['chk_avisado'] ?? '';
$chk_confirmado = $d['chk_confirmado'] ?? '';
$chk_preceptor = $d['chk_preceptor'] ?? '';
$f_ini = $d['f_ini'] ?? '';
$f_fin = $d['f_fin'] ?? '';

$oDesplProfesores = new Desplegable();
$oDesplProfesores->setOpciones($d['oDesplProfesores_opciones'] ?? []);
$oDesplProfesores->setNombre('id_profesor');
$oDesplProfesores->setBlanco('t');
$oDesplProfesores->setOpcion_sel(array_key_exists('id_profesor_sel', $d) ? $d['id_profesor_sel'] : -1);

$oDesplAsignaturas = [];
if (!empty($d['oDesplAsignaturas_opciones'])) {
    $oDesplAsignaturas = new Desplegable('', $d['oDesplAsignaturas_opciones'], '', true);
    $oDesplAsignaturas->setNombre('id_asignatura');
    $oDesplAsignaturas->setAction("fnjs_mas_profes('asignatura')");
}

$oHash = new HashFront();
$oHash->setCamposNo('mod!avis_profesor');
$oHash->setCamposForm($d['camposForm'] ?? '');
$oHash->setArraycamposHidden($d['a_camposHidden'] ?? []);

$web = AppUrlConfig::getPublicAppBaseUrl();
$url_profesores = $web . '/src/actividadestudios/profesores_desplegable_data';

$oHashTipo = new HashFront();
$oHashTipo->setUrl($url_profesores);
$oHashTipo->setCamposForm('salida');
$h = $oHashTipo->linkSinValParams();

$oHashTipo->setCamposForm('salida!id_activ');
$h1 = $oHashTipo->linkSinValParams();

$oHashTipo->setCamposForm('salida!id_activ!id_asignatura');
$h2 = $oHashTipo->linkSinValParams();

$url_actividad_asignatura_nueva = $web . '/src/actividadestudios/actividad_asignatura_nueva';
$url_actividad_asignatura_editar = $web . '/src/actividadestudios/actividad_asignatura_editar';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'h' => $h,
    'h1' => $h1,
    'h2' => $h2,
    'url_profesores' => $url_profesores,
    'mod' => $mod,
    'id_activ' => $Qid_activ,
    'id_asignatura' => $Qid_asignatura,
    'nombre_corto' => $nombre_corto,
    'oDesplAsignaturas' => $oDesplAsignaturas,
    'oDesplProfesores' => $oDesplProfesores,
    'chk_preceptor' => $chk_preceptor,
    'chk_avisado' => $chk_avisado,
    'chk_confirmado' => $chk_confirmado,
    'f_ini' => $f_ini,
    'f_fin' => $f_fin,
    'locale_us' => OrbixRuntime::isLocaleUs(),
    'url_actividad_asignatura_nueva' => $url_actividad_asignatura_nueva,
    'url_actividad_asignatura_editar' => $url_actividad_asignatura_editar,
];

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('form_asignaturas_de_una_actividad.phtml', $a_campos);
