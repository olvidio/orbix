<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/certificados_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$form = certificados_recibido_form_from_payload(certificados_post_data(
    PostRequest::getDataFromUrl('/src/certificados/certificado_recibido_modificar_data', $_POST)
));

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
