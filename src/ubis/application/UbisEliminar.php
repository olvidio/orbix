<?php

namespace src\ubis\application;

use src\ubis\application\services\UbiRepositoryResolver;

final class UbisEliminar
{
    public function __construct(
        private UbiRepositoryResolver $ubiRepositoryResolver,
    ) {
    }

    public function execute(string $objPau, int $idUbi): string
    {
        $repo = $this->ubiRepositoryResolver->getRepository($objPau);
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
