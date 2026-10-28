<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\procesos\domain\criteria\ProcesoTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoProcesoTipo extends DatosInfoRepo
{
    public function __construct(
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
        $this->setTxtTitulo(_("Tipos de procesos que puede tener una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de proceso?"));
        $this->setTxtBuscar(_("tipo de proceso a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ProcesoTipo');
        $this->setMetodoGestor('getProcesoTipos');

        $this->setRepositoryInterface(ProcesoTipoRepositoryInterface::class);
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $criteria = Criteria::create()->orderBy(ProcesoTipoField::NomProceso);
        } else {
            $criteria = Criteria::create()->where(Filter::matchesUnaccented(ProcesoTipoField::NomProceso, $this->k_buscar));
        }

        return $this->procesoTipoRepository->matching($criteria);
    }
}
