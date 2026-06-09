<?php

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$formData = PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_upload_firmado_data', $_POST);

$Qid_item = (int)($formData['id_item'] ?? 0);
$id_nom = (int)($formData['id_nom'] ?? 0);
$nom = (string)($formData['nom'] ?? '');
$apellidos_nombre = (string)($formData['apellidos_nombre'] ?? '');

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposNo('certificado_pdf');
$oHashCertificadoPdf->setArrayCamposHidden(
    [
        'id_item' => $Qid_item,
        'id_nom' => $id_nom,
        'solo_pdf' => 1
    ]);

$a_campos = ['oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $apellidos_nombre,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_upload_firmado.html.twig', $a_campos);
