<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/certificados_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$oPosicion->recordar();

$Qid_sel = '';
$Qscroll_id = '';
if (isset($_POST['stack'])) {
    $stack2 = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack2)) {
            $Qid_sel = tessera_imprimir_string($oPosicion2->getParametro('id_sel'));
            $Qscroll_id = tessera_imprimir_string($oPosicion2->getParametro('scroll_id'));
            $oPosicion2->olvidar($stack2);
        }
    }
}

$id_nom = certificados_id_nom_from_sel_post();

$datosPersona = certificados_post_data(PostRequest::getDataFromUrl('/src/certificados/certificado_emitido_imprimir_datos', [
    'id_nom' => $id_nom,
], false));
if (!empty($datosPersona['error'])) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'aviso' => PostRequest::stripInternalCallProvenance(tessera_imprimir_string($datosPersona['error'])),
    ];
    $oView = new ViewNewTwig('frontend/certificados/controller');
    $oView->renderizar('certificado_emitido_imprimir.html.twig', $a_campos);
    return;
}

$personaData = certificados_imprimir_persona_from_payload($datosPersona);
$nombreApellidos = $personaData['nombreApellidos'];
$f_certificado = $personaData['f_certificado'];
$any = $personaData['any'];

$locData = certificados_post_data(PostRequest::getDataFromUrl('/src/shared/locales_posibles', [
    'id_nom' => $id_nom,
]));
$a_locales = notas_desplegable_opciones($locData['a_locales'] ?? []);
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

$pag_certificado_eliminar = AppUrlConfig::getApiBaseUrl() . '/src/certificados/certificado_emitido_delete';
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
