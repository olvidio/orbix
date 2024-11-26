<?php

// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\repositories\CertificadoDlRepository;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use core\ServerConf;
use personas\model\entity\Persona;
use usuarios\model\entity\GestorLocal;
use web\Hash;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$Qid_dossier = (integer)filter_input(INPUT_POST, 'id_dossier');
$local = empty($Qid_dossier) ? FALSE : TRUE;

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    // desde dossiers es uno nuevo
    $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_pau');
}

if ($local) {
    $CertificadoRepository = new CertificadoDlRepository();
} else {
    $CertificadoRepository = new CertificadoRepository();
}
$oCertificado = $CertificadoRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();
/*
$idioma = $oCertificado->getIdioma();
$destino = $oCertificado->getDestino();
$certificado = $oCertificado->getCertificado();
$f_certificado = $oCertificado->getF_certificado()->getFromLocal();
if ($local){
    $f_enviado = $oCertificado->getF_recibido()->getFromLocal();
} else {
    $f_enviado = $oCertificado->getF_enviado()->getFromLocal();
}
*/

$oPersona = Persona::NewPersona($id_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();
$nom = empty($nom)? $apellidos_nombre : $nom;

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposNo('certificado_pdf');
$oHashCertificadoPdf->setArrayCamposHidden(
    [
        'id_dossier' => $Qid_dossier,
        'id_item' => $Qid_item,
        'id_nom' => $id_nom,
        'solo_pdf' => 1
    ]);

$a_campos = ['oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
   'ApellidosNombre' => $apellidos_nombre,
];

$oView = new core\ViewTwig('certificados/controller');
$oView->renderizar('certificado_subir_firmado.html.twig', $a_campos);