<?php

// INICIO Cabecera global de URL de controlador *********************************
use encargossacd\model\entity\GestorEncargoHorario;
use web\Hash;
use web\Lista;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_zona = (integer)filter_input(INPUT_POST, 'id_zona');
$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qid_enc = (integer)filter_input(INPUT_POST, 'id_enc');
$Qdia_ref = (string)filter_input(INPUT_POST, 'dia');

$gesEncargoHorario = new GestorEncargoHorario();
$cEncargosHorario = $gesEncargoHorario->getEncargoHorarios(['id_enc' => $Qid_enc]);

$a_horario = [];
$i = 0;
foreach ($cEncargosHorario as $oEncargoHorario) {
    $i++;
    $id_item_h = $oEncargoHorario->getId_item_h();
    $f_ini = $oEncargoHorario->getF_ini()->getFromLocal();
    $f_fin = $oEncargoHorario->getF_fin()->getFromLocal();
    $dia_ref = $oEncargoHorario->getDia_ref();
    $h_ini = $oEncargoHorario->getH_ini();
    $h_fin = $oEncargoHorario->getH_fin();

    $a_horario[$i]['id_item_h'] = $id_item_h;
    $a_horario[$i]['f_ini'] = $f_ini;
    $a_horario[$i]['f_fin'] = $f_fin;
    $a_horario[$i]['dia_ref'] = $dia_ref;
    $a_horario[$i]['h_ini'] = $h_ini;
    $a_horario[$i]['h_fin'] = $h_fin;
}

$oHashAdd = new Hash();
$oHashAdd->setArrayCamposHidden(['id_enc' => $Qid_enc, 'mod' => "nuevo"]);
$oHashAdd->setCamposForm('dia_ref!f_fin!f_ini!h_fin!h_ini');

$a_campos = ['oPosicion' => $oPosicion,
    'a_horario' => $a_horario,
    'id_enc' => $Qid_enc,
    'oHashAdd' => $oHashAdd,
];

$oView = new core\ViewTwig('misas/controller');
echo $oView->render('definir_horario_encargo.html.twig', $a_campos);