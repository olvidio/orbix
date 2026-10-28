<?php

namespace src\menus\domain;

/* No vale el underscore en el nombre */

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\menus\domain\criteria\GrupMenuField;
use src\menus\domain\entity\GrupMenu;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
            $criteria = Criteria::create()->orderBy(GrupMenuField::Orden);
        } else {
            // La tabla no tiene columna `descripcion` (la consulta legacy fallaba): se busca por el nombre del grupo.
            $criteria = Criteria::create()->where(Filter::matchesUnaccented(GrupMenuField::GrupMenu, $this->k_buscar));
        }

        return $this->grupMenuRepository->matching($criteria);
    }
}
