<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;
use src\misas\application\support\MisasBuildInput;
use src\shared\domain\value_objects\TimeLocal;

class GuardarHorarioTarea
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
        $id_item_h = MisasBuildInput::int($input, 'id_item_h');
        $t_start = MisasBuildInput::string($input, 't_start');
        $t_end = MisasBuildInput::string($input, 't_end');

        if ($id_item_h === 0) {
            return ['error' => _('Error: falta el id_item')];
        }
        $oEncargoHorario = $this->encargoHorarioRepository->findById($id_item_h);
        if ($oEncargoHorario === null) {
            return ['error' => sprintf(_('No se encuentra el horario %d'), $id_item_h)];
        }

        if ($t_start !== '') {
            $oEncargoHorario->setH_ini(TimeLocal::fromString($t_start));
        }
        if ($t_end !== '') {
            $oEncargoHorario->setH_fin(TimeLocal::fromString($t_end));
        }

        if ($this->encargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            return ['error' => $this->encargoHorarioRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
