<?php

namespace src\ubis\domain;

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
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nombre_tipo_ctr'];
            $aOperador = [];
        } else {
            $aWhere = ['nombre_tipo_ctr' => $this->k_buscar];
            $aOperador = ['nombre_tipo_ctr' => 'sin_acentos'];
        }

        return $this->tipoCentroRepository->getTiposCentro($aWhere, $aOperador);
    }
}
