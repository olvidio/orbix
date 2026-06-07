<?php

namespace src\inventario\application;

use src\inventario\domain\contracts\EquipajeRepositoryInterface;

/**
 * Borrado de un equipaje (antes solo en `equipajes_eliminar.php`).
 */
final class EquipajeEliminar
{
    public function __construct(
        private EquipajeRepositoryInterface $equipajeRepository,
    ) {
    }

    public function execute(int $id_equipaje): string
    {
        if ($id_equipaje <= 0) {
            return (string)_('falta id_equipaje');
        }

        $oEquipaje = $this->equipajeRepository->findById($id_equipaje);
        if ($oEquipaje === null) {
            return (string)sprintf(_('No se encuentra el equipaje %d'), $id_equipaje);
        }
        if ($this->equipajeRepository->Eliminar($oEquipaje) === false) {
            return (string)_('hay un error, no se ha eliminado') . "\n" . $this->equipajeRepository->getErrorTxt();
        }

        return '';
    }
}
