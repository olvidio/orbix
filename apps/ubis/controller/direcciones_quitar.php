<?php

use src\ubis\application\repositories\CasaDlRepository;
use src\ubis\application\repositories\CasaExRepository;
use src\ubis\application\repositories\CentroDlRepository;
use src\ubis\application\repositories\CentroExRepository;

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

$oUbi = $UbiRepository->findById($Qid_ubi);
$oUbi->removeDireccion($id_direccion);