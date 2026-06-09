<?php

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\config\OrbixRuntime;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

// Crea los objetos de uso global **********************************************
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack2 = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack2)) { // devuelve false si no puede ir
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack2);
        }
    }
}

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
    $id_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
} else {
    $id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

/////////// Consulta al backend ///////////////////
$url_backend = '/src/certificados/certificado_emitido_imprimir_datos';
$a_campos_backend = ['id_nom' => $id_nom];
$datosPersona = PostRequest::getDataFromUrl($url_backend, $a_campos_backend, false);
if (!empty($datosPersona['error'])) {
    $a_campos = [
        'oPosicion' => $oPosicion,
        'aviso' => PostRequest::stripInternalCallProvenance((string)$datosPersona['error']),
    ];
    $oView = new ViewNewTwig('frontend/certificados/controller');
    $oView->renderizar('certificado_emitido_imprimir.html.twig', $a_campos);
    return;
}

$nombreApellidos = $datosPersona['nombreApellidos'];
$lugar_nacimiento = $datosPersona['lugar_nacimiento'];
$f_nacimiento = $datosPersona['f_nacimiento'];
$nivel_stgr = $datosPersona['nivel_stgr'];

$region_latin = $datosPersona['region_latin'];
$vstgr = $datosPersona['vstgr'];
$dir_stgr = $datosPersona['dir_stgr'];
$lugar_firma = $datosPersona['lugar_firma'];
$contador = $datosPersona['contador'];

$f_certificado = (string)($datosPersona['f_certificado'] ?? '');
$any = (string)($datosPersona['any_2digit'] ?? '');

// preguntar: nº, destino, idioma

//Idiomas
/////////// Consulta al backend ///////////////////
$url_backend = '/src/shared/locales_posibles';
$a_campos_backend = ['id_nom' => $id_nom];
$dataLocales = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$a_locales = $dataLocales['a_locales'];

$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

// número de protocolo (sigla regional en el frontend; año en 2 cifras desde el backend)
$sigla = OrbixRuntime::miRegion();
$certificado = "$sigla $contador/$any";

// destino
$destino = '';

$oHashCertificadoPdf = new HashFront();
$oHashCertificadoPdf->setCamposForm('certificado!firmado!f_certificado!idioma!destino');
$oHashCertificadoPdf->setCamposNo('firmado');
$oHashCertificadoPdf->setArrayCamposHidden(['id_nom' => $id_nom, 'nuevo' => 1]);

$pag_certificado_2_pdf = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/certificados/controller/certificado_emitido_2_mpdf.php';
$oHash = new HashFront();
$oHash->setUrl($pag_certificado_2_pdf);
$oHash->setCamposForm('id_item!guardar');
// Tras `?guardar=1&id_item=…` hace falta `&hnov=…` (linkSinValParams), no otro `?` (linkSinVal).
$h = $oHash->linkSinValParams();

$pag_certificado_eliminar = AppUrlConfig::getApiBaseUrl() . '/src/certificados/certificado_emitido_delete';
$oHash_e = new HashFront();
$oHash_e->setUrl($pag_certificado_eliminar);
$oHash_e->setCamposForm('id_item');
$h_eliminar = $oHash_e->linkSinValParams();

$a_campos = ['oPosicion' => $oPosicion,
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