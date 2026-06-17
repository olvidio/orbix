<?php

use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/certificados_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = list_nav_stack_from_post();
if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_boot_recordar($oPosicion, $Qrefresh);
}
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());

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
