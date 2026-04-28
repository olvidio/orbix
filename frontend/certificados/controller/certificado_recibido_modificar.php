<?php

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$formData = PostRequest::getDataFromUrl('/src/certificados/certificado_recibido_modificar_data', $_POST);

$id_nom = (int)($formData['id_nom'] ?? 0);
$Qid_item = (int)($formData['id_item'] ?? 0);
$nom = (string)($formData['nom'] ?? '');
$idioma = (string)($formData['idioma'] ?? '');
$destino = (string)($formData['destino'] ?? '');
$certificado = (string)($formData['certificado'] ?? '');
$f_certificado = (string)($formData['f_certificado'] ?? '');
$f_recibido = (string)($formData['f_recibido'] ?? '');
$chk_firmado = (string)($formData['chk_firmado'] ?? '');

$a_locales = (array)($formData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, $idioma, true);

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_recibido');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'id_item' => $Qid_item,
    'refresh' => 1,
]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'nom' => $nom,
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $idioma,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_recibido' => $f_recibido,
    'chk_firmado' => $chk_firmado,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_recibido_adjuntar.html.twig', $a_campos);
