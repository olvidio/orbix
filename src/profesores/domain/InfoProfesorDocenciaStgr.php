<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorDocenciaStgrField;
use src\profesores\domain\contracts\ProfesorDocenciaStgrRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorDocenciaStgr extends DatosInfoRepo
{

    public function __construct(
        private ProfesorDocenciaStgrRepositoryInterface $profesorDocenciaStgrRepository,
    ) {
        $this->setTxtTitulo(_("dossier de actividad docente"));
        $this->setTxtEliminar();
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorDocenciaStgr');
        $this->setMetodoGestor('getProfesorDocenciasStgr');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorDocenciaStgrRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1025;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorDocenciaStgrField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorDocenciaStgrField::CursoInicio, OrderDirection::Desc);
        }

        return $this->profesorDocenciaStgrRepository->matching($criteria);
    }
}