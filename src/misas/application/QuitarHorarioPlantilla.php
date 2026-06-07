<?php

namespace src\misas\application;

use src\misas\application\support\MisasBuildInput;
use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\shared\domain\value_objects\NullDateTimeLocal;

class QuitarHorarioPlantilla
{
    public function __construct(
        private readonly PlantillaRepositoryInterface $plantillaRepository,
    ) {
    }

    /**
     * Anula `t_start` / `t_end` de una fila `misa_plantillas_dl` (`id_item`).
     *
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $id_item = MisasBuildInput::int($input, 'id_item');
        if ($id_item === 0) {
            return ['error' => _('Error: falta el id_item')];
        }
        $oPlantilla = $this->plantillaRepository->findById($id_item);
        if ($oPlantilla === null) {
            return ['error' => sprintf(_('No se encuentra la plantilla %d'), $id_item)];
        }

        $oNull = new NullDateTimeLocal();
        $oPlantilla->setT_start($oNull);
        $oPlantilla->setT_end($oNull);

        if ($this->plantillaRepository->Guardar($oPlantilla) === false) {
            return ['error' => $this->plantillaRepository->getErrorTxt()];
        }

        return ['error' => ''];
    }
}
