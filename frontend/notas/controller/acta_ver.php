<?php

/**
 * Esta página muestra un formulario para modificar los datos de un acta.
 *
 * Payload vía `PostRequest` → `/src/notas/acta_ver_form_data` (`ActaVerFormData`);
 * el controlador arma HashFront y la vista.
 *
 * @package    delegacion
 * @subpackage    est
 * @author    Daniel Serrabou
 * @since        14/10/03.
 *
 */

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\helpers\SignedDownloadToken;
use function frontend\shared\helpers\urlsafe_b64encode;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
$f_acta = '';
$libro = '';
$pagina = '';
$linea = '';
$lugar = '';
$observ = '';

// Si notas=(nuevo|acta), quiere decir que estoy en un include de actividadestudios/controller/acta_notas
$notas = empty($notas) ? '' : $notas;
$permiso = empty($permiso) ? 3 : $permiso;

// Si soy region del stgr, no puedo modificar actas (que lo hagan las dl).
if (OrbixRuntime::miAmbito() === 'rstgr') {
    $permiso = 0;
}

$requestPayload = PostRequest::requestPayloadForHash();
$a_sel = isset($requestPayload['sel']) && is_array($requestPayload['sel'])
    ? $requestPayload['sel']
    : [];
$Qmod = (string)($requestPayload['mod'] ?? '');
$Qsa_actas = (string)($requestPayload['sa_actas'] ?? '');
$Qacta = (string)($requestPayload['acta'] ?? '');
$Qnotas = (string)($requestPayload['notas'] ?? '');

if (empty($notas) && empty($Qnotas)) {
    $oPosicion->recordar();
}

$payload = $requestPayload;
$payload['scope_notas'] = $notas;
$payload['scope_permiso'] = $permiso;
if (isset($acta_notas_a_actas)) {
    $payload['acta_notas_a_actas_json'] = json_encode($acta_notas_a_actas, JSON_THROW_ON_ERROR);
}
if (isset($id_activ)) {
    $payload['id_activ_scope'] = $id_activ;
}
if (isset($id_asignatura)) {
    $payload['id_asignatura_scope'] = (string)$id_asignatura;
}

$d = PostRequest::getDataFromUrl('/src/notas/acta_ver_form_data', $payload);

$notas = (string)($d['notas'] ?? $notas);
$permiso = (int)($d['permiso'] ?? $permiso);
$Qmod = (string)($d['mod'] ?? $Qmod);
$acta_actual = (string)($d['acta_actual'] ?? '');
$acta_new = (string)($d['acta_new'] ?? '');
$ult_acta = $d['ult_acta'] ?? '';
$f_acta = (string)($d['f_acta'] ?? '');
$libro = (string)($d['libro'] ?? '');
$ult_lib = (string)($d['ult_lib'] ?? '');
$pagina = (string)($d['pagina'] ?? '');
$ult_pag = (string)($d['ult_pag'] ?? '');
$linea = (string)($d['linea'] ?? '');
$ult_lin = (string)($d['ult_lin'] ?? '');
$lugar = (string)($d['lugar'] ?? '');
$observ = (string)($d['observ'] ?? '');
$id_activ = (int)($d['id_activ'] ?? 0);
$id_asignatura_actual = (string)($d['id_asignatura_actual'] ?? '');
$nombre_asignatura = (string)($d['nombre_asignatura'] ?? '');
$examinadores = $d['examinadores'] ?? [];
$a_actas = $d['a_actas'] ?? [];
$has_pdf = !empty($d['has_pdf']);
if (!empty($d['warn_no_id_activ'])) {
    echo _('no se guardará el ca/cv donde se cursó la asignatura');
}

$obj = 'notas\\model\\entity\\ActaDl';

$oHashActa = new HashFront();
$sCamposForm = 'libro!linea!pagina!lugar!observ!id_asignatura!f_acta!acta!name_asignatura';
if ($Qmod === 'nueva' || $notas === 'nuevo') {
    $sCamposForm .= '!acta';
    $sCamposForm .= '!f_acta';
}
if ($examinadores !== [] && ($examinadores[0] ?? '') !== '') {
    $sCamposForm .= '!examinadores';
}
$oHashActa->setCamposForm($sCamposForm);
$oHashActa->setCamposNo('go_to!examinadores!notas!refresh');
$a_camposHidden = [];
if ($Qmod === 'nueva' || $notas === 'nuevo') {
    $a_camposHidden['mod'] = 'nueva';
    if (!empty($id_activ)) {
        $a_camposHidden['id_activ'] = $id_activ;
    }
} else {
    $a_camposHidden['mod'] = '';
    $a_camposHidden['id_activ'] = $id_activ;
    $a_camposHidden['sa_actas'] = urlsafe_b64encode(json_encode($a_actas, JSON_THROW_ON_ERROR));
    $a_camposHidden['notas'] = $notas;
}
$oHashActa->setArrayCamposHidden($a_camposHidden);

$oHashActaPdf = new HashFront();
$oHashActaPdf->setCamposForm('acta_pdf');
$oHashActaPdf->setCamposNo('acta_pdf');
$oHashActaPdf->setArrayCamposHidden(['acta_num' => $acta_actual]);

$titulo = strtoupper(_('datos del acta'));

$url_examinadores = AppUrlConfig::getPublicAppBaseUrl() . '/src/notas/examinadores_search';
$oHashExaminadores = new HashFront();
$oHashExaminadores->setUrl($url_examinadores);
$oHashExaminadores->setCamposForm('search');
$h_examinadores = $oHashExaminadores->getParamAjaxEnArray();

$url_asignaturas = AppUrlConfig::getPublicAppBaseUrl() . '/src/notas/asignaturas_search';
$oHashAsignaturas = new HashFront();
$oHashAsignaturas->setUrl($url_asignaturas);
$oHashAsignaturas->setCamposForm('search');
$h_asignaturas = $oHashAsignaturas->getParamAjaxEnArray();

$url_acta_nueva = AppUrlConfig::getPublicAppBaseUrl() . '/src/notas/acta_nueva';
$url_acta_modificar = AppUrlConfig::getPublicAppBaseUrl() . '/src/notas/acta_modificar';

$base = AppUrlConfig::getPublicAppBaseUrl();
$url_upload = $base . '/frontend/notas/controller/acta_pdf_upload.php';

if (!$has_pdf) {
    $readonly = '';
    $url_download = '';
    $url_delete = '';
} else {
    $readonly = 'readonly';
    $url_download = SignedDownloadToken::urlNotasActa($acta_actual);
    $url_delete = $base . '/frontend/notas/controller/acta_pdf_delete.php';
}
$oHashActaDelete = new HashFront();
$oHashActaDelete->setArrayCamposHidden(['acta_num' => $acta_actual]);
$h_delete = $oHashActaDelete->getParamAjax();

if (OrbixRuntime::miAmbito() === 'rstgr' || OrbixRuntime::miAmbito() === 'r') {
    $soy_rstgr = true;
} else {
    $soy_rstgr = false;
}

$a_campos = ['obj' => $obj,
    'oPosicion' => $oPosicion,
    'notas' => $notas,
    'mod' => $Qmod,
    'oHashActa' => $oHashActa,
    'oHashActaPdf' => $oHashActaPdf,
    'titulo' => $titulo,
    'acta_actual' => $acta_actual,
    'acta_new' => $acta_new,
    'ult_acta' => $ult_acta,
    'f_acta' => $f_acta,
    'libro' => $libro,
    'ult_lib' => $ult_lib,
    'pagina' => $pagina,
    'ult_pag' => $ult_pag,
    'linea' => $linea,
    'ult_lin' => $ult_lin,
    'lugar' => $lugar,
    'observ' => $observ,
    'url_examinadores' => $url_examinadores,
    'h_examinadores' => $h_examinadores,
    'url_asignaturas' => $url_asignaturas,
    'h_asignaturas' => $h_asignaturas,
    'url_acta_nueva' => $url_acta_nueva,
    'url_acta_modificar' => $url_acta_modificar,
    'id_asignatura' => $id_asignatura_actual,
    'nombre_asignatura' => $nombre_asignatura,
    'examinadores' => $examinadores,
    'a_actas' => $a_actas,
    'permiso' => $permiso,
    'readonly' => $readonly,
    'url_upload' => $url_upload,
    'url_download' => $url_download,
    'url_delete' => $url_delete,
    'h_delete' => $h_delete,
    'soy_rstgr' => $soy_rstgr,
];

$oView = new ViewNewPhtml('frontend\notas\controller');
$oView->renderizar('acta_ver.phtml', $a_campos);
