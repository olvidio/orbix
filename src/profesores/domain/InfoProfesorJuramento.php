<?php

namespace src\profesores\domain;


/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorJuramentoField;
use src\profesores\domain\contracts\ProfesorJuramentoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorJuramento extends DatosInfoRepo
{

    public function __construct(
        private ProfesorJuramentoRepositoryInterface $profesorJuramentoRepository,
    ) {
        $this->setTxtTitulo(_("dossier juramento del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorJuramento');
        $this->setMetodoGestor('getProfesorJuramentos');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorJuramentoRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1021;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorJuramentoField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorJuramentoField::FJuramento, OrderDirection::Desc);
        }

        return $this->profesorJuramentoRepository->matching($criteria);
    }
}