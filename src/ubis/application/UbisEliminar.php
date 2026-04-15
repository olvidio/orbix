<?php

namespace src\ubis\application;

use src\shared\infrastructure\ProvidesRepositories;

final class UbisEliminar
{
    use ProvidesRepositories;

    public function execute(string $objPau, int $idUbi): string
    {
        $repo = $this->getRepository($objPau);
        if ($repo->Eliminar($idUbi) === false) {
            return _("hay un error, no se ha eliminado");
        }
        return '';
    }
}
