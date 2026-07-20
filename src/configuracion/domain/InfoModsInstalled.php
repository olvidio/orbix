<?php

namespace src\configuracion\domain;

/* No vale el underscore en el nombre */

use src\configuracion\domain\contracts\ModuloInstaladoRepositoryInterface;
use src\configuracion\domain\entity\ModuloInstalado;
use src\shared\domain\DatosInfoRepo;

class InfoModsInstalled extends DatosInfoRepo
{
    public function __construct(
        private ModuloInstaladoRepositoryInterface $moduloInstaladoRepository,
    ) {
        $this->setTxtTitulo(_("módulos instalados"));
        $this->setTxtEliminar(_("¿Está seguro que desea desinstalar este módulo?"));
        $this->setTxtBuscar(_("buscar un módulo"));
        $this->setTxtExplicacion(_("Debe salir y volver a entrar en la aplicación para que los cambios tengan efecto"));

        $this->setClase('src\\configuracion\\domain\\entity\\ModuloInstalado');
        $this->setMetodoGestor('getModulosInstalados');

        $this->setRepositoryInterface(ModuloInstaladoRepositoryInterface::class);
    }

    /**
     * @return list<ModuloInstalado>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'id_mod'];
            $aOperador = [];
        } else {
            $aWhere = ['id_mod' => $this->k_buscar];
            $aOperador = [];
        }

        return $this->moduloInstaladoRepository->getModuloInstalados($aWhere, $aOperador);
    }
}
