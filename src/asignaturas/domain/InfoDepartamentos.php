<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\asignaturas\domain\entity\Departamento;
use src\asignaturas\domain\criteria\DepartamentoField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

class InfoDepartamentos extends DatosInfoRepo
{
    public function __construct(
        private DepartamentoRepositoryInterface $departamentoRepository,
    ) {
        $this->setTxtTitulo(_("departamentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este departamento?"));
        $this->setTxtBuscar(_("buscar un departamento"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Departamento');
        $this->setMetodoGestor('getDepartamentos');

        $this->setRepositoryInterface(DepartamentoRepositoryInterface::class);
    }

    /**
     * @return list<Departamento>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(DepartamentoField::NombreDepartamento);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(DepartamentoField::NombreDepartamento, (string) $this->k_buscar));
        }

        return $this->departamentoRepository->matching($criteria);
    }
}
