<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
use src\asignaturas\domain\entity\AsignaturaTipo;
use src\asignaturas\domain\criteria\AsignaturaTipoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

class InfoAsignaturaTipo extends DatosInfoRepo
{
    public function __construct(
        private AsignaturaTipoRepositoryInterface $asignaturaTipoRepository,
    ) {
        $this->setTxtTitulo(_("tipos de asignaturas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de asignatura?"));
        $this->setTxtBuscar(_("buscar un tipo de asignatura"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\AsignaturaTipo');
        $this->setMetodoGestor('getAsignaturaTipos');

        $this->setRepositoryInterface(AsignaturaTipoRepositoryInterface::class);
    }

    /**
     * @return list<AsignaturaTipo>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(AsignaturaTipoField::TipoAsignatura);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(AsignaturaTipoField::TipoAsignatura, (string) $this->k_buscar));
        }

        return $this->asignaturaTipoRepository->matching($criteria);
    }
}
