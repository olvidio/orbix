<?php


// INICIO Cabecera global de URL de controlador *********************************
use src\casas\domain\contracts\IngresoRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****


$Qque = (string)filter_input(INPUT_POST, 'que');

$IngresoRepository = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
switch ($Qque) {
    case "update":
        $data = (string)filter_input(INPUT_POST, 'data');
        $colName = (string)filter_input(INPUT_POST, 'colName');
        $obj = json_decode($data);
        //print_r($obj);
        $dl = json_decode($colName);
        //print_r($dl);
        $id_activ = $obj->id;
        $plazas_previstas = $obj->$dl;

        $oIngreso = $IngresoRepository->finndById($id_activ);
        $oIngreso->DBCarregar();
        $oIngreso->setNum_asistentes_previstos($plazas_previstas);
        if ($oIngreso->DBGuardar() === false) {
            echo _('Hay un error, no se ha guardado');
        }
        break;
}
