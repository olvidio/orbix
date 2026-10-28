<?php

namespace  src\actividades\domain;

/* No vale el underscore en el nombre */

use src\actividades\domain\contracts\RepeticionRepositoryInterface;
use src\actividades\domain\criteria\RepeticionField;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

class InfoTipoRepeticion extends DatosInfoRepo
{
    public function __construct(
        private RepeticionRepositoryInterface $repeticionRepository,
    ) {
        $this->setTxtTitulo(_("tipo de repetición de actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de repetición?"));
        $this->setTxtBuscar(_("buscar un tipo de repetición"));
        $this->setTxtExplicacion();

        $this->setClase('src\\actividades\\domain\\entity\\Repeticion');
        $this->setMetodoGestor('getRepeticiones');

        $this->setRepositoryInterface(RepeticionRepositoryInterface::class);
    }

    /**
     * @return list<\src\actividades\domain\entity\Repeticion>
     */
    public function getColeccion(): array
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        $criteria = Criteria::create();
        if (!empty($this->k_buscar)) {
            $criteria = $criteria->where(Filter::matchesUnaccented(RepeticionField::Repeticion, $this->k_buscar));
        }
        $criteria = $criteria
            ->orderBy(RepeticionField::Temporada)
            ->addOrderBy(RepeticionField::Repeticion);

        return $this->repeticionRepository->matching($criteria);
    }
}
