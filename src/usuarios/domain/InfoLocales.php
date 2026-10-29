<?php

namespace src\usuarios\domain;

use src\shared\domain\DatosInfoRepo;
use src\usuarios\domain\criteria\LocalField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\OrderDirection;
use src\usuarios\domain\contracts\LocalRepositoryInterface;
use src\usuarios\domain\entity\Local;

/* No vale el underscore en el nombre */

class InfoLocales extends DatosInfoRepo
{
    public function __construct(
        private LocalRepositoryInterface $localRepository,
    ) {
        $this->setTxtTitulo(_("idiomas posibles para la aplicación"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("idioma a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\usuarios\\domain\\entity\\Local');
        $this->setMetodoGestor('getLocales');

        $this->setRepositoryInterface(LocalRepositoryInterface::class);
    }

    /**
     * @return list<Local>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (!empty($this->k_buscar)) {
            $criteria = $criteria->where(Filter::matchesUnaccented(LocalField::NomIdioma, $this->k_buscar));
        }

        return $this->localRepository->matching(
            $criteria
                ->orderBy(LocalField::Active, OrderDirection::Desc)
                ->addOrderBy(LocalField::NomIdioma)
        );
    }
}
