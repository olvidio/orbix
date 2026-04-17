<?php

/*
 * DEPRECADO: dispatcher con parametro `que` heredado de
 * apps/procesos/controller/tipo_activ_proceso_ajax.php. Se mantiene como
 * wrapper temporal hasta refactorizar por accion (refactor.md "Endpoints por accion").
 */

use core\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use web\Lista;
use web\TiposActividades;
use function core\is_true;

header('Content-Type: text/plain; charset=UTF-8');

$Qque = (string)filter_input(INPUT_POST, 'que');

switch ($Qque) {
    case 'lista':
        $aWhere = ['_ordre' => 'id_tipo_activ'];
        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $cTiposDeActividades = $TipoDeActividadRepository->getTiposDeActividades($aWhere);

        $ProcesoTipoRepository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $cProcesosTipo = $ProcesoTipoRepository->getProcesoTipos();
        $a_procesos_tipo = [];
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $id_tipo = $oProcesoTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $a_procesos_tipo[$id_tipo] = $oProcesoTipo->getNom_proceso();
        }

        $a_cabeceras = [
            _("id_tipo_activ"),
            _("tipo actividad"),
            _("proceso"),
            _("proceso no dl"),
        ];

        $a_valores = [];
        $i = 0;
        foreach ($cTiposDeActividades as $oTipo) {
            $i++;
            $id_tipo_activ = $oTipo->getId_tipo_activ();
            $id_tipo_proceso = $oTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $id_tipo_proceso_ex = $oTipo->getId_tipo_proceso_ex(ConfigGlobal::mi_sfsv());
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            $a_valores[$i][1] = $id_tipo_activ;
            $a_valores[$i][2] = $oTiposActividades->getNom();

            $propio = 't';
            $id_txt_dl = 'dl_' . $id_tipo_activ;
            $nom_proceso = empty($a_procesos_tipo[$id_tipo_proceso]) ? '----' : $a_procesos_tipo[$id_tipo_proceso];
            $txt_proceso_dl = "<span class=link id=$id_txt_dl onclick=fnjs_cambiar_proceso('$id_tipo_activ','$propio')> $nom_proceso</span>";

            $propio = 'f';
            $id_txt_nodl = 'nodl_' . $id_tipo_activ;
            $nom_proceso = empty($a_procesos_tipo[$id_tipo_proceso_ex]) ? '----' : $a_procesos_tipo[$id_tipo_proceso_ex];
            $txt_proceso_nodl = "<span class=link id=$id_txt_nodl onclick=fnjs_cambiar_proceso('$id_tipo_activ','$propio')> $nom_proceso</span>";

            $a_valores[$i][3] = $txt_proceso_dl;
            $a_valores[$i][4] = $txt_proceso_nodl;
        }
        $oLista = new Lista();
        $oLista->setCabeceras($a_cabeceras);
        $oLista->setDatos($a_valores);
        echo $oLista->lista();
        break;

    case 'lst_posibles_procesos':
        $Qid_tipo_activ = (int)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qpropio = (string)filter_input(INPUT_POST, 'propio');
        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $aWhere = ['sfsv' => $mi_sfsv, '_ordre' => 'nom_proceso'];
        $ProcesoTipoRepository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $cProcesosTipo = $ProcesoTipoRepository->getProcesoTipos($aWhere);
        $txt_lista = '';
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $id_tipo_proceso = $oProcesoTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $nom_proceso = $oProcesoTipo->getNom_proceso();
            $txt_lista .= "<tr><td class=link id=$id_tipo_proceso onclick=fnjs_asignar_proceso(event,'$Qid_tipo_activ','$Qpropio','$id_tipo_proceso')> $nom_proceso</td></tr>";
        }
        echo "<table><tr><td class=cabecera>" . _("procesos") . "</td></tr>$txt_lista</table>";
        break;

    case 'asignar':
        $Qid_tipo_activ = (int)filter_input(INPUT_POST, 'id_tipo_activ');
        $Qpropio = (string)filter_input(INPUT_POST, 'propio');
        $Qid_tipo_proceso = (int)filter_input(INPUT_POST, 'id_tipo_proceso');

        $TipoDeActividadRepository = $GLOBALS['container']->get(TipoDeActividadRepositoryInterface::class);
        $oTipoDeActividad = $TipoDeActividadRepository->findById($Qid_tipo_activ);
        if (is_true($Qpropio)) {
            $oTipoDeActividad->setId_tipo_proceso($Qid_tipo_proceso, ConfigGlobal::mi_sfsv());
        } else {
            $oTipoDeActividad->setId_tipo_proceso_ex($Qid_tipo_proceso, ConfigGlobal::mi_sfsv());
        }
        if ($TipoDeActividadRepository->Guardar($oTipoDeActividad) === false) {
            echo _("hay un error, no se ha guardado el proceso");
        }
        break;
}
