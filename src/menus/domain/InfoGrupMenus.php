<?php

namespace src\menus\domain;

/* No vale el underscore en el nombre */

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\entity\GrupMenu;
use src\shared\domain\DatosInfoRepo;

class InfoGrupMenus extends DatosInfoRepo
{
    public function __construct(
        private GrupMenuRepositoryInterface $grupMenuRepository,
    ) {
        $this->setTxtTitulo(_("grupmenus"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este grupmenu?"));
        $this->setTxtBuscar(_("buscar un grupmenú por descripción"));
        $this->setTxtExplicacion();

        $this->setClase('src\\menus\\domain\\entity\\GrupMenu');
        $this->setMetodoGestor('getGrupmenus');

        $this->setRepositoryInterface(GrupMenuRepositoryInterface::class);
    }

    /**
     * @return list<GrupMenu>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'orden'];
            $aOperador = [];
        } else {
            $aWhere = ['descripcion' => $this->k_buscar];
            $aOperador = ['descripcion' => 'sin_acentos'];
        }

        return $this->grupMenuRepository->getGrupMenus($aWhere, $aOperador);
    }
}
