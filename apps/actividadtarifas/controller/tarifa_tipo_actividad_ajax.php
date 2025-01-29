<?php

use actividadtarifas\model\entity\GestorTipoActivTarifa;
use actividadtarifas\model\entity\TipoActivTarifa;
use actividadtarifas\model\entity\TipoTarifa;
use core\ConfigGlobal;
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
        $oGesTipoActivTarifas = new GestorTipoActivTarifa();
        $cTipoActivTarifas = $oGesTipoActivTarifas->getTipoActivTarifas(array('_ordre' => 'substring(id_tipo_activ::text,1)'));
        $i = 0;
        $a_cabeceras = [];
        $a_valores = [];
        foreach ($cTipoActivTarifas as $oTipoActivTarifa) {
            $i++;
            $id_item = $oTipoActivTarifa->getId_item();
            $id_tarifa = $oTipoActivTarifa->getId_tarifa();
            $id_tipo_activ = $oTipoActivTarifa->getId_tipo_activ();
            $id_serie = $oTipoActivTarifa->getId_serie();

            $oTipoActividad = new TiposActividades($id_tipo_activ);
            $isfsv = $oTipoActividad->getSfsvId();
            $oTipoTarifa = new TipoTarifa(array('id_tarifa' => $id_tarifa));

            $nom_tipo = $oTipoActividad->getNom();
            $modo = $oTipoTarifa->getModo();
            if (!empty($modo)) {
                $modo_txt = _("total");
            } else {
                $modo_txt = _("por dia");
            }

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
            if ($miSfsv == $isfsv && $_SESSION['oPerm']->have_perm_oficina('adl')) {
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
            $oTipoActivTarifa = new TipoActivTarifa();
        } else {
            $oTipoActivTarifa = new TipoActivTarifa($Qid_item);
            $oTipoActivTarifa->DBCarregar();
        }
        $oTipoActivTarifa->setId_tarifa($Qid_tarifa);
        $oTipoActivTarifa->setId_serie(TipoActivTarifa::S_GENERAL);
        $oTipoActivTarifa->setId_tipo_activ($Qid_tipo_activ);
        if ($oTipoActivTarifa->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado");
            echo "\n" . $oTipoActivTarifa->getErrorTxt();
        }
        break;
    case "eliminar":
        $Qid_item = (string)filter_input(INPUT_POST, 'id_item');
        $oTipoActivTarifa = new TipoActivTarifa();
        $oTipoActivTarifa->setId_item($Qid_item);
        if ($oTipoActivTarifa->DBEliminar() === false) {
            echo _("hay un error, no se ha borrado");
        }
        break;
}

