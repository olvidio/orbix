<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\misas\application\support\MisasBuildInput;

/**
 * Anula la hora de inicio y de fin de un horario de tarea (`EncargoHorario`, `id_item`).
 * Es el contrario de {@see GuardarHorarioTarea}: ambos trabajan sobre el mismo horario del modal
 * `horario_tarea`.
 */
class QuitarHorarioTarea
{
    public function __construct(
        private readonly EncargoHorarioRepositoryInterface $encargoHorarioRepository,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $id_item = MisasBuildInput::int($input, 'id_item');
        if ($id_item === 0) {
            return ['error' => _("Error: falta el id_item")];
        }
        $oEncargoHorario = $this->encargoHorarioRepository->findById($id_item);
        if ($oEncargoHorario === null) {
            return ['error' => sprintf(_("No se encuentra el horario %d"), $id_item)];
        }

        $oEncargoHorario->setH_ini(null);
        $oEncargoHorario->setH_fin(null);

        if ($this->encargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            return ['error' => $this->encargoHorarioRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
