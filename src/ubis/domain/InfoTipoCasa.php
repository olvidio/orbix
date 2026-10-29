<?php

namespace src\ubis\domain;

use src\ubis\domain\criteria\TipoCasaField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\TipoCasaRepositoryInterface;
use src\ubis\domain\entity\TipoCasa;

/* No vale el underscore en el nombre */

class InfoTipoCasa extends DatosInfoRepo
{
    public function __construct(
        private TipoCasaRepositoryInterface $tipoCasaRepository,
    ) {
        $this->setTxtTitulo(_("tipos de casa"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de casa?"));
        $this->setTxtBuscar(_("buscar un tipo de casa"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TipoCasa');
        $this->setMetodoGestor('getTiposCasa');

        $this->setRepositoryInterface(TipoCasaRepositoryInterface::class);
    }

    /**
     * @return list<TipoCasa>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NombreTipoCasa);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NombreTipoCasa, (string) $this->k_buscar));
        }

        return $this->tipoCasaRepository->matching($criteria);
    }
}
