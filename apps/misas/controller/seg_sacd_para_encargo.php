<?php

// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\entity\Encargo;
use encargossacd\model\entity\EncargoHorario;
use personas\model\entity\PersonaSacd;
use web\Desplegable;
use web\Hash;
use zonassacd\model\entity\GestorZonaSacd;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qtarea = (integer)filter_input(INPUT_POST, 'tarea');
$Qdia_ref = (string)filter_input(INPUT_POST, 'dia');
$Qsemana = (integer)filter_input(INPUT_POST, 'semana');
$Qid_item_h = (integer)filter_input(INPUT_POST, 'id_item_h');
$Qid_sacd = (integer)filter_input(INPUT_POST, 'id_sacd');
$Qid_item_horario_sacd = (integer)filter_input(INPUT_POST, 'id_item_horario_sacd');

// Datos del encargo:
$oEncargo = new Encargo($Qid_enc);
$nombre_encargo = $oEncargo->getDesc_enc();

$oEncargoGorario = new EncargoHorario($Qid_item_h);
$num_sacd = $oEncargoGorario->getN_sacd();
$h_ini = $oEncargoGorario->getH_ini();
$h_fin = $oEncargoGorario->getH_fin();

$gesZonaSacd = new GestorZonaSacd();
$a_Id_nom = $gesZonaSacd->getSacdsZona($Qid_zona);

$oHash = new Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/misas/controller/asignar_sacd.php');
$a_campos_hidden = [
    'id_item_h' => $Qid_item_h,
    'id_item_horario_sacd' => $Qid_item_horario_sacd,
    'id_enc' => $Qid_enc,
    'id_zona' => $Qid_zona,
    'id_ubi' => $Qid_ubi,
    'tarea' => $Qtarea,
    'dia_ref' => $Qdia_ref,
    'semana' => $Qsemana,
];

$oHash->setArrayCamposHidden($a_campos_hidden);
$oHash->setCamposForm('id_sacd');

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
$oDesplSacd->setOpcion_sel($Qid_sacd);

$a_campos = ['oPosicion' => $oPosicion,
    'nombre_encargo' => $nombre_encargo,
    'num_sacd' => $num_sacd,
    'h_ini' => $h_ini,
    'h_fin' => $h_fin,
    'oHash' => $oHash,
    'dia_ref' => $Qdia_ref,
    'oDesplSacd' => $oDesplSacd,

];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('sacd_para_encargo.html.twig', $a_campos);