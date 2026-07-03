<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;

/**
 * Caso de uso: opciones del desplegable de tareas dependientes de una fase.
 */
class ProcesosDepende
{
    public function __construct(
        private readonly ActividadTareaRepositoryInterface $actividadTareaRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{opciones: list<array{0: string, 1: string}>, blanco: bool}
     */
    public function execute(array $input): array
    {
        $Qacc = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'acc');
        $Qvalor_depende = \src\shared\domain\helpers\FuncTablasSupport::inputString($input, 'valor_depende');
        if ($Qacc !== '#id_tarea' && $Qacc !== '#id_tarea_previa') {
            return ['opciones' => [], 'blanco' => true];
        }

        $aOpciones = $this->actividadTareaRepository->getArrayActividadTareas(\src\shared\domain\helpers\FuncTablasSupport::inputInt($input, 'valor_depende'));

        return [
            'opciones' => OpcionesDesplegable::enOrden($aOpciones),
            'blanco' => true,
        ];
    }
}
