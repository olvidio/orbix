<?php

use core\ConfigGlobal;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use personas\model\entity\Persona;
use src\usuarios\application\repositories\LocalRepository;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

//Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
    $stack2 = filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 !== '') {
        $oPosicion2 = new web\Posicion();
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
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'id_tabla');
}

/////////// Consulta al backend ///////////////////
$url_backend = '/src/certificados/infrastructure/controllers/certificado_emitido_imprimir_datos.php';
$a_campos_backend = [ 'id_nom' => $id_nom ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    exit($data['error']);
}

$nombreApellidos = $data['nombreApellidos'];
$lugar_nacimiento = $data['lugar_nacimiento'];
$f_nacimiento = $data['f_nacimiento'];
$nivel_stgr = $data['nivel_stgr'];

$region_latin = $data['region_latin'];
$vstgr = $data['vstgr'];
$dir_stgr = $data['dir_stgr'];
$lugar_firma = $data['lugar_firma'];
$contador = $data['contador'];

// preguntar: nº, destino, idioma

//Idiomas
/////////// Consulta al backend ///////////////////
$url_backend = '/src/shared/infrastructure/controllers/locales_posibles.php';
$a_campos_backend = [ 'id_nom' => $id_nom ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);
if (isset($data['error'])) {
    exit($data['error']);
}

$a_locales = $data['a_locales'];

$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

$oHoy = new DateTimeLocal();
$f_certificado = $oHoy->getFromLocal();

// número de protocolo
$any = $oHoy->format('y');
$sigla = ConfigGlobal::mi_region();
$certificado = "$sigla $contador/$any";

// destino
$destino = '';

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposForm('certificado!firmado!f_certificado!idioma!destino');
$oHashCertificadoPdf->setCamposNo('firmado');
$oHashCertificadoPdf->setArrayCamposHidden(['id_nom' => $id_nom, 'nuevo' => 1]);

$pag_certificado_2_pdf = ConfigGlobal::getWeb() . '/frontend/certificados/controller/certificado_emitido_2_mpdf.php';
$oHash = new Hash();
$oHash->setUrl($pag_certificado_2_pdf);
$oHash->setCamposForm('id_item!guardar');
$h = $oHash->linkSinVal();

$pag_certificado_eliminar = ConfigGlobal::getWeb() . '/src/certificados/infrastructure/controllers/certificado_emitido_delete.php';
$oHash_e = new Hash();
$oHash_e->setUrl($pag_certificado_eliminar);
$oHash_e->setCamposForm('id_item');
$h_eliminar = $oHash_e->linkSinVal();

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