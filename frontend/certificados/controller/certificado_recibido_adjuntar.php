<?php

// INICIO Cabecera global de URL de controlador *********************************
use frontend\shared\model\ViewNewTwig;
use src\personas\domain\entity\Persona;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use function core\is_true;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$id_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qnuevo = (integer)filter_input(INPUT_POST, 'nuevo');
$oPersona = Persona::findPersonaEnGlobal($id_nom);
$nom = $oPersona->getApellidosNombre();
$idioma = '';
$destino = '';
$certificado = '';
$f_certificado = '';
$f_recibido = '';
$firmado = '';

$f_recibido = (new DateTimeLocal())->getFromLocal();
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
    'nuevo' => $Qnuevo,
    'refresh' => 1,
]);

//Idiomas
$LocalRepository = $GLOBALS['container']->get(LocalRepositoryInterface::class);
$a_locales = $LocalRepository->getArrayLocales();
$oDesplIdiomas = new Desplegable('idioma', $a_locales, '', true);

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