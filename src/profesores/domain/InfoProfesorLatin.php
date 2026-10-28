<?php

namespace src\profesores\domain;

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorLatinField;
use src\profesores\domain\contracts\ProfesorLatinRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */
class InfoProfesorLatin extends DatosInfoRepo
{

    public function __construct(
        private ProfesorLatinRepositoryInterface $profesorLatinRepository,
    ) {
        $this->setTxtTitulo(_("dossier profesores de latín"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esto?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorLatin');
        $this->setMetodoGestor('getProfesoresLatin');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorLatinRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1022;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorLatinField::IdNom, $this->id_pau));
        }

        return $this->profesorLatinRepository->matching($criteria);
    }
}