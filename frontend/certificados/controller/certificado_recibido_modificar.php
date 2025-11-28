<?php

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\model\ViewNewTwig;
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use function core\is_true;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

$oPosicion->recordar();

$Qid_item = (integer)strtok($a_sel[0], "#");
// el scroll id es de la página anterior, hay que guardarlo allí
$oPosicion->addParametro('id_sel', $a_sel, 1);
$scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
$oPosicion->addParametro('scroll_id', $scroll_id, 1);

$certificadoRecibidoRepository = $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);
$oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);

$id_nom = $oCertificadoRecibido->getId_nom();
$nom = $oCertificadoRecibido->getNom();
$idioma = $oCertificadoRecibido->getIdioma();
$destino = $oCertificadoRecibido->getDestino();
$certificado = $oCertificadoRecibido->getCertificado();
$f_certificado = $oCertificadoRecibido->getF_certificado()->getFromLocal();
$f_recibido = $oCertificadoRecibido->getF_recibido()->getFromLocal();
$firmado = $oCertificadoRecibido->isFirmado();


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
$LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $LocalRepository->getArrayLocales();
$oDesplIdiomas = new Desplegable('idioma', $a_locales, $idioma, true);

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

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_recibido_adjuntar.html.twig', $a_campos);