<?php

declare(strict_types=1);

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\misas\application\support\MisasBuildInput;
use src\shared\domain\value_objects\TimeLocal;

/**
 * Datos del horario de una tarea (modal `horario_tarea.phtml`).
 *
 * Simple lectura de `t_start`/`t_end` del `EncargoHorario` indicado por
 * `id_item_h`. Se saca de la vista frontend para cumplir la regla de
 * `refactor.md`: los controladores `frontend/` no pueden instanciar
 * repositorios de `src\` ni resolver dependencias del contenedor.
 */
class HorarioTareaData
{

    public function __construct(
        private readonly EncargoHorarioRepositoryInterface $encargoHorarioRepository,
    ) {
    }
    /**
     * @param array<string, mixed> $input
     * @return array{t_start: string, t_end: string}
     */
    public function getData(array $input): array
    {
        $id_item_h = MisasBuildInput::int($input, 'id_item_h');

        $repo = $this->encargoHorarioRepository;
        $oEncargoHorario = $repo->findById($id_item_h);

        $hIni = $oEncargoHorario?->getH_ini();
        $hFin = $oEncargoHorario?->getH_fin();

        return [
            't_start' => $hIni instanceof TimeLocal ? $hIni->format('H:i') : '',
            't_end' => $hFin instanceof TimeLocal ? $hFin->format('H:i') : '',
        ];
    }
}
