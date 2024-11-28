<?php

use core\ConfigGlobal;
use personas\model\entity as personas;
use usuarios\model\entity\GestorLocal;
use web\DateTimeLocal;
use web\Hash;

/**
 * Funciones más comunes de la aplicación
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
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

$oPersona = personas\Persona::NewPersona($id_nom);
if (!is_object($oPersona)) {
    $msg_err = "<br>$oPersona con id_nom: $id_nom en  " . __FILE__ . ": line " . __LINE__;
    exit($msg_err);
}
$nombreApellidos = $oPersona->getNombreApellidos();
$lugar_nacimiento = $oPersona->getLugar_nacimiento();
$f_nacimiento = $oPersona->getF_nacimiento()->getFechaLatin();
$nivel_stgr = $oPersona->getStgr();

$region_latin = $_SESSION['oConfig']->getNomRegionLatin();
$vstgr = $_SESSION['oConfig']->getNomVstgr();
$dir_stgr = $_SESSION['oConfig']->getDirStgr();
$lugar_firma = $_SESSION['oConfig']->getLugarFirma();

// preguntar: nº, destino, idioma

//Idiomas
$gesIdiomas = new GestorLocal();
$oDesplIdiomas = $gesIdiomas->getListaLocales();
$oDesplIdiomas->setNombre('idioma');
$oDesplIdiomas->setBlanco(TRUE);

$oHoy = new DateTimeLocal();
$f_certificado = $oHoy->getFromLocal();
// número de protocolo
$any = $oHoy->format('y');
$sigla = ConfigGlobal::mi_region();
$num = $_SESSION['oConfig']->getContador_certificados();

$certificado = "$sigla $num/$any";

// destino
$destino = '';

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposForm('certificado!firmado!f_certificado!idioma!destino');
$oHashCertificadoPdf->setCamposNo('firmado');
$oHashCertificadoPdf->setArrayCamposHidden(['id_nom' => $id_nom, 'nuevo' => 1]);

$pag_certificado_2_pdf = ConfigGlobal::getWeb() . '/apps/certificados/controller/certificado_2_mpdf.php';
$oHash = new Hash();
$oHash->setUrl($pag_certificado_2_pdf);
$oHash->setCamposForm('id_item!guardar');
$h = $oHash->linkSinVal();

$pag_certificado_eliminar = ConfigGlobal::getWeb() . '/apps/certificados/controller/certificado_delete.php';
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

$oView = new core\ViewTwig('certificados/controller');
$oView->renderizar('certificado_imprimir.html.twig', $a_campos);
