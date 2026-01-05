<?php
/**
 * Controlador para las peticiones ajax desde peticiones_activ.php
 *
 * acción (que): update|borrar
 */


// INICIO Cabecera global de URL de controlador *********************************
use src\actividadplazas\domain\contracts\PlazaPeticionRepositoryInterface;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
$Qsactividad = (string)filter_input(INPUT_POST, 'sactividad');
$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case "update":
        // borro todo y grabo lo nuevo:
        $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion(array('id_nom' => $Qid_nom, 'tipo' => $Qsactividad));
        foreach ($cPlazasPeticion as $oPlazaPeticion) {
            $PlazaPeticionRepository->Eliminar($oPlazaPeticion);
        }
        // grabar
        $i = 0;
        $a_actividades = (array)filter_input(INPUT_POST, 'actividades', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        foreach ($a_actividades as $id_activ) {
            if (empty($id_activ)) {
                continue;
            }
            $i++;
            $oPlazaPeticion = $PlazaPeticionRepository->findById($Qid_nom, $id_activ);
            $oPlazaPeticion->setOrden($i);
            $oPlazaPeticion->setTipo($Qsactividad);
            $PlazaPeticionRepository->Guardar($oPlazaPeticion);
        }
        echo $oPosicion->go_atras(1);
        break;
    case 'borrar';
        $PlazaPeticionRepository = $GLOBALS['container']->get(PlazaPeticionRepositoryInterface::class);
        $cPlazasPeticion = $PlazaPeticionRepository->getPlazasPeticion(array('id_nom' => $Qid_nom, 'tipo' => $Qsactividad));
        foreach ($cPlazasPeticion as $oPlazaPeticion) {
            $PlazaPeticionRepository->Eliminar($oPlazaPeticion);
        }
        break;
}