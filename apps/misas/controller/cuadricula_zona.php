<?php


// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\PersonaSacd;
use web\Desplegable;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$gesZonaSacd = new GestorZonaSacd();
$a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);

foreach ($a_Id_nom as $id_nom) {
    $PersonaSacd = new PersonaSacd($id_nom);
    $sacd = $PersonaSacd->getNombreApellidos();
    // iniciales
    $nom = mb_substr($PersonaSacd->getNom(), 0, 1);
    $ap1 = mb_substr($PersonaSacd->getApellido1(), 0, 1);
    $ap2 = mb_substr($PersonaSacd->getApellido2(), 0, 1);
    $iniciales = strtoupper($nom.$ap1.$ap2);

    $key = $id_nom.'#'.$iniciales;

    $a_sacd[$key] = $sacd ?? '?';
}

$oDesplSacd = new Desplegable();
$oDesplSacd->setNombre('id_sacd');
$oDesplSacd->setOpciones($a_sacd);
$oDesplSacd->setBlanco(TRUE);






$array_cuadricula = [

];


$json_cuadricula = json_encode($array_cuadricula);

$encargo_descripcion = "misas Sarria";

$a_campos = ['oPosicion' => $oPosicion,
    'oDesplSacd' => $oDesplSacd,
    'json_cuadricula' => $json_cuadricula,
    'encargo_descripcion' => $encargo_descripcion,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('ver_cuadricula_zona.html.twig', $a_campos);