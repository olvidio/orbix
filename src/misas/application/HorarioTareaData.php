<?php

declare(strict_types=1);

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;

/**
 * Datos del horario de una tarea (modal `horario_tarea.phtml`).
 *
 * Simple lectura de `t_start`/`t_end` del `EncargoHorario` indicado por
 * `id_item_h`. Se saca de la vista frontend para cumplir la regla de
 * `refactor.md`: los controladores `frontend/` no pueden instanciar
 * repositorios de `src\` ni tocar `$GLOBALS['container']`.
 */
class HorarioTareaData
{
    /**
     * @param array<string, mixed> $input
     * @return array{t_start: string, t_end: string}
     */
    public static function getData(array $input): array
    {
        $id_item_h = (int)($input['id_item_h'] ?? 0);

        $repo = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $oEncargoHorario = $repo->findById($id_item_h);

        return [
            't_start' => $oEncargoHorario !== null ? (string)$oEncargoHorario->getH_ini() : '',
            't_end' => $oEncargoHorario !== null ? (string)$oEncargoHorario->getH_fin() : '',
        ];
    }
}
