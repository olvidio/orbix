<?php

namespace src\procesos\application;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use web\Desplegable;

/**
 * Caso de uso: devuelve el HTML de un desplegable de tareas
 * dependientes de la fase indicada (se usa al cambiar de fase o
 * fase_previa en el formulario).
 */
class ProcesosDepende
{
    public function execute(array $input): string
    {
        $Qacc = (string)($input['acc'] ?? '');
        $Qvalor_depende = (string)($input['valor_depende'] ?? '');
        if ($Qacc !== '#id_tarea' && $Qacc !== '#id_tarea_previa') {
            return '';
        }
        $ActividadTareaRepository = $GLOBALS['container']->get(ActividadTareaRepositoryInterface::class);
        $aOpciones = $ActividadTareaRepository->getArrayActividadTareas((int)$Qvalor_depende);
        $oDesplegable = new Desplegable();
        $oDesplegable->setOpciones($aOpciones);
        $oDesplegable->setBlanco(true);

        return $oDesplegable->options();
    }
}
