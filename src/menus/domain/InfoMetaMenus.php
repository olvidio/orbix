<?php

namespace src\menus\domain;

/* No vale el underscore en el nombre */

use src\menus\domain\contracts\MetaMenuRepositoryInterface;
use src\menus\domain\criteria\MetaMenuField;
use src\menus\domain\entity\MetaMenu;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
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
            $criteria = Criteria::create()->orderBy(MetaMenuField::Url);
        } else {
            $criteria = Criteria::create()->where(Filter::matchesUnaccented(MetaMenuField::Descripcion, $this->k_buscar));
        }

        return $this->metaMenuRepository->matching($criteria);
    }
}
