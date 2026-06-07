<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: opciones del desplegable fase_ref[] en usuario_perm_activ.
 */
class UsuarioPermActivFases
{
    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{opciones: array<int|string, string>}
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = input_string($input, 'id_tipo_activ');
        $Qdl_propia = input_string($input, 'dl_propia');

        $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, is_true($Qdl_propia));
        $aOpciones = $this->actividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);

        return ['opciones' => $aOpciones];
    }
}
