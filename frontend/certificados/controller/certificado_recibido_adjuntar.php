<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use function frontend\shared\helpers\is_true;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/certificados_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';
$oPosicion = FrontBootstrap::boot();
list_nav_boot_recordar($oPosicion);
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());


$id_nom = certificados_id_nom_from_sel_post();
$Qnuevo = (int)filter_input(INPUT_POST, 'nuevo');
$formData = certificados_post_data(PostRequest::getDataFromUrl('/src/certificados/certificado_recibido_adjuntar_data', [
    'id_nom' => $id_nom,
], false));
if (!empty($formData['error'])) {
    echo PostRequest::stripInternalCallProvenance(tessera_imprimir_string($formData['error']));
    return;
}
$form = certificados_adjuntar_form_from_payload($formData);
$aviso = $form['aviso'];
$nom = $form['nom'];
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_recibido = $form['f_enviado'];
$firmado = '';
$chk_firmado = is_true($firmado) ? 'checked' : '';

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_recibido');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'nuevo' => $Qnuevo,
    'refresh' => 1,
]);

$locData = certificados_post_data(PostRequest::getDataFromUrl('/src/certificados/certificados_locales_data', []));
$a_locales = notas_desplegable_opciones($locData['a_locales'] ?? []);
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
