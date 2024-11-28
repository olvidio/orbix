<?php

// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\Persona;
use usuarios\model\entity\GestorLocal;
use web\DateTimeLocal;
use web\Hash;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************


$oPosicion->recordar();

$id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$oPersona = Persona::NewPersona($id_nom);
$nom = $oPersona->getApellidosNombre();
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_recibido = '';
$firmado = '';


if (empty($f_recibido)) {
    $f_recibido = (new DateTimeLocal())->getFromLocal();
}
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!f_certificado!idioma!f_recibido');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado!stack');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden([
    'id_nom' => $id_nom,
    'refresh' => 1,
]);

//Idiomas
$gesIdiomas = new GestorLocal();
$oDesplIdiomas = $gesIdiomas->getListaLocales();
$oDesplIdiomas->setNombre('idioma');
$oDesplIdiomas->setBlanco(TRUE);


$a_campos = [
    'oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'nom' => $nom,
    'oDesplIdiomas' => $oDesplIdiomas,
    'idioma' => $idioma,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_recibido' => $f_recibido,
    'chk_firmado' => $chk_firmado,
];

$oView = new core\ViewTwig('certificados/controller');
$oView->renderizar('certificado_dl_adjuntar.html.twig', $a_campos);