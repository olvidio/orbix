<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;

/**
 * Caso de uso: devuelve las opciones disponibles para el desplegable de
 * tareas dependientes de la fase indicada (usado al cambiar de fase o
 * fase_previa en el formulario procesos_ver).
 *
 * Respuesta JSON con `opciones` (value => label). El frontend inyecta
 * los `<option>` en el `<select>` destino indicado por `acc`.
 */
class ProcesosDepende
{
    /**
     * @return array{opciones:array<string,string>,blanco:bool}
     */
    public function execute(array $input): array
    {
        $Qacc = (string)($input['acc'] ?? '');
        $Qvalor_depende = (string)($input['valor_depende'] ?? '');
        if ($Qacc !== '#id_tarea' && $Qacc !== '#id_tarea_previa') {
            return ['opciones' => [], 'blanco' => true];
        }

        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $aOpciones = $ActividadTareaRepository->getArrayActividadTareas((int)$Qvalor_depende);

        return [
            'opciones' => $aOpciones,
            'blanco' => true,
        ];
    }
}
