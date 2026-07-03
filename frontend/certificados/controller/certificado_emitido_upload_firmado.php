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

$stackFromPost = ListNavSupport::stackFromPost();
if ($stackFromPost !== 0) {
    ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
}
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());

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
