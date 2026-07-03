<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\certificados\helpers\CertificadosPostInput;
use frontend\certificados\helpers\CertificadosPayload;
use frontend\shared\helpers\ListNavSupport;
use frontend\shared\helpers\FuncTablasSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::buildReturnParametrosFromPost());


$id_nom = CertificadosPostInput::idNomFromSelPost();
$Qnuevo = (int)filter_input(INPUT_POST, 'nuevo');
$formData = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificado_recibido_adjuntar_data', [
    'id_nom' => $id_nom,
], false));
if (!empty($formData['error'])) {
    echo PostRequest::stripInternalCallProvenance(PayloadCoercion::string($formData['error']));
    return;
}
$form = CertificadosPayload::adjuntarFormFromPayload($formData);
$aviso = $form['aviso'];
$nom = $form['nom'];
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_recibido = $form['f_enviado'];
$firmado = '';
$chk_firmado = FuncTablasSupport::isTrue($firmado) ? 'checked' : '';

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_recibido');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'nuevo' => $Qnuevo,
    'refresh' => 1,
]);

$locData = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificados_locales_data', []));
$a_locales = NotasFormSupport::desplegableOpciones($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

$basePublic = rtrim(AppUrlConfig::getPublicAppBaseUrl(), '/');
$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'url_certificado_recibido_guardar' => $basePublic . '/src/certificados/certificado_recibido_guardar',
    'url_certificado_recibido_pdf_upload' => $basePublic . '/src/certificados/certificado_recibido_pdf_upload',
    'nom' => $nom,
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $idioma,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_recibido' => $f_recibido,
    'chk_firmado' => $chk_firmado,
    'aviso' => $aviso,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_recibido_adjuntar.html.twig', $a_campos);
