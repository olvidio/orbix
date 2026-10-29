<?php

namespace src\encargossacd\domain;

use src\encargossacd\domain\contracts\EncargoTipoRepositoryInterface;
use src\encargossacd\domain\criteria\EncargoTipoField;
use src\encargossacd\domain\entity\EncargoTipo;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoEncargoTipo extends DatosInfoRepo
{

    public function __construct(
        private EncargoTipoRepositoryInterface $encargoTipoRepository,
    ) {
        $this->setTxtTitulo(_("tipos de encargos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de encargo?"));
        $this->setTxtBuscar(_("buscar un tipo de encargo"));
        $this->setTxtExplicacion(_("solo los tipos 4000 y 7000 aparecen en el planning"));

        $this->setClase('src\\encargossacd\\domain\\entity\\EncargoTipo');
        $this->setMetodoGestor('getEncargoTipos');

        $this->setRepositoryInterface(EncargoTipoRepositoryInterface::class);
    }

    /**
     * @return list<EncargoTipo>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $criteria = $criteria->where(Filter::matchesUnaccented(EncargoTipoField::TipoEnc, $this->k_buscar));
        }
        $criteria = $criteria->orderBy(EncargoTipoField::IdTipoEnc);

        $Coleccion = $this->encargoTipoRepository->matching($criteria);

        return $Coleccion;
    }
}