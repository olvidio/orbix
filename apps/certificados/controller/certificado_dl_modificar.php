<?php

// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\repositories\CertificadoDlRepository;
use core\ViewTwig;
use web\DateTimeLocal;
use web\Hash;
use usuarios\model\entity\GestorLocal;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$oPosicion->recordar();

$Qid_item = (integer)strtok($a_sel[0], "#");
// el scroll id es de la página anterior, hay que guardarlo allí
$oPosicion->addParametro('id_sel', $a_sel, 1);
$scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$oPosicion->addParametro('scroll_id', $scroll_id, 1);

$CertificadoDLRepository = new CertificadoDlRepository();
$oCertificado = $CertificadoDLRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();
$idioma = $oCertificado->getIdioma();
$destino = $oCertificado->getDestino();
$certificado = $oCertificado->getCertificado();
$f_certificado = $oCertificado->getF_certificado()->getFromLocal();
$f_recibido = $oCertificado->getF_recibido()->getFromLocal();
$firmado = $oCertificado->isFirmado();


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
    'id_item' => $Qid_item,
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

$oView = new ViewTwig('certificados/controller');
$oView->renderizar('certificado_dl_adjuntar.html.twig', $a_campos);