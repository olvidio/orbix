<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\shared\security\HashB;

/**
 * Caso de uso: datos para la pantalla `procesos_select`.
 */
class ProcesosSelectData
{
    public function __construct(
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
    }

    /**
     * @return array{
     *     a_tipos_proceso: array<int|string, string>,
     *     ctx_regenerar: array<int|string, string>,
     *     ctx_clonar: array<int|string, string>
     * }
     */
    public function execute(): array
    {
        $a_tipos_proceso = $this->procesoTipoRepository->getArrayProcesoTipos();
        $ctx_regenerar = [];
        $ctx_clonar = [];
        foreach ($a_tipos_proceso as $id => $_label) {
            $idInt = (int) $id;
            $ctx_regenerar[$id] = HashB::sign('procesos_regenerar', ['id_tipo_proceso' => $idInt]);
            $ctx_clonar[$id] = HashB::sign('procesos_clonar', ['id_tipo_proceso' => $idInt]);
        }

        return [
            'a_tipos_proceso' => $a_tipos_proceso,
            'ctx_regenerar' => $ctx_regenerar,
            'ctx_clonar' => $ctx_clonar,
        ];
    }
}
