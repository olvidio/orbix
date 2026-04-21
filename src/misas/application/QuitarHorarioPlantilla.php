<?php

namespace src\misas\application;

use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\shared\domain\value_objects\NullDateTimeLocal;

class QuitarHorarioPlantilla
{
    /**
     * Anula `t_start` / `t_end` de una fila `misa_plantillas_dl` (`id_item`).
     *
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $id_item = (int)($input['id_item'] ?? 0);
        if ($id_item === 0) {
            return ['error' => _('Error: falta el id_item')];
        }

        $PlantillaRepository = $GLOBALS['container']->get(PlantillaRepositoryInterface::class);
        $oPlantilla = $PlantillaRepository->findById($id_item);
        if ($oPlantilla === null) {
            return ['error' => sprintf(_('No se encuentra la plantilla %d'), $id_item)];
        }

        $oNull = new NullDateTimeLocal();
        $oPlantilla->setT_start($oNull);
        $oPlantilla->setT_end($oNull);

        if ($PlantillaRepository->Guardar($oPlantilla) === false) {
            return ['error' => $PlantillaRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
