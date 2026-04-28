<?php

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$formData = PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_adjuntar_data', [
    'id_nom' => $id_nom,
]);
$form = is_array($formData) ? $formData : [];
$nom = (string)($form['nom'] ?? '');
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_enviado = (string)($form['f_enviado'] ?? '');
$firmado = '';
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_enviado');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'refresh' => 1,
]);

$locData = PostRequest::getDataFromUrl('/src/certificados/certificados_locales_data', []);
$a_locales = (array)($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'nom' => $nom,
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $idioma,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_enviado' => $f_enviado,
    'chk_firmado' => $chk_firmado,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_adjuntar.html.twig', $a_campos);
