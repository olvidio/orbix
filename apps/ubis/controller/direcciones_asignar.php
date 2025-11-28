<?php


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
use src\ubis\domain\contracts\CasaDlRepositoryInterface;
use src\ubis\domain\contracts\CasaExRepositoryInterface;
use src\ubis\domain\contracts\CentroDlRepositoryInterface;
use src\ubis\domain\contracts\CentroExRepositoryInterface;

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
        $UbiRepository = $GLOBALS['container']->get(CentroDlRepositoryInterface::class);
        break;
    case "DireccionCentroEx":
        $UbiRepository = $GLOBALS['container']->get(CentroExRepositoryInterface::class);
        break;
    case "DireccionCdcDl":
        $UbiRepository = $GLOBALS['container']->get(CasaDlRepositoryInterface::class);
        break;
    case "DireccionCdcEx":
        $UbiRepository = $GLOBALS['container']->get(CasaExRepositoryInterface::class);
        break;
}

// por defecto:
$principal = false;
$propietario = true;

$oUbi = $UbiRepository->findById($Qid_ubi);
$oUbi->addDireccion($Qid_direccion, $principal, $propietario);
