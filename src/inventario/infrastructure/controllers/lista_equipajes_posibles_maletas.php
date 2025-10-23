<?php

use src\inventario\application\repositories\EgmRepository;
use src\inventario\application\repositories\EquipajeRepository;
use src\inventario\application\repositories\LugarRepository;
use web\ContestarJson;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_equipaje = (string)filter_input(INPUT_POST, 'id_equipaje');

$error_txt = '';

// generar maletas

// posibles maletas: dlb-Magatzem-->id_ubi=40
$id_ubi = 40;
// quitar las ya asignadas
// equipajes coincidentes
$EquipajeRepository = new EquipajeRepository();
$oEquipaje = $EquipajeRepository->findById($Qid_equipaje);
$f_ini_iso = $oEquipaje->getF_ini()->getIso();
$f_fin_iso = $oEquipaje->getF_fin()->getIso();
$aEquipajes = $EquipajeRepository->getEquipajesCoincidentes($f_ini_iso, $f_fin_iso);
$EgmRepository = new EgmRepository();
$aEgms = $EgmRepository->getArrayIdFromIdEquipajes($aEquipajes, 'lugar');

// último id_grupo:
$new_id_grupo = $EgmRepository->getUltimoGrupo($Qid_equipaje) + 1;

$LugarRepository = new LugarRepository();
$cLugares = $LugarRepository->getLugares(['id_ubi' => $id_ubi]);
$aOpciones = [];
foreach ($cLugares as $oLugar) {
    $id_lugar = $oLugar->getId_lugar();
    if (!in_array($id_lugar, $aEgms)) {
        $aOpciones[$id_lugar] = $oLugar->getNom_lugar();
    }
}
$aOpciones[1] = _("nuevo");
asort($aOpciones);

$data = [
    'a_opciones' => $aOpciones,
    'new_id_grupo' => $new_id_grupo,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
