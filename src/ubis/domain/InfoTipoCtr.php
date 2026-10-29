<?php

namespace src\ubis\domain;

use src\ubis\domain\criteria\TipoCentroField as F;
use src\shared\domain\criteria\Criteria;
use src\shared\domain\criteria\Filter;
use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\TipoCentroRepositoryInterface;
use src\ubis\domain\entity\TipoCentro;

/* No vale el underscore en el nombre */

class InfoTipoCtr extends DatosInfoRepo
{
    public function __construct(
        private TipoCentroRepositoryInterface $tipoCentroRepository,
    ) {
        $this->setTxtTitulo(_("tipos de centro"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de centro?"));
        $this->setTxtBuscar(_("buscar un tipo de centro"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TipoCentro');
        $this->setMetodoGestor('getTiposCentro');

        $this->setRepositoryInterface(TipoCentroRepositoryInterface::class);
    }

    /**
     * @return list<TipoCentro>
     */
    public function getColeccion(): array
    {
        $criteria = Criteria::create();
        if (empty($this->k_buscar)) {
            $criteria = $criteria->orderBy(F::NombreTipoCtr);
        } else {
            $criteria = $criteria->where(Filter::matchesUnaccented(F::NombreTipoCtr, (string) $this->k_buscar));
        }

        return $this->tipoCentroRepository->matching($criteria);
    }
}
