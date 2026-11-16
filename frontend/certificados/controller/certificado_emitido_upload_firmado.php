<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashF;
use frontend\shared\FrontBootstrap;
use frontend\certificados\helpers\CertificadosPayload;
use frontend\certificados\helpers\CertificadosPostInput;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\PayloadCoercion;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

// Desde la lista llega el certificado marcado en `sel[]` (`id_item#...`), no `id_item`.
$form = CertificadosPayload::uploadFirmadoFromPayload(CertificadosPayload::postData(
    PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_upload_firmado_data', [
        'id_item' => CertificadosPostInput::idItemFromSelPost(),
    ])
));

$idItem = $form['id_item'];
$navState = ListNavSupport::buildSelectionStatePatchFromPost();
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['id_item' => $idItem],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildSelectionStatePatchFromPost());


$oHashCertificadoPdf = new HashF();
$oHashCertificadoPdf->setCamposNo('certificado_pdf');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_item' => $form['id_item'],
    'id_nom' => $form['id_nom'],
    'solo_pdf' => 1,
    // Cápsula del certificado: `certificado_emitido_pdf_upload` toma de ella el `id_item`.
    'ctx_guardar' => $form['ctx_guardar'],
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $form['apellidos_nombre'],
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_upload_firmado.html.twig', $a_campos);
