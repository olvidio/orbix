<?php

namespace src\procesos\application;

use core\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use web\Lista;
use web\TiposActividades;

/**
 * Caso de uso: devuelve la tabla HTML con todos los tipos de actividad y
 * el proceso asignado (propio y no-propio) para cada uno.
 */
class TipoActivProcesoLista
{
    public function execute(array $input = []): string
    {
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

        return $oLista->lista();
    }
}
