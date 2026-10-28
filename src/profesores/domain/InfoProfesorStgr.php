<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorStgrField;
use src\profesores\domain\contracts\ProfesorStgrRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorStgr extends DatosInfoRepo
{

    public function __construct(
        private ProfesorStgrRepositoryInterface $profesorStgrRepository,
    ) {
        $this->setTxtTitulo(_("dossier de nombramientos del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este nombramiento?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorStgr');
        $this->setMetodoGestor('getProfesores');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorStgrRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1018;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorStgrField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorStgrField::FNombramiento, OrderDirection::Desc);
        }

        return $this->profesorStgrRepository->matching($criteria);
    }
}