<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\criteria\UbiInventarioField as F;
use src\inventario\domain\entity\UbiInventario;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoUbiInventario extends DatosInfoRepo
{
    public function __construct(
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
    ) {
        $this->setTxtTitulo(_("ubis"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta casa/centro?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\UbiInventario');
        $this->setMetodoGestor('getUbisInventario');
        $this->setPau('p');

        $this->setRepositoryInterface(UbiInventarioRepositoryInterface::class);
    }

    /**
     * @return list<UbiInventario>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NomUbi);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NomUbi, (string) $this->k_buscar));
        }

        return $this->ubiInventarioRepository->matching($criteria);
    }
}
