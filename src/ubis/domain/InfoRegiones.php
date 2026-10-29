<?php

namespace src\ubis\domain;

use src\ubis\domain\criteria\RegionField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\entity\Region;

/* No vale el underscore en el nombre */

class InfoRegiones extends DatosInfoRepo
{
    public function __construct(
        private RegionRepositoryInterface $regionRepository,
    ) {
        $this->setTxtTitulo(_("regiones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta región?"));
        $this->setTxtBuscar(_("buscar una región (sigla)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\Region');
        $this->setMetodoGestor('getRegiones');

        $this->setRepositoryInterface(RegionRepositoryInterface::class);
    }

    /**
     * @return list<Region>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::Region);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::Region, (string) $this->k_buscar));
        }

        return $this->regionRepository->matching($criteria);
    }
}
