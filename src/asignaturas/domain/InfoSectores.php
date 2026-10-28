<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\entity\Sector;
use src\asignaturas\domain\criteria\SectorField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

class InfoSectores extends DatosInfoRepo
{
    public function __construct(
        private SectorRepositoryInterface $sectorRepository,
    ) {
        $this->setTxtTitulo(_("sectores"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este sector?"));
        $this->setTxtBuscar(_("buscar un sector"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Sector');
        $this->setMetodoGestor('getSectores');

        $this->setRepositoryInterface(SectorRepositoryInterface::class);
    }

    /**
     * @return list<Sector>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(SectorField::NombreSector);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(SectorField::NombreSector, (string) $this->k_buscar));
        }

        return $this->sectorRepository->matching($criteria);
    }
}
