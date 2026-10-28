<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\procesos\domain\criteria\ActividadFaseField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoFases extends DatosInfoRepo
{
    public function __construct(
        private readonly ActividadFaseRepositoryInterface $actividadFaseRepository,
    ) {
        $this->setTxtTitulo(_("Fases que se pueden realizar en una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta fase?"));
        $this->setTxtBuscar(_("fase a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ActividadFase');
        $this->setMetodoGestor('getActividadFases');

        $this->setRepositoryInterface(ActividadFaseRepositoryInterface::class);
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $criteria = Criteria::create()->orderBy(ActividadFaseField::DescFase);
        } else {
            $criteria = Criteria::create()->where(Filter::matchesUnaccented(ActividadFaseField::DescFase, $this->k_buscar));
        }

        return $this->actividadFaseRepository->matching($criteria);
    }
}
