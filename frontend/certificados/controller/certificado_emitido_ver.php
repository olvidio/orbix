<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
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

$Qid_item = CertificadosPostInput::idItemFromSelPost();
$navState = ListNavSupport::buildSelectionStatePatchFromPost();
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    ['id_item' => $Qid_item],
    $navState,
);
ListNavSupport::syncNavStateAt($oPosicion, 1, ListNavSupport::buildSelectionStatePatchFromPost());


$data = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_ver_datos', [
    'id_item' => $Qid_item,
], false));
if (!empty($data['error'])) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'aviso' => PostRequest::stripInternalCallProvenance(\frontend\shared\helpers\PayloadCoercion::string($data['error'])),
    ];
    $oView = new ViewNewTwig('frontend/certificados/controller');
    $oView->renderizar('certificado_emitido_ver.html.twig', $a_campos);
    return;
}

$ver = CertificadosPayload::emitidoVerFromPayload($data);
$id_nom = $ver['id_nom'];
$nom = $ver['nom'];
$idioma = $ver['idioma'];
$destino = $ver['destino'];
$certificado = $ver['certificado'];
$f_certificado = $ver['f_certificado'];
$f_enviado = $ver['f_enviado'];
$chk_firmado = \src\shared\domain\helpers\FuncTablasSupport::isTrue($ver['firmado']) ? 'checked' : '';
$content_pdf = base64_decode($ver['content'], true);
if ($content_pdf === false) {
    $content_pdf = '';
}
$apellidos_nombre = $ver['apellidos_nombre'];

$locData = CertificadosPayload::postData(PostRequest::getDataFromUrl('/src/shared/locales_posibles', [
    'id_nom' => $id_nom,
]));
$a_locales = NotasFormSupport::desplegableOpciones($locData['a_locales'] ?? []);
$oDesplIdiomas = new Desplegable('idioma', $a_locales, $idioma, true);

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!destino!f_certificado!idioma!nom!f_enviado');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado');
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_item' => $Qid_item,
    'id_nom' => $id_nom,
    'certificado_old' => $certificado,
]);

$dir_tmp = OrbixRuntime::dir() . '/log/tmp/';
$cmd_shell = "find $dir_tmp -mtime +1 -delete";
shell_exec($cmd_shell);

$filename_sin_barra = str_replace('/', '_', $certificado);
$filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
$filename_pdf = OrbixRuntime::dir() . '/log/tmp/' . $filename_sin_espacio . '.pdf';
if (($file_handle = @fopen($filename_pdf, 'wb')) !== false) {
    fwrite($file_handle, $content_pdf);
    fclose($file_handle);
    $filename_pdf_web = AppUrlConfig::getPublicAppBaseUrl() . '/log/tmp/' . $filename_sin_espacio . '.pdf';
} else {
    $filename_pdf_web = '';
}

$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $apellidos_nombre,
    'nom' => $nom,
    'idioma' => $idioma,
    'oDesplIdiomas' => $oDesplIdiomas,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_enviado' => $f_enviado,
    'chk_firmado' => $chk_firmado,
    'filename_pdf' => $filename_pdf_web,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_ver.html.twig', $a_campos);
