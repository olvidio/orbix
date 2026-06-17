<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\config\OrbixRuntime;
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
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$stackFromPost = list_nav_stack_from_post();
if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_boot_recordar($oPosicion, $Qrefresh);
}
list_nav_persist_recordar_entry($oPosicion, list_nav_build_return_parametros_from_post());

$Qid_item = certificados_id_item_from_sel_post();

$data = certificados_post_data(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_ver_datos', [
    'id_item' => $Qid_item,
], false));
if (!empty($data['error'])) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'aviso' => PostRequest::stripInternalCallProvenance(tessera_imprimir_string($data['error'])),
    ];
    $oView = new ViewNewTwig('frontend/certificados/controller');
    $oView->renderizar('certificado_emitido_ver.html.twig', $a_campos);
    return;
}

$ver = certificados_emitido_ver_from_payload($data);
$id_nom = $ver['id_nom'];
$nom = $ver['nom'];
$idioma = $ver['idioma'];
$destino = $ver['destino'];
$certificado = $ver['certificado'];
$f_certificado = $ver['f_certificado'];
$f_enviado = $ver['f_enviado'];
$chk_firmado = is_true($ver['firmado']) ? 'checked' : '';
$content_pdf = base64_decode($ver['content'], true);
if ($content_pdf === false) {
    $content_pdf = '';
}
$apellidos_nombre = $ver['apellidos_nombre'];

$locData = certificados_post_data(PostRequest::getDataFromUrl('/src/shared/locales_posibles', [
    'id_nom' => $id_nom,
]));
$a_locales = notas_desplegable_opciones($locData['a_locales'] ?? []);
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
