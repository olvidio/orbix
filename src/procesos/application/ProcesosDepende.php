<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\shared\domain\helpers\OpcionesDesplegable;
use src\shared\domain\helpers\FuncTablasSupport;

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
        $Qacc = FuncTablasSupport::inputString($input, 'acc');
        $Qvalor_depende = FuncTablasSupport::inputString($input, 'valor_depende');
        if ($Qacc !== '#id_tarea' && $Qacc !== '#id_tarea_previa') {
            return ['opciones' => [], 'blanco' => true];
        }

        $aOpciones = $this->actividadTareaRepository->getArrayActividadTareas(FuncTablasSupport::inputInt($input, 'valor_depende'));

        return [
            'opciones' => OpcionesDesplegable::enOrden($aOpciones),
            'blanco' => true,
        ];
    }
}
