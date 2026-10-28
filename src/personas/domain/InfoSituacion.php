<?php

namespace src\personas\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\criteria\Filter;
use src\shared\domain\criteria\Criteria;
use src\profesores\domain\criteria\SituacionField;
use src\personas\domain\contracts\SituacionRepositoryInterface;
use src\personas\domain\entity\Situacion;
use src\shared\domain\DatosInfoRepo;

class InfoSituacion extends DatosInfoRepo
{
    public function __construct(
        private SituacionRepositoryInterface $situacionRepository,
    ) {
        $this->setTxtTitulo(_("tipos de situación"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de situación?"));
        $this->setTxtBuscar(_("buscar un tipo de situación"));
        $this->setTxtExplicacion();

        $this->setClase('src\\personas\\domain\\entity\\Situacion');
        $this->setMetodoGestor('getSituaciones');

        $this->setRepositoryInterface(SituacionRepositoryInterface::class);
    }

    /**
     * @return list<Situacion>
     */
    public function getColeccion(): array
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(SituacionField::Situacion);
        } else {
            $criteria = $criteria->andWhere(Filter::matchesUnaccented(SituacionField::Situacion, $this->k_buscar));
        }

        return $this->situacionRepository->matching($criteria);
    }
}
