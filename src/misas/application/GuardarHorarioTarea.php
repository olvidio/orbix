<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoHorarioRepositoryInterface;

class GuardarHorarioTarea
{
    /**
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $id_item_h = (int)($input['id_item_h'] ?? 0);
        $t_start = (string)($input['t_start'] ?? '');
        $t_end = (string)($input['t_end'] ?? '');

        if ($id_item_h === 0) {
            return ['error' => _('Error: falta el id_item')];
        }

        $EncargoHorarioRepository = $GLOBALS['container']->get(EncargoHorarioRepositoryInterface::class);
        $oEncargoHorario = $EncargoHorarioRepository->findById($id_item_h);
        if ($oEncargoHorario === null) {
            return ['error' => sprintf(_('No se encuentra el horario %d'), $id_item_h)];
        }

        if ($t_start !== '') {
            $oEncargoHorario->setH_ini($t_start);
        }
        if ($t_end !== '') {
            $oEncargoHorario->setH_fin($t_end);
        }

        if ($EncargoHorarioRepository->Guardar($oEncargoHorario) === false) {
            return ['error' => $EncargoHorarioRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
