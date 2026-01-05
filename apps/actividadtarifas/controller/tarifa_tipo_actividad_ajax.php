<?php

use core\ConfigGlobal;
use src\actividadtarifas\domain\contracts\RelacionTarifaTipoActividadRepositoryInterface;
use src\actividadtarifas\domain\contracts\TipoTarifaRepositoryInterface;
use src\actividadtarifas\domain\entity\RelacionTarifaTipoActividad;
use src\actividadtarifas\domain\value_objects\SerieId;
use src\actividadtarifas\domain\value_objects\TarifaModoId;
use web\Lista;
use web\TiposActividades;

/**
 * Esta página sirve para ejecutar las operaciones de guardar, eliminar, listar...
 * que se piden desde: act_tipo_tarifas.php y act_tipo_tarifa_form.php
 *
 * @package    delegacion
 * @subpackage    actividades
 * @author    Daniel Serrabou
 * @since        22/12/2010.
 *
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case "get":
        $miSfsv = ConfigGlobal::mi_sfsv();
        // listado de tarifas asociadas a tipos de actividad.
        $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $cTipoActivTarifas = $RelacionTarifaTipoActividadRepository->getTipoActivTarifas(['_ordre' => 'substring(id_tipo_activ::text,1)']);
        $i = 0;
        $a_cabeceras = [];
        $a_valores = [];
        $TipoTarifaRepository = $GLOBALS['container']->get(TipoTarifaRepositoryInterface::class);
        $a_modos_tarifa = TarifaModoId::getArrayModo();
        foreach ($cTipoActivTarifas as $oRelacionTarifaTipoActividad) {
            $i++;
            $id_item = $oRelacionTarifaTipoActividad->getId_item();
            $id_tarifa = $oRelacionTarifaTipoActividad->getId_tarifa();
            $id_tipo_activ = $oRelacionTarifaTipoActividad->getId_tipo_activ();
            $id_serie = $oRelacionTarifaTipoActividad->getId_serie();

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            $oTipoTarifa = $TipoTarifaRepository->findById($id_tarifa);

            $nom_tipo = $oTipoActividad->getNom();
            $modo = $oTipoTarifa->getModo();
            $modo_txt = $a_modos_tarifa[$modo];

            $nombre_tarifa = $oTipoTarifa->getLetra();
            /*
            if ($id_serie !== TipoActivTarifa::S_GENERAL) {
                $aTipoSerie = $oTipoActivTarifa->getArraySerie();
                $nombre_tarifa .= " (" . $aTipoSerie[$id_serie] . ")";
            }
            */

            $nombre_tarifa .= "  ($modo_txt)";

            $a_valores[$i][1] = $nom_tipo;
            $a_valores[$i][2] = $nombre_tarifa;
            // permiso
            if ($miSfsv === $isfsv && $_SESSION['oPerm']->have_perm_oficina('adl')) {
                $script = "fnjs_modificar($id_item)";
                $a_valores[$i][3] = array('script' => $script, 'valor' => _("modificar"));
            }
        }
        $a_cabeceras[] = _("tipo actividad");
        $a_cabeceras[] = _("tarifa");
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        // sólo pueden añadir: adl, pr i actividades
        if (($_SESSION['oPerm']->have_perm_oficina('adl')) || ($_SESSION['oPerm']->have_perm_oficina('pr')) || ($_SESSION['oPerm']->have_perm_oficina('calendario'))) {
            echo '<br><span class="link" onclick="fnjs_modificar(\'nuevo\');">' . _("añadir tarifa tipo") . '</span>';
        }
        break;
    case "update":
        $Qid_item = (string)filter_input(INPUT_POST, 'id_item');
        $Qid_tarifa = (string)filter_input(INPUT_POST, 'id_tarifa');
        //$Qid_serie = (string)filter_input(INPUT_POST, 'id_serie');
        $Qid_tipo_activ = (string)filter_input(INPUT_POST, 'id_tipo_activ');

        if ($Qid_item === 'nuevo') {
            $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
            $newId = $RelacionTarifaTipoActividadRepository->nextId();
            $oRelacionTarifaTipoActividad = new RelacionTarifaTipoActividad();
            $oRelacionTarifaTipoActividad->setId_item($newId);
        } else {
            $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
            $oRelacionTarifaTipoActividad = $RelacionTarifaTipoActividadRepository->findById($Qid_item);
        }
        $oRelacionTarifaTipoActividad->setId_tarifa($Qid_tarifa);
        $oRelacionTarifaTipoActividad->setId_serie(SerieId::GENERAL);
        $oRelacionTarifaTipoActividad->setId_tipo_activ($Qid_tipo_activ);
        if ($RelacionTarifaTipoActividadRepository->Guardar($oRelacionTarifaTipoActividad) === false) {
            echo _("hay un error, no se ha guardado");
        }
        break;
    case "eliminar":
        $Qid_item = (string)filter_input(INPUT_POST, 'id_item');
        $RelacionTarifaTipoActividadRepository = $GLOBALS['container']->get(RelacionTarifaTipoActividadRepositoryInterface::class);
        $oRelacionTarifaTipoActividad = $RelacionTarifaTipoActividadRepository->findById($Qid_item);
        if ($RelacionTarifaTipoActividadRepository->Eliminar($oRelacionTarifaTipoActividad) === false) {
            echo _("hay un error, no se ha borrado");
        }
        break;
}

