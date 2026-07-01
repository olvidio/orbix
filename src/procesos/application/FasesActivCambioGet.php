<?php

namespace src\procesos\application;

use src\actividades\domain\contracts\TipoDeActividadRepositoryInterface;
use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;
use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\is_true;

/**
 * Caso de uso: fases posibles para id_tipo_activ y dl_propia (desplegable JSON).
 */
class FasesActivCambioGet
{
    public function __construct(
        private readonly TipoDeActividadRepositoryInterface $tipoDeActividadRepository,
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{id: string, opciones: list<array{0: string, 1: string}>, selected: string, blanco: bool, action: string}
     */
    public function execute(array $input): array
    {
        $Qid_tipo_activ = input_string($input, 'id_tipo_activ');
        $Qdl_propia = input_string($input, 'dl_propia');
        $Qid_fase_sel = input_string($input, 'id_fase_sel');

        $aTiposDeProcesos = $this->tipoDeActividadRepository->getTiposDeProcesos($Qid_tipo_activ, is_true($Qdl_propia) ?? false);
        $aOpciones = $this->actividadFaseRepository->getArrayActividadFases($aTiposDeProcesos, true);

        return [
            'id' => 'id_fase_nueva',
            'opciones' => OpcionesDesplegable::enOrden($aOpciones),
            'selected' => $Qid_fase_sel,
            'blanco' => true,
            'action' => 'fnjs_lista()',
        ];
    }
}
