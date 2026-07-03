<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\certificados\helpers\CertificadosPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = \frontend\shared\helpers\ListNavSupport::stackFromPost();
if ($stackFromPost !== 0) {
    \frontend\shared\helpers\ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    \frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
}
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());

$form = CertificadosPayload::uploadFirmadoFromPayload(CertificadosPayload::postData(
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
