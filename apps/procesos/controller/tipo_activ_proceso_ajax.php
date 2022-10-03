<?php

use actividades\model\entity\GestorTipoDeActividad;
use actividades\model\entity\TipoDeActividad;
use core\ConfigGlobal;
use function core\is_true;
use procesos\model\entity\GestorProcesoTipo;
use web\Lista;
use web\TiposActividades;

// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


$Qque = (string)\filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'lista':
        $aWhere = ['_ordre' => 'id_tipo_activ'];
        $oGesTiposDeActividades = new GestorTipoDeActividad();
        $cTiposDeActividades = $oGesTiposDeActividades->getTiposDeActividades($aWhere);

        $oGesProcesosTipo = new GestorProcesoTipo();
        $cProcesosTipo = $oGesProcesosTipo->getProcesoTipos();
        $a_procesos_tipo = [];
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $id_tipo = $oProcesoTipo->getId_tipo_proceso();
            $nom_proceso = $oProcesoTipo->getNom_proceso();
            $a_procesos_tipo[$id_tipo] = $nom_proceso;
        }

        $a_cabeceras = [];
        $a_cabeceras[] = _("id_tipo_activ");
        $a_cabeceras[] = _("tipo actividad");
        $a_cabeceras[] = _("proceso");
        $a_cabeceras[] = _("proceso no dl");

        $a_valores = [];
        $i = 0;
        foreach ($cTiposDeActividades as $oTipo) {
            $i++;
            $id_tipo_activ = $oTipo->getId_tipo_activ();
            $id_tipo_proceso = $oTipo->getId_tipo_proceso();
            $id_tipo_proceso_ex = $oTipo->getId_tipo_proceso_ex();
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            $a_valores[$i][1] = $id_tipo_activ;
            $a_valores[$i][2] = $oTiposActividades->getNom();

            $propio = 't';
            $id_txt_dl = 'dl_' . $id_tipo_activ;
            if (empty($a_procesos_tipo[$id_tipo_proceso])) {
                $nom_proceso = '----';
            } else {
                $nom_proceso = $a_procesos_tipo[$id_tipo_proceso];
            }
            $txt_proceso_dl = "<span class=link id=$id_txt_dl onclick=fnjs_cambiar_proceso('$id_tipo_activ','$propio')> $nom_proceso</span>";

            $propio = 'f';
            $id_txt_nodl = 'nodl_' . $id_tipo_activ;
            if (empty($a_procesos_tipo[$id_tipo_proceso_ex])) {
                $nom_proceso = '----';
            } else {
                $nom_proceso = $a_procesos_tipo[$id_tipo_proceso_ex];
            }
            $txt_proceso_nodl = "<span class=link id=$id_txt_nodl onclick=fnjs_cambiar_proceso('$id_tipo_activ','$propio')> $nom_proceso</span>";

            $a_valores[$i][3] = $txt_proceso_dl;
            $a_valores[$i][4] = $txt_proceso_nodl;
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        break;
    case "lst_posibles_procesos":
        $Qid_tipo_activ = (integer)\filter_input(INPUT_POST, 'id_tipo_activ');
        $Qpropio = (string)\filter_input(INPUT_POST, 'propio');
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $aWhere = ['sfsv' => $mi_sfsv, '_ordre' => 'nom_proceso'];
        $GesProcesoTipo = new GestorProcesoTipo();
        $cProcesosTipo = $GesProcesoTipo->getProcesoTipos($aWhere);
        $txt_lista = '';
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $id_tipo_proceso = $oProcesoTipo->getId_tipo_proceso();
            $nom_proceso = $oProcesoTipo->getNom_proceso();

            $txt_lista .= "<tr><td class=link id=$id_tipo_proceso onclick=fnjs_asignar_proceso(event,'$Qid_tipo_activ','$Qpropio','$id_tipo_proceso')> $nom_proceso</td></tr>";
        }
        $txt = "<table><tr><td class=cabecera>" . _("procesos") . "</td></tr>$txt_lista</table>";
        echo $txt;
        break;
    case "asignar":
        $Qid_tipo_activ = (integer)\filter_input(INPUT_POST, 'id_tipo_activ');
        $Qpropio = (string)\filter_input(INPUT_POST, 'propio');
        $Qid_tipo_proceso = (integer)\filter_input(INPUT_POST, 'id_tipo_proceso');

        $oTipoActividad = new TipoDeActividad($Qid_tipo_activ);
        $oTipoActividad->DBCarregar();
        if (is_true($Qpropio)) {
            $oTipoActividad->setId_tipo_proceso($Qid_tipo_proceso);
        } else {
            $oTipoActividad->setId_tipo_proceso_ex($Qid_tipo_proceso);
        }
        if ($oTipoActividad->DBGuardar() === false) {
            echo _("hay un error, no se ha guardado el proceso");
        }
        break;
}
