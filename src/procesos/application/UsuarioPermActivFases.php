<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qid_tipo_activ = FuncTablasSupport::inputString($input, 'id_tipo_activ');
        $Qdl_propia = FuncTablasSupport::inputString($input, 'dl_propia');

        $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, FuncTablasSupport::isTrue($Qdl_propia) ?? false);
        $aOpciones = $this->actividadFaseRepository->getArrayActividadFases($aTiposDeProcesos);

        return ['opciones' => $aOpciones];
    }
}
