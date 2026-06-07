<?php

namespace src\misas\application;

use src\misas\application\support\MisasBuildInput;
use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\misas\domain\entity\Plantilla;

class AnadirCtrTarea
{

    public function __construct(
        private readonly PlantillaRepositoryInterface $plantillaRepository,
    ) {
    }
    /**
     * @param array<string, mixed> $input
     * @return array{error: string}
     */
    public function execute(array $input): array
    {
        $que = MisasBuildInput::string($input, 'que');

        $error_txt = '';
        switch ($que) {
            case 'anadir':
                $id_ubi = MisasBuildInput::int($input, 'id_ubi');
                $id_tarea = MisasBuildInput::int($input, 'id_tarea');
                $id_item = (int)$this->plantillaRepository->getNewId();
                $oPlantilla = new Plantilla();
                $oPlantilla->setId_item($id_item);
                $oPlantilla->setTarea($id_tarea);
                $oPlantilla->setId_ctr($id_ubi);
                $oPlantilla->setSemana(-1);
                if ($this->plantillaRepository->Guardar($oPlantilla) === false) {
                    $error_txt = $this->plantillaRepository->getErrorTxt();
                }
                break;

            case 'quitar':
                $id_item = MisasBuildInput::int($input, 'id_item');
                if ($id_item === 0) {
                    $error_txt = _('Error: falta el id_item');
                    break;
                }
                $oPlantilla = $this->plantillaRepository->findById($id_item);
                if ($oPlantilla === null) {
                    $error_txt = sprintf(_('No se encuentra la plantilla %d'), $id_item);
                    break;
                }
                if ($this->plantillaRepository->Eliminar($oPlantilla) === false) {
                    $error_txt = $this->plantillaRepository->getErrorTxt();
                }
                break;

            default:
                $error_txt = sprintf(_('opción no definida en switch en %s, linea %s'), __FILE__, __LINE__);
                break;
        }

        return ['error' => $error_txt];
    }
}
