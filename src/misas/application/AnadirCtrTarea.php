<?php

namespace src\misas\application;

use src\misas\domain\contracts\PlantillaRepositoryInterface;
use src\misas\domain\entity\Plantilla;

class AnadirCtrTarea
{
    /**
     * @return array{error: string}
     */
    public static function execute(array $input): array
    {
        $que = (string)($input['que'] ?? '');
        $PlantillaRepository = $GLOBALS['container']->get(PlantillaRepositoryInterface::class);

        $error_txt = '';
        switch ($que) {
            case 'anadir':
                $id_ubi = (int)($input['id_ubi'] ?? 0);
                $id_tarea = (int)($input['id_tarea'] ?? 0);
                $id_item = (int)$PlantillaRepository->getNewId();
                $oPlantilla = new Plantilla();
                $oPlantilla->setId_item($id_item);
                $oPlantilla->setTarea($id_tarea);
                $oPlantilla->setId_ctr($id_ubi);
                $oPlantilla->setSemana(-1);
                if ($PlantillaRepository->Guardar($oPlantilla) === false) {
                    $error_txt = $PlantillaRepository->getErrorTxt();
                }
                break;

            case 'quitar':
                $id_item = (int)($input['id_item'] ?? 0);
                if ($id_item === 0) {
                    $error_txt = _('Error: falta el id_item');
                    break;
                }
                $oPlantilla = $PlantillaRepository->findById($id_item);
                if ($oPlantilla === null) {
                    $error_txt = sprintf(_('No se encuentra la plantilla %d'), $id_item);
                    break;
                }
                if ($PlantillaRepository->Eliminar($oPlantilla) === false) {
                    $error_txt = $PlantillaRepository->getErrorTxt();
                }
                break;

            default:
                $error_txt = sprintf(_('opción no definida en switch en %s, linea %s'), __FILE__, __LINE__);
                break;
        }

        return ['error' => $error_txt];
    }
}
