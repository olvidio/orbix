<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/certificados_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$form = certificados_upload_firmado_from_payload(certificados_post_data(
    PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_upload_firmado_data', $_POST)
));

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposNo('certificado_pdf');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_item' => $form['id_item'],
    'id_nom' => $form['id_nom'],
    'solo_pdf' => 1,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $form['apellidos_nombre'],
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_upload_firmado.html.twig', $a_campos);
