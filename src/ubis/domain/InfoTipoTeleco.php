<?php

namespace src\ubis\domain;

use src\ubis\domain\criteria\TipoTelecoField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\ubis\domain\entity\TipoTeleco;

/* No vale el underscore en el nombre */

class InfoTipoTeleco extends DatosInfoRepo
{
    public function __construct(
        private TipoTelecoRepositoryInterface $tipoTelecoRepository,
    ) {
        $this->setTxtTitulo(_("tipos de teleco"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de teleco?"));
        $this->setTxtBuscar(_("buscar un tipo de teleco"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TipoTeleco');
        $this->setMetodoGestor('getTiposTeleco');

        $this->setRepositoryInterface(TipoTelecoRepositoryInterface::class);
    }

    /**
     * @return list<TipoTeleco>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NombreTeleco);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NombreTeleco, (string) $this->k_buscar));
        }

        return $this->tipoTelecoRepository->matching($criteria);
    }
}
