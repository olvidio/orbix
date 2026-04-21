<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

class EliminarEncargoZona
{
    /**
     * Elimina un `Encargo` por id. Devuelve texto vacio si todo fue bien,
     * o el mensaje de error del repositorio en caso contrario.
     */
    public static function execute(int $id_enc): string
    {
        $EncargoRepository = $GLOBALS['container']->get(EncargoRepositoryInterface::class);

        $oEncargo = $EncargoRepository->findById($id_enc);
        if ($oEncargo === null) {
            return sprintf(_('No se encuentra el encargo %d'), $id_enc);
        }

        if ($EncargoRepository->Eliminar($oEncargo) === false) {
            return $EncargoRepository->getErrorTxt();
        }
        return '';
    }
}
