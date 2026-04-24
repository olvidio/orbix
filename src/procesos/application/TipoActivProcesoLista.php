<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use web\TiposActividades;

/**
 * Caso de uso: devuelve el listado estructurado de tipos de actividad
 * con el proceso propio / no-propio asignado. El frontend renderiza la
 * tabla con `web\Lista`.
 */
class TipoActivProcesoLista
{
    /**
     * @return array{
     *     a_cabeceras:array<int,string>,
     *     a_tipos:array<int,array{id_tipo_activ:string,nom:string,id_tipo_proceso:int,nom_proceso_propio:string,id_tipo_proceso_ex:int,nom_proceso_no_propio:string}>
     * }
     */
    public static function execute(): array
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

        $a_tipos = [];
        foreach ($cTiposDeActividades as $oTipo) {
            $id_tipo_activ = $oTipo->getId_tipo_activ();
            $id_tipo_proceso = (int)$oTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv());
            $id_tipo_proceso_ex = (int)$oTipo->getId_tipo_proceso_ex(ConfigGlobal::mi_sfsv());
            $oTiposActividades = new TiposActividades($id_tipo_activ);
            $nom_proceso_propio = empty($a_procesos_tipo[$id_tipo_proceso]) ? '----' : $a_procesos_tipo[$id_tipo_proceso];
            $nom_proceso_no_propio = empty($a_procesos_tipo[$id_tipo_proceso_ex]) ? '----' : $a_procesos_tipo[$id_tipo_proceso_ex];
            $a_tipos[] = [
                'id_tipo_activ' => $id_tipo_activ,
                'nom' => $oTiposActividades->getNom(),
                'id_tipo_proceso' => $id_tipo_proceso,
                'nom_proceso_propio' => $nom_proceso_propio,
                'id_tipo_proceso_ex' => $id_tipo_proceso_ex,
                'nom_proceso_no_propio' => $nom_proceso_no_propio,
            ];
        }

        return [
            'a_cabeceras' => $a_cabeceras,
            'a_tipos' => $a_tipos,
        ];
    }
}
