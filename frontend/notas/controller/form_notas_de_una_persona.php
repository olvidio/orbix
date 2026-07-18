<?php

use frontend\notas\helpers\NotasPayload;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

/**
 * Form de alta / edicion de una `PersonaNota` de un dossier.
 *
 * @param string  $_POST['pau']       persona (`p`)
 * @param integer $_POST['id_pau']    id_nom de la persona
 * @param string  $_POST['obj_pau']   clase del dossier
 * @param integer $_POST['id_dossier']
 * @param string  $_POST['mod']       `nuevo` | `editar`
 * @param integer $_POST['permiso']   1, 2, 3
 * @param integer $_POST['scroll_id']
 * @param array   $_POST['sel']       [id_activ#id_asignatura]
 *
 * Orquesta la vista `frontend/notas/view/form_notas_de_una_persona.phtml`. La logica de
 * preparacion de datos vive en `src\notas\application\NotaPersonaFormData`
 * y se consume via PostRequest desde `/src/notas/nota_persona_form_data`
 * (el frontend no importa el caso de uso directamente).
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use src\configuracion\domain\value_objects\ConfigSnapshot;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
ListNavSupport::enterDossierChildNav($oPosicion);

$obj = 'notas\\model\\entity\\PersonaNotaDB';

$Qpau = (string)filter_input(INPUT_POST, 'pau');
$Qid_pau = (int)filter_input(INPUT_POST, 'id_pau');
$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');
$Qpermiso = (int)filter_input(INPUT_POST, 'permiso');

$selRaw = filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$a_sel = is_array($selRaw) ? $selRaw : [];
$sel = $a_sel;

$payload = PostRequest::getDataFromUrl('/src/notas/nota_persona_form_data', [
    'id_pau' => $Qid_pau,
    'id_asignatura_real' => (string)filter_input(INPUT_POST, 'id_asignatura_real'),
    'sel' => $sel,
    'pau' => $Qpau,
    'mod' => (string)filter_input(INPUT_POST, 'mod'),
]);
$datos = NotasPayload::personaFormFromPayload($payload);
$mod = $datos['mod'];
$id_asignatura_real = $datos['id_asignatura_real'];

$oDesplNotas = new Desplegable();
$oDesplNotas->setOpciones($datos['aOpcionesSituacion']);
$oDesplNotas->setNombre('id_situacion');
$ns = $datos['vo']['NotaSituacion'];
$id_situacion = $datos['id_situacion'] === '' || $datos['id_situacion'] === 0
    ? (string)($ns['NUMERICA'] ?? 10)
    : (string)$datos['id_situacion'];
$oDesplNotas->setOpcion_sel($id_situacion);

$lista_situacion_no_acta = $datos['lista_situacion_no_acta'];

$oDesplProfesores = [];
$oDesplNiveles = [];
if ($mod === 'editar') {
    $oDesplProfesores = new Desplegable();
    $oDesplProfesores->setNombre('id_preceptor');
    $oDesplProfesores->setOpciones($datos['profesores']);
    $oDesplProfesores->setOpcion_sel((string)$datos['id_preceptor']);
    $oDesplProfesores->setBlanco(true);
} else {
    $oDesplNiveles = new Desplegable();
    $oDesplNiveles->setNombre('id_nivel');
    $oDesplNiveles->setOpciones($datos['asignaturas_faltan']);
    $oDesplNiveles->setBlanco(true);
    $oDesplNiveles->setAction('fnjs_cmb_opcional()');
}

$chk_preceptor = !empty($datos['preceptor']) ? 'checked' : '';

$tipo_acta = $datos['tipo_acta'];
$ta = $datos['vo']['TipoActa'];
if ($tipo_acta !== '' && $tipo_acta !== 0) {
    $tipoActaInt = \frontend\shared\helpers\PayloadCoercion::int($tipo_acta);
    $chk_acta = $tipoActaInt === ($ta['FORMATO_ACTA'] ?? 0) ? 'checked' : '';
    $chk_certificado = $tipoActaInt === ($ta['FORMATO_CERTIFICADO'] ?? 0) ? 'checked' : '';
} else {
    $chk_acta = 'checked';
    $chk_certificado = '';
}

$epoca = $datos['epoca'];
$ne = $datos['vo']['NotaEpoca'];
if ($epoca !== '' && $epoca !== 0) {
    $epocaInt = \frontend\shared\helpers\PayloadCoercion::int($epoca);
    $chk_epoca_ca = $epocaInt === ($ne['EPOCA_CA'] ?? 0) ? 'checked' : '';
    $chk_epoca_inv = $epocaInt === ($ne['EPOCA_INVIERNO'] ?? 0) ? 'checked' : '';
    $chk_epoca_otro = $epocaInt === ($ne['EPOCA_OTRO'] ?? 0) ? 'checked' : '';
} else {
    $chk_epoca_ca = 'checked';
    $chk_epoca_inv = '';
    $chk_epoca_otro = '';
}

$helpers = $datos['helpers'];

$oHash = new HashFront();
$campos_chk = '!preceptor!epoca!tipo_acta';
$camposForm = 'preceptor!nota_num!nota_max!id_situacion!acta!tipo_acta!f_acta!preceptor!id_preceptor!epoca!id_activ!detalle';
$camposNo = 'refresh!id_preceptor!id_activ' . $campos_chk;
$a_camposHidden = [
    'campos_chk' => $campos_chk,
    'mod' => $mod,
    'pau' => $Qpau,
    'id_pau' => $Qid_pau,
    'obj_pau' => $Qobj_pau,
    'permiso' => $Qpermiso,
    'id_activ' => $datos['id_activ'],
];
if ($id_asignatura_real !== '') {
    $a_camposHidden['id_asignatura_real'] = $id_asignatura_real;
    $a_camposHidden['id_asignatura'] = $id_asignatura_real;
    $a_camposHidden['id_nivel'] = $datos['id_nivel'];
} else {
    $camposForm .= '!id_nivel!id_asignatura';
    $camposNo .= '!id_nivel!id_asignatura';
}
$oHash->setCamposForm($camposForm);
$oHash->setcamposNo($camposNo);
$oHash->setArraycamposHidden($a_camposHidden);

$web = AppUrlConfig::getPublicAppBaseUrl();

$url_posibles_opcionales = AppUrlConfig::srcBrowserUrl('/src/notas/posibles_opcionales_data');
$oHashOpcionales = new HashFront();
$oHashOpcionales->setUrl($url_posibles_opcionales);
$oHashOpcionales->setCamposForm('id_nom');
$h_posibles_opcionales = $oHashOpcionales->linkSinValParams();

$url_posibles_preceptores = AppUrlConfig::srcBrowserUrl('/src/notas/posibles_preceptores_data');
$oHashPreceptores = new HashFront();
$oHashPreceptores->setUrl($url_posibles_preceptores);
$h_posibles_preceptores = $oHashPreceptores->linkSinValParams();

$url_actividad_buscar = $web . '/frontend/notas/controller/actividad_buscar_form.php';
$oHashActivBuscar = new HashFront();
$oHashActivBuscar->setUrl($url_actividad_buscar);
$oHashActivBuscar->setCamposForm('dl_org!f_acta_iso');
$h_actividad_buscar = $oHashActivBuscar->linkSinValParams();

$url_buscar_acta = AppUrlConfig::srcBrowserUrl('/src/notas/buscar_acta');
$oHashBuscarActa = new HashFront();
$oHashBuscarActa->setUrl($url_buscar_acta);
$oHashBuscarActa->setCamposForm('acta');
$h_buscar_acta = $oHashBuscarActa->linkSinValParams();

$url_persona_nota_nueva = AppUrlConfig::srcBrowserUrl('/src/notas/persona_nota_nueva');
$url_persona_nota_editar = AppUrlConfig::srcBrowserUrl('/src/notas/persona_nota_editar');

$oConfig = $_SESSION['oConfig'] ?? null;
$nota_max_default = $oConfig instanceof ConfigSnapshot ? \frontend\shared\helpers\PayloadCoercion::int($oConfig->getNotaMax()) : 0;
$nota_max = $datos['nota_max'] === '' || $datos['nota_max'] === 0
    ? $nota_max_default
    : \frontend\shared\helpers\PayloadCoercion::int($datos['nota_max'], $nota_max_default);

$a_campos = [
    'obj' => $obj,
    'vo' => $datos['vo'],
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_posibles_opcionales' => $url_posibles_opcionales,
    'h_posibles_opcionales' => $h_posibles_opcionales,
    'url_posibles_preceptores' => $url_posibles_preceptores,
    'h_posibles_preceptores' => $h_posibles_preceptores,
    'url_actividad_buscar' => $url_actividad_buscar,
    'h_actividad_buscar' => $h_actividad_buscar,
    'url_buscar_acta' => $url_buscar_acta,
    'h_buscar_acta' => $h_buscar_acta,
    'url_persona_nota_nueva' => $url_persona_nota_nueva,
    'url_persona_nota_editar' => $url_persona_nota_editar,
    'op_genericas' => $helpers['op_genericas_json'],
    'condicion_js' => $helpers['condicion_js'],
    'Qid_asignatura_real' => $id_asignatura_real,
    'nombre_corto' => $datos['nombre_corto'],
    'oDesplNiveles' => $oDesplNiveles,
    'nota_num' => $datos['nota_num'],
    'nota_max' => $nota_max,
    'nota_max_default' => $nota_max_default,
    'oDesplNotas' => $oDesplNotas,
    'chk_acta' => $chk_acta,
    'chk_certificado' => $chk_certificado,
    'acta' => $datos['acta'],
    'f_acta' => $datos['f_acta'],
    'f_acta_iso' => $datos['f_acta_iso'],
    'chk_preceptor' => $chk_preceptor,
    'id_preceptor' => $datos['id_preceptor'],
    'oDesplProfesores' => $oDesplProfesores,
    'epoca' => $epoca,
    'chk_epoca_ca' => $chk_epoca_ca,
    'chk_epoca_inv' => $chk_epoca_inv,
    'chk_epoca_otro' => $chk_epoca_otro,
    'nom_activ' => $datos['nom_activ'],
    'detalle' => $datos['detalle'],
    'lista_situacion_no_acta' => $lista_situacion_no_acta,
    'locale_us' => OrbixRuntime::isLocaleUs(),
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('form_notas_de_una_persona.phtml', $a_campos);
