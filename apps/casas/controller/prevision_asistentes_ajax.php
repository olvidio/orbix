<?php


// INICIO Cabecera global de URL de controlador *********************************
use src\casas\domain\contracts\IngresoRepositoryInterface;
use web\ContestarJson;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****


$Qque = (string)filter_input(INPUT_POST, 'que');

$IngresoRepository = $GLOBALS['container']->get(IngresoRepositoryInterface::class);
$error = '';
switch ($Qque) {
    case "update":
        $data = (string)filter_input(INPUT_POST, 'data');
        $colName = (string)filter_input(INPUT_POST, 'colName');
        $obj = json_decode($data);
        $dl = json_decode($colName);
        $id_activ = $obj->id ?? 0;
        $plazas_previstas = $dl !== null ? ($obj->$dl ?? 0) : 0;

        $oIngreso = $IngresoRepository->finndById($id_activ);
        if ($oIngreso === null) {
            $error = (string)_('no se encuentra el ingreso');
            break;
        }
        $oIngreso->setNum_asistentes_previstos($plazas_previstas);
        if ($IngresoRepository->Guardar($oIngreso) === false) {
            $error = (string)_('Hay un error, no se ha guardado');
        }
        break;
}

ContestarJson::enviar($error, 'ok');
