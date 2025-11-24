<?php

use src\ubis\application\repositories\CasaDlRepository;
use src\ubis\application\repositories\CasaExRepository;
use src\ubis\application\repositories\CentroDlRepository;
use src\ubis\application\repositories\CentroExRepository;

/**
 * Esta página sirve para asignar una dirección a un determinado ubi.
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        15/5/02.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_ubi = (integer)filter_input(INPUT_POST, 'id_ubi');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
$Qid_direccion = (integer)filter_input(INPUT_POST, 'id_direccion');

switch ($Qobj_dir) {
    case "DireccionCentroDl":
        $UbiRepository = new CentroDlRepository();
        break;
    case "DireccionCentroEx":
        $UbiRepository = new CentroExRepository();
        break;
    case "DireccionCdcDl":
        $UbiRepository = new CasaDlRepository();
        break;
    case "DireccionCdcEx":
        $UbiRepository = new CasaExRepository();
        break;
}

// por defecto:
$principal = false;
$propietario = true;

$oUbi = $UbiRepository->findById($Qid_ubi);
$oUbi->addDireccion($Qid_direccion, $principal, $propietario);
