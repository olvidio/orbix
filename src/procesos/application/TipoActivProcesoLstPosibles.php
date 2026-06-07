<?php

namespace src\procesos\application;

use src\shared\config\ConfigGlobal;
use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

/**
 * Caso de uso: procesos posibles asignables a un id_tipo_activ.
 */
class TipoActivProcesoLstPosibles
{
    public function __construct(
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = input_int($input, 'id_tipo_activ');
        $Qpropio = input_string($input, 'propio');

        $mi_sfsv = ConfigGlobal::mi_sfsv();
        $aWhere = ['sfsv' => $mi_sfsv, '_ordre' => 'nom_proceso'];

        $cProcesosTipo = $this->procesoTipoRepository->getProcesoTipos($aWhere);

        $aProcesos = [];
        foreach ($cProcesosTipo as $oProcesoTipo) {
            $aProcesos[] = [
                'id_tipo_proceso' => (int)$oProcesoTipo->getId_tipo_proceso(),
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
