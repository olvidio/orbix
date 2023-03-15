<?php

// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\GestorPersonaSacd;
use personas\model\entity\PersonaSacd;
use web\Lista;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qque =  (integer)filter_input(INPUT_POST, 'que');
$Qcolumn = (integer)filter_input(INPUT_POST, 'column');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$dao_plantilla = new stdClass();
$dao_plantilla->id_zona = $Qid_zona;
$dao_plantilla->id_ubi = $Qid_ubi;
$dao_plantilla->que = $Qque;
$dao_plantilla->column = $Qcolumn;
$dao_plantilla->id_item = $Qid_item;


$gesZonaSacd = new GestorZonaSacd();
$a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);

$a_cabeceras = ['sacd de la zona'];
$a_valores = [];
$i = 0;
foreach ($a_Id_nom as $id_nom) {
    $i++;
    $dao_plantilla->id_nom = $id_nom;
    $data = json_encode((array)$dao_plantilla);
    $PersonaSacd = new PersonaSacd($id_nom);
    $sacd = $PersonaSacd->getNombreApellidos();
    $a_valores[$i][0] = "<span class=link onclick=\"fnjs_asignar_sacd('$data');\">$sacd</span>";
}

$oTabla = new Lista();
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$a_campos = ['oPosicion' => $oPosicion,
		'oTabla' => $oTabla,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('lista_sacd.html.twig', $a_campos);