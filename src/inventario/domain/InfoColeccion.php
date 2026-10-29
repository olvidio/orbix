<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\criteria\ColeccionField as F;
use src\inventario\domain\entity\Coleccion;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoColeccion extends DatosInfoRepo
{
    public function __construct(
        private ColeccionRepositoryInterface $coleccionRepository,
    ) {
        $this->setTxtTitulo(_("colecciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta colección?"));
        $this->setTxtBuscar(_("buscar en 'nombre colección'"));
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Coleccion');
        $this->setMetodoGestor('getColecciones');
        $this->setPau('p');

        $this->setRepositoryInterface(ColeccionRepositoryInterface::class);
    }

    /**
     * @return list<Coleccion>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NomColeccion);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NomColeccion, (string) $this->k_buscar));
        }

        return $this->coleccionRepository->matching($criteria);
    }
}
