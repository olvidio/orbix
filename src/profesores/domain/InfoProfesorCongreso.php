<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorCongresoField;
use src\profesores\domain\contracts\ProfesorCongresoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorCongreso extends DatosInfoRepo
{

    public function __construct(
        private ProfesorCongresoRepositoryInterface $profesorCongresoRepository,
    ) {
        $this->setTxtTitulo(_("congresos a los que ha asistido una persona"));
        $this->setTxtEliminar();
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorCongreso');
        $this->setMetodoGestor('getProfesorCongresos');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorCongresoRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1024;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorCongresoField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorCongresoField::FIni, OrderDirection::Desc);
        } else {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(ProfesorCongresoField::Congreso, $this->k_buscar));
        }

        return $this->profesorCongresoRepository->matching($criteria);
    }
}