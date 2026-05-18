<?php

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) {
    $id_nom = (int)strtok((string)$a_sel[0], '#');
} else {
    $id_nom = (int)filter_input(INPUT_POST, 'id_nom');
}
$Qnuevo = (int)filter_input(INPUT_POST, 'nuevo');
$formData = PostRequest::getDataFromUrl('/src/certificados/certificado_recibido_adjuntar_data', [
    'id_nom' => $id_nom,
], false);
$aviso = '';
if (!empty($formData['error'])) {
    echo PostRequest::stripInternalCallProvenance((string)$formData['error']);
    return;
}
$form = is_array($formData) ? $formData : [];
$aviso = (string)($form['aviso'] ?? '');
$nom = (string)($form['nom'] ?? '');
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_recibido = (string)($form['f_recibido'] ?? '');
$firmado = '';
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_recibido');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'nuevo' => $Qnuevo,
    'refresh' => 1,
]);

$locData = PostRequest::getDataFromUrl('/src/certificados/certificados_locales_data', []);
$a_locales = (array)($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

$basePublic = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'url_certificado_recibido_guardar' => $basePublic . '/src/certificados/certificado_recibido_guardar',
    'url_certificado_recibido_pdf_upload' => $basePublic . '/src/certificados/certificado_recibido_pdf_upload',
    'nom' => $nom,
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $idioma,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_recibido' => $f_recibido,
    'chk_firmado' => $chk_firmado,
    'aviso' => $aviso,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_recibido_adjuntar.html.twig', $a_campos);
