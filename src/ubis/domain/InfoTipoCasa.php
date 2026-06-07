<?php

namespace src\ubis\domain;

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
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nombre_tipo_casa'];
            $aOperador = [];
        } else {
            $aWhere = ['nombre_tipo_casa' => $this->k_buscar];
            $aOperador = ['nombre_tipo_casa' => 'sin_acentos'];
        }

        return $this->tipoCasaRepository->getTiposCasa($aWhere, $aOperador);
    }
}
