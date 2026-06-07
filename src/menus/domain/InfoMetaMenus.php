<?php

namespace src\menus\domain;

/* No vale el underscore en el nombre */

use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\entity\MetaMenu;
use src\shared\domain\DatosInfoRepo;

class InfoMetaMenus extends DatosInfoRepo
{
    public function __construct(
        private MetaMenuRepositoryInterface $metaMenuRepository,
    ) {
        $this->setTxtTitulo(_("metamenus"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este metamenu?"));
        $this->setTxtBuscar(_("buscar un metamenú por descripción"));
        $this->setTxtExplicacion();

        $this->setClase('src\\menus\\domain\\entity\\MetaMenu');
        $this->setMetodoGestor('getMetamenus');

        $this->setRepositoryInterface(MetaMenuRepositoryInterface::class);
    }

    /**
     * @return list<MetaMenu>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'url'];
            $aOperador = [];
        } else {
            $aWhere = ['descripcion' => $this->k_buscar];
            $aOperador = ['descripcion' => 'sin_acentos'];
        }

        return $this->metaMenuRepository->getMetamenus($aWhere, $aOperador);
    }
}
