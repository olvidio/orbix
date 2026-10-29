<?php

namespace src\ubis\domain;

use src\ubis\domain\criteria\DelegacionField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;
use src\ubis\domain\entity\Delegacion;

/* No vale el underscore en el nombre */

class InfoDelegaciones extends DatosInfoRepo
{
    public function __construct(
        private DelegacionRepositoryInterface $delegacionRepository,
    ) {
        $this->setTxtTitulo(_("delegaciones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta delegación?"));
        $this->setTxtBuscar(_("buscar una delegación (sigla)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\Delegacion');
        $this->setMetodoGestor('getDelegaciones');

        $this->setRepositoryInterface(DelegacionRepositoryInterface::class);
    }

    /**
     * @return list<Delegacion>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::Region);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::Dl, (string) $this->k_buscar));
        }

        return $this->delegacionRepository->matching($criteria);
    }
}
