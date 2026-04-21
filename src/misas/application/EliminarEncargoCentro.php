<?php

namespace src\misas\application;

use src\misas\domain\contracts\EncargoCtrRepositoryInterface;
use src\misas\domain\value_objects\EncargoCtrId;

class EliminarEncargoCentro
{
    /**
     * Elimina un `EncargoCtr` por su uuid. Devuelve texto vacio si todo fue
     * bien, o el mensaje de error del repositorio en caso contrario.
     */
    public static function execute(string $id_item): string
    {
        if (empty($id_item)) {
            return _('Falta el identificador del encargo-centro a eliminar');
        }

        $EncargoCtrRepository = $GLOBALS['container']->get(EncargoCtrRepositoryInterface::class);

        $EncargoCtr = $EncargoCtrRepository->findById(new EncargoCtrId($id_item));
        if ($EncargoCtr === null) {
            return sprintf(_('No se encuentra el encargo-centro %s'), $id_item);
        }

        if ($EncargoCtrRepository->Eliminar($EncargoCtr) === false) {
            return $EncargoCtrRepository->getErrorTxt();
        }
        return '';
    }
}
