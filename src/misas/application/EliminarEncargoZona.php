<?php

namespace src\misas\application;

use src\encargossacd\domain\contracts\EncargoRepositoryInterface;

class EliminarEncargoZona
{

    public function __construct(
        private readonly EncargoRepositoryInterface $encargoRepository,
    ) {
    }
    /**
     * Elimina un `Encargo` por id. Devuelve texto vacio si todo fue bien,
     * o el mensaje de error del repositorio en caso contrario.
     */
    public function execute(int $id_enc): string
    {

        $oEncargo = $this->encargoRepository->findById($id_enc);
        if ($oEncargo === null) {
            return sprintf(_('No se encuentra el encargo %d'), $id_enc);
        }

        if ($this->encargoRepository->Eliminar($oEncargo) === false) {
            return $this->encargoRepository->getErrorTxt();
        }
        return '';
    }
}
