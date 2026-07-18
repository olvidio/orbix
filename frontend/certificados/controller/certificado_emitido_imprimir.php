<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\certificados\helpers\CertificadosPostInput;
use frontend\certificados\helpers\CertificadosPayload;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

$id_nom = CertificadosPostInput::idNomFromSelPost();

$navState = ListNavSupport::buildCertificadoImprimirParentReturnParametros();
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['id_nom' => $id_nom],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildCertificadoImprimirParentReturnParametros());

$datosPersona = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_imprimir_datos', [
    'id_nom' => $id_nom,
], false));
if (!empty($datosPersona['error'])) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'aviso' => PostRequest::stripInternalCallProvenance(\frontend\shared\helpers\PayloadCoercion::string($datosPersona['error'])),
    ];
    $oView = new ViewNewTwig('frontend/certificados/controller');
    $oView->renderizar('certificado_emitido_imprimir.html.twig', $a_campos);
    return;
}

$personaData = CertificadosPayload::imprimirPersonaFromPayload($datosPersona);
$nombreApellidos = $personaData['nombreApellidos'];
$f_certificado = $personaData['f_certificado'];
$any = $personaData['any'];

$locData = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/shared/locales_posibles', [
    'id_nom' => $id_nom,
]));
$a_locales = NotasFormSupport::desplegableOpciones($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

$sigla = OrbixRuntime::miRegion();
$certificado = $sigla . ' ' . $personaData['contador'] . '/' . $any;
$destino = '';

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado!firmado!f_certificado!idioma!destino');
$oHashCertificadoPdf->setCamposNo('firmado');
$oHashCertificadoPdf->setArrayCamposHidden(['id_nom' => $id_nom, 'nuevo' => 1]);

$pag_certificado_2_pdf = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/certificados/controller/certificado_emitido_2_mpdf.php';
$oHash = new HashFront();
$oHash->setUrl($pag_certificado_2_pdf);
$oHash->setCamposForm('id_item!guardar');
$h = $oHash->linkSinValParams();

$pag_certificado_eliminar = AppUrlConfig::srcBrowserUrl('/src/certificados/certificado_emitido_delete');
$oHash_e = new HashFront();
$oHash_e->setUrl($pag_certificado_eliminar);
$oHash_e->setCamposForm('id_item');
$h_eliminar = $oHash_e->linkSinValParams();

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'nombreApellidos' => $nombreApellidos,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'destino' => $destino,
    'oDesplIdiomas' => $oDesplIdiomas,
    'pag_certificado_2_pdf' => $pag_certificado_2_pdf,
    'pag_certificado_eliminar' => $pag_certificado_eliminar,
    'h' => $h,
    'h_eliminar' => $h_eliminar,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_imprimir.html.twig', $a_campos);
