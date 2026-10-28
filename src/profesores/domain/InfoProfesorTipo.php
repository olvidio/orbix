<?php

namespace src\profesores\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorTipoField;
use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorTipo extends DatosInfoRepo
{

    public function __construct(
        private ProfesorTipoRepositoryInterface $profesorTipoRepository,
    ) {
        $this->setTxtTitulo(_("tipos de profesores"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de profesor?"));
        $this->setTxtBuscar(_("buscar un tipo de profesor"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorTipo');
        $this->setMetodoGestor('getProfesorTipos');

        $this->setRepositoryInterface(ProfesorTipoRepositoryInterface::class);
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorTipoField::TipoProfesor);
        } else {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(ProfesorTipoField::TipoProfesor, $this->k_buscar));
        }

        return $this->profesorTipoRepository->matching($criteria);
    }
}