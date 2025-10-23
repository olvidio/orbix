<?php


// INICIO Cabecera global de URL de controlador *********************************
use actividades\model\entity\ActividadAll;
use src\inventario\application\repositories\EquipajeRepository;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (int)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';


$EquipajesRepository = new EquipajeRepository();
$oEquipaje = $EquipajesRepository->findById($Qid_equipaje);

$ids_actividades = $oEquipaje->getIds_activ();
$aId_activ = explode(',', $ids_actividades);
$a = 0;
$a_actividades = [];
foreach ($aId_activ as $id_activ) {
    $a++;
    $oActividad = new ActividadAll($id_activ);
    $nom_activ = $oActividad->getNom_activ();
    if (empty($nom_activ)) {
        $nom_activ = sprintf(_("OJO! No se encuentra la actividad con id: %s"), $id_activ);
    }
    $a_actividades[$a] = $nom_activ;
}

$data = [
    'a_actividades' => $a_actividades,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);