<?php


/**
 * Esta página quita la dirección de un ubi.
 *
 * Se le pasan las var:
 *
 * @package    delegacion
 * @subpackage    ubis
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
$Qidx = (integer)filter_input(INPUT_POST, 'idx');
$Qobj_dir = (string)filter_input(INPUT_POST, 'obj_dir');
// id_direccion es string, porque puede ser una lista de varios separados por coma
$Qid_direccion = (string)filter_input(INPUT_POST, 'id_direccion');

// puede haber más de una dirección
$a_id_direccion = explode(',', $Qid_direccion);
$id_direccion = $a_id_direccion[$Qidx];

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

$oUbi = $UbiRepository->findById($Qid_ubi);
$oUbi->removeDireccion($id_direccion);