<?php

// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\repositories\CertificadoRepository;
use core\ViewTwig;
use personas\model\entity\Persona;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

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

$CertificadoRepository = new CertificadoRepository();
$oCertificado = $CertificadoRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$nom = $oCertificado->getNom();

$oPersona = Persona::NewPersona($id_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();
$nom = empty($nom) ? $apellidos_nombre : $nom;

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposNo('certificado_pdf');
$oHashCertificadoPdf->setArrayCamposHidden(
    [
        'id_item' => $Qid_item,
        'id_nom' => $id_nom,
        'solo_pdf' => 1
    ]);

$a_campos = ['oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $apellidos_nombre,
];

$oView = new ViewTwig('certificados/controller');
$oView->renderizar('certificado_subir_firmado.html.twig', $a_campos);