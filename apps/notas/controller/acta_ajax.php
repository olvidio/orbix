<?php

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;

/**
 * Esta página sirve para dar una lista de examinadores para los inputs autocomplete
 *
 *
 * @package    delegacion
 * @subpackage    estudios
 * @author    Daniel Serrabou
 * @since        19/08/15.
 *
 */

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)filter_input(INPUT_POST, 'que');
$sQuery = (string)filter_input(INPUT_POST, 'search');

switch ($Qque) {
    case 'examinadores':
        $ActaTribunalDlRepository = $GLOBALS['container']->get(ActaTribunalDlRepositoryInterface::class);
        $json = $ActaTribunalDlRepository->getJsonExaminadores($sQuery);
        break;
    case 'asignaturas':
        $AsignaturaRepository = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $json = $AsignaturaRepository->getJsonAsignaturas(array('nombre_asignatura' => $sQuery));
        break;
}
echo $json;