<?php

// INICIO Cabecera global de URL de controlador *********************************
use personas\model\entity\GestorPersonaSacd;
use personas\model\entity\PersonaSacd;
use web\Hash;
use web\Lista;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qtarea =  (integer)filter_input(INPUT_POST, 'tarea');
$Qdia = (string)filter_input(INPUT_POST, 'dia');
$Qsemana = (integer)filter_input(INPUT_POST, 'semana');
$Qid_item = (integer)filter_input(INPUT_POST, 'id_item');

$dao_plantilla = new stdClass();
$dao_plantilla->que = 'asignar';
$dao_plantilla->id_zona = $Qid_zona;
$dao_plantilla->id_ubi = $Qid_ubi;
$dao_plantilla->tarea = $Qtarea;
$dao_plantilla->dia = $Qdia;
$dao_plantilla->semana = $Qsemana;
$dao_plantilla->id_item = $Qid_item;


$gesZonaSacd = new GestorZonaSacd();
$a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);

$a_cabeceras = ['sacd de la zona'];
$a_valores = [];
$i = 0;
$oHash = new Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/misas/controller/lista_sacd_zona.php');
foreach ($a_Id_nom as $id_nom) {
    $i++;
    $dao_plantilla->id_nom = $id_nom;
    $oHash->setArrayCamposHidden((array)$dao_plantilla);
    $param = $oHash->getParamAjax();

    //$data = json_encode((array)$param);

    $PersonaSacd = new PersonaSacd($id_nom);
    $sacd = $PersonaSacd->getNombreApellidos();
    $a_valores[$i][0] = "<span class='link' onclick='fnjs_asignar_sacd(\"$param\")' >$sacd</span>";
}

$oTabla = new Lista();
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setDatos($a_valores);

$dao_plantilla->que = 'quitar';
$oHash->setArrayCamposHidden((array)$dao_plantilla);
$param_quitar = $oHash->getParamAjax();

$a_campos = ['oPosicion' => $oPosicion,
		'oTabla' => $oTabla,
    'param_quitar' => $param_quitar,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('lista_sacd.html.twig', $a_campos);