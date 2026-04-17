<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;

final class UbisEliminar
{
    use ProvidesRepositories;

    public function execute(string $objPau, int $idUbi): string
    {
        $repo = $this->getRepository($objPau);
        $oUbi = $repo->findById($idUbi);
        if ($oUbi === null) {
            return _("no se encuentra el ubi a borrar");
        }
        if ($repo->Eliminar($oUbi) === false) {
            return _("hay un error, no se ha eliminado");
        }
        return '';
    }
}
