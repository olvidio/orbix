<?php

namespace src\asignaturas\domain;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\criteria\AsignaturaField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoOpcionales extends DatosInfoRepo
{
    public function __construct(
        private AsignaturaRepositoryInterface $asignaturaRepository,
    ) {
        $this->setTxtTitulo(_("asignaturas opcionales"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta opcional?"));
        $this->setTxtBuscar(_("buscar una asignatura opcional"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Asignatura');
        $this->setMetodoGestor('getAsignaturas');

        $this->setRepositoryInterface(AsignaturaRepositoryInterface::class);
    }

    /**
     * @return list<Asignatura>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (!empty($this->k_buscar)) {
            $criteria = $criteria->where(Filter::matchesUnaccented(AsignaturaField::NombreAsignatura, (string) $this->k_buscar));
        }
        $criteria = $criteria
            ->andWhere(Filter::gt(AsignaturaField::IdAsignatura, 3000))
            ->orderBy(AsignaturaField::NombreCorto);

        return $this->asignaturaRepository->matching($criteria);
    }
}
