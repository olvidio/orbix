<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\actividades\domain\entity\TiposActividades;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

/**
 * Caso de uso: listado de tipos de actividad con proceso propio / no-propio.
 */
class TipoActivProcesoLista
{
    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
    }

    /**
     * @return array{
     *     a_cabeceras: list<string>,
     *     a_tipos: list<array<string, mixed>>
     * }
     */
    public function execute(): array
    {
        $aWhere = ['_ordre' => 'id_tipo_activ'];
        $cTiposDeActividades = $this->tipoDeActividadRepository->getTiposDeActividades($aWhere);

        $cProcesosTipo = $this->procesoTipoRepository->getProcesoTipos();
        $a_procesos_tipo = [];
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $id_tipo = $oProcesoTipo->getId_tipo_proceso();
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
            $id_tipo_proceso = (int) ($oTipo->getId_tipo_proceso(ConfigGlobal::mi_sfsv()) ?? 0);
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
