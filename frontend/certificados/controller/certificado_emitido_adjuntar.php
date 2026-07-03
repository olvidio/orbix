<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
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
\frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion);
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost());


$id_nom = CertificadosPostInput::idNomFromSelPost();
$formData = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_adjuntar_data', [
    'id_nom' => $id_nom,
], false));
if (!empty($formData['error'])) {
    echo PostRequest::stripInternalCallProvenance(\frontend\shared\helpers\PayloadCoercion::string($formData['error']));
    return;
}
$form = CertificadosPayload::adjuntarFormFromPayload($formData);
$aviso = $form['aviso'];
$nom = $form['nom'];
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_enviado = $form['f_enviado'];
$firmado = '';
$chk_firmado = \src\shared\domain\helpers\FuncTablasSupport::isTrue($firmado) ? 'checked' : '';

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_enviado');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'nuevo' => 1,
    'refresh' => 1,
]);

$locData = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificados_locales_data', []));
$a_locales = NotasFormSupport::desplegableOpciones($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'nom' => $nom,
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $idioma,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_enviado' => $f_enviado,
    'chk_firmado' => $chk_firmado,
    'aviso' => $aviso,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_adjuntar.html.twig', $a_campos);
