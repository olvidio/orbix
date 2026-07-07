<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\certificados\helpers\CertificadosPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$form = CertificadosPayload::recibidoFormFromPayload(CertificadosPayload::postData(
    PostRequest::getDataFromUrl('/src/certificados/certificado_recibido_modificar_data', $_POST)
));

$idItem = (int) ($form['id_item'] ?? 0);
$navState = ListNavSupport::buildCertificadoImprimirParentReturnParametros();
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['id_item' => $idItem],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildCertificadoImprimirParentReturnParametros());


$oDesplIdiomas = new Desplegable('idioma', $form['a_locales'], $form['idioma'], true);

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_recibido');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $form['id_nom'],
    'id_item' => $form['id_item'],
    'refresh' => 1,
]);

$basePublic = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'url_certificado_recibido_guardar' => $basePublic . '/src/certificados/certificado_recibido_guardar',
    'url_certificado_recibido_pdf_upload' => $basePublic . '/src/certificados/certificado_recibido_pdf_upload',
    'nom' => $form['nom'],
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $form['idioma'],
    'destino' => $form['destino'],
    'certificado' => $form['certificado'],
    'f_certificado' => $form['f_certificado'],
    'f_recibido' => $form['f_recibido'],
    'chk_firmado' => $form['chk_firmado'],
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_recibido_adjuntar.html.twig', $a_campos);
