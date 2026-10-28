<?php

namespace  src\profesores\domain;

use src\shared\domain\criteria\OrderDirection;
use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\ProfesorAmpliacionField;
use src\profesores\domain\contracts\ProfesorAmpliacionRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */
class InfoProfesorAmpliacion extends DatosInfoRepo
{

    public function __construct(
        private ProfesorAmpliacionRepositoryInterface $profesorAmpliacionRepository,
    ) {
        $this->setTxtTitulo(_("dossier de ampliación de docencia del studium generale"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta fila?"));
        $this->setTxtBuscar(_("todos"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorAmpliacion');
        $this->setMetodoGestor('getProfesorAmpliaciones');
        $this->setPau('p');

        $this->setRepositoryInterface(ProfesorAmpliacionRepositoryInterface::class);
    }

    public function getId_dossier(): int
    {
        return 1019;
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
            $criteria = $criteria->andWhere(Filter::eq(ProfesorAmpliacionField::IdNom, $this->id_pau));
        }
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(ProfesorAmpliacionField::FNombramiento, OrderDirection::Desc);
        }

        return $this->profesorAmpliacionRepository->matching($criteria);
    }
}