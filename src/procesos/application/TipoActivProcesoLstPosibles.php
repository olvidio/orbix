<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;

/**
 * Caso de uso: devuelve la lista de procesos posibles que el usuario puede
 * asignar a un id_tipo_activ concreto, como estructura. El frontend se
 * encarga de la mini-tabla HTML clickable.
 */
class TipoActivProcesoLstPosibles
{
    public function execute(array $input): array
    {
        $Qid_tipo_activ = (int)($input['id_tipo_activ'] ?? 0);
        $Qpropio = (string)($input['propio'] ?? '');

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $aWhere = ['sfsv' => $mi_sfsv, '_ordre' => 'nom_proceso'];

        $ProcesoTipoRepository = $GLOBALS['container']->get(ProcesoTipoRepositoryInterface::class);
        $cProcesosTipo = $ProcesoTipoRepository->getProcesoTipos($aWhere);

        $aProcesos = [];
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $aProcesos[] = [
                'id_tipo_proceso' => (int)$oProcesoTipo->getId_tipo_proceso($mi_sfsv),
                'nom_proceso' => $oProcesoTipo->getNom_proceso(),
            ];
        }

        return [
            'id_tipo_activ' => $Qid_tipo_activ,
            'propio' => $Qpropio,
            'a_procesos' => $aProcesos,
        ];
    }
}
