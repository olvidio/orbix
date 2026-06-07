<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

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
     * @return array{opciones: array<int|string, string>, blanco: bool}
     */
    public function execute(array $input): array
    {
        $Qacc = input_string($input, 'acc');
        $Qvalor_depende = input_string($input, 'valor_depende');
        if ($Qacc !== '#id_tarea' && $Qacc !== '#id_tarea_previa') {
            return ['opciones' => [], 'blanco' => true];
        }

        $aOpciones = $this->actividadTareaRepository->getArrayActividadTareas(input_int($input, 'valor_depende'));

        return [
            'opciones' => $aOpciones,
            'blanco' => true,
        ];
    }
}
