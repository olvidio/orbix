<?php

namespace src\profesores\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorPublicacionField;
use src\profesores\domain\contracts\ProfesorPublicacionRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorPublicacion extends DatosInfoRepo
{

    public function __construct(
        private ProfesorPublicacionRepositoryInterface $profesorPublicacionRepository,
    ) {
        $this->setTxtTitulo(_("dossier de publicaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta publicación?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorPublicacion');
        $this->setMetodoGestor('getProfesorPublicaciones');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorPublicacionRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1012;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorPublicacionField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorPublicacionField::FPublicacion, OrderDirection::Desc);
        } else {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(ProfesorPublicacionField::Titulo, $this->k_buscar));
        }

        return $this->profesorPublicacionRepository->matching($criteria);
    }
}