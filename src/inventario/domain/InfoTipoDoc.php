<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\criteria\TipoDocField as F;
use src\inventario\domain\entity\TipoDoc;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTipoDoc extends DatosInfoRepo
{
    public function __construct(
        private TipoDocRepositoryInterface $tipoDocRepository,
    ) {
        $this->setTxtTitulo(_("tipo de documentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta tipo de documento?"));
        $this->setTxtBuscar(_("buscar en 'detalle'"));
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\TipoDoc');
        $this->setMetodoGestor('getTipoDocs');
        $this->setPau('p');

        $this->setRepositoryInterface(TipoDocRepositoryInterface::class);
    }

    /**
     * @return list<TipoDoc>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NomDoc);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NomDoc, (string) $this->k_buscar));
        }

        return $this->tipoDocRepository->matching($criteria);
    }
}
