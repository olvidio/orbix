<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\criteria\LugarField as F;
use src\inventario\domain\entity\Lugar;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoLugar extends DatosInfoRepo
{
    public function __construct(
        private LugarRepositoryInterface $lugarRepository,
    ) {
        $this->setTxtTitulo(_("centro o casa"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta casa/centro?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Lugar');
        $this->setMetodoGestor('getLugares');
        $this->setPau('p');

        $this->setRepositoryInterface(LugarRepositoryInterface::class);
    }

    /**
     * @return list<Lugar>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NomLugar);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NomLugar, (string) $this->k_buscar));
        }

        return $this->lugarRepository->matching($criteria);
    }
}
