<?php

// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\GestorPersona;
use personas\model\entity\Persona;
use web\Hash;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_nom = (integer)strtok($a_sel[0], "#");
    $id_tabla = (string)strtok("#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $id_tabla = (string)filter_input(INPUT_POST, 'tabla');
}

$oPersona = Persona::NewPersona($Qid_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();

$certificado_actual = '';

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado_num!copia!f_certificado');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!copia');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden(['id_nom' => $Qid_nom]);


$a_campos = ['oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
   'ApellidosNombre' => $apellidos_nombre,
];

$oView = new core\ViewTwig('certificados/controller');
$oView->renderizar('certificado_browse.html.twig', $a_campos);