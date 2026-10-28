<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorTituloEstField;
use src\profesores\domain\contracts\ProfesorTituloEstRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorTituloEst extends DatosInfoRepo
{

    public function __construct(
        private ProfesorTituloEstRepositoryInterface $profesorTituloEstRepository,
    ) {
        $this->setTxtTitulo(_("dossier de títulos de postgrado"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este título?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorTituloEst');
        $this->setMetodoGestor('getProfesorTitulosEst');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorTituloEstRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1017;
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        $criteria = Criteria::create();
        if (!empty($this->id_pau)) {
            $criteria = $criteria->andWhere(Filter::eq(ProfesorTituloEstField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorTituloEstField::Year, OrderDirection::Desc);
        } else {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(ProfesorTituloEstField::Titulo, $this->k_buscar));
        }

        return $this->profesorTituloEstRepository->matching($criteria);
    }
}
