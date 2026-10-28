<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorDirectorField;
use src\profesores\domain\contracts\ProfesorDirectorRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorDirector extends DatosInfoRepo
{

    public function __construct(
        private ProfesorDirectorRepositoryInterface $profesorDirectorRepository,
    ) {
        $this->setTxtTitulo(_("dossier de directores de departamento del studium generale"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorDirector');
        $this->setMetodoGestor('getProfesoresDirectores');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorDirectorRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1020;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorDirectorField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorDirectorField::FNombramiento, OrderDirection::Desc);
        }

        return $this->profesorDirectorRepository->matching($criteria);
    }
}