<?php


// INICIO Cabecera global de URL de controlador *********************************
use core\ViewTwig;
use misas\domain\repositories\InicialesSacdRepository;
//use personas\model\entity\PersonaEx;
use personas\model\entity\PersonaSacd;
use web\DateTimeLocal;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');

$columns_cuadricula = [
//    ["id" => "id_sacd", "name" => "Id sacd", "field" => "id_sacd", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "nombre_sacd", "name" => "Nombre sacd", "field" => "nombre_sacd", "width" => 250, "cssClass" => "cell-title"],
    ["id" => "iniciales", "name" => "Iniciales", "field" => "iniciales", "width" => 150, "cssClass" => "cell-title"],
    ["id" => "color", "name" => "Color", "field" => "color", "width" => 150, "cssClass" => "cell-title"],
];

$data_cuadricula = [];

$gesZonaSacd = new GestorZonaSacd();
$a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);

$a_datos_sacd = [];
foreach ($a_Id_nom as $id_nom) {
//    if ($id_nom>0) {
        $PersonaSacd = new PersonaSacd($id_nom);
        $sacd = $PersonaSacd->getNombreApellidos();
//    } else {
//        $PersonaEx = new PersonaEx($id_nom);
//        $sacd = $PersonaEx->getNombreApellidos();
//    }

    $InicialesSacdRepository = new InicialesSacdRepository();
    $InicialesSacd = $InicialesSacdRepository->findById($id_nom);
    if ($InicialesSacd === null) {
        $iniciales = '';
        $color = '';
    } else {
        $iniciales = $InicialesSacd->getIniciales();
        $color = $InicialesSacd->getColor();
    }



    $data_cols = [];
    $data_cols["id_sacd"] = $id_nom;
    $data_cols["nombre_sacd"] = $sacd;
    $data_cols["iniciales"] = $iniciales;
    $data_cols["color"] = $color;

    $data_cuadricula[] = $data_cols;
}

$json_columns_cuadricula = json_encode($columns_cuadricula);
$json_data_cuadricula = json_encode($data_cuadricula);

$url_update_iniciales = 'apps/misas/controller/update_iniciales.php';
$oHashIniciales = new Hash();
$oHashIniciales->setUrl($url_update_iniciales);
$oHashIniciales->setCamposForm('id_sacd!iniciales!color');
$h_iniciales = $oHashIniciales->linkSinVal();


$a_campos = ['oPosicion' => $oPosicion,
    'json_columns_cuadricula' => $json_columns_cuadricula,
    'json_data_cuadricula' => $json_data_cuadricula,
    'h_iniciales' => $h_iniciales,
    'id_zona' => $Qid_zona,
];

$oView = new ViewTwig('misas/controller');
echo $oView->render('ver_iniciales_zona.html.twig', $a_campos);