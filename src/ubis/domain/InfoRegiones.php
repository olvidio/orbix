<?php

namespace src\ubis\domain;

use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\RegionRepositoryInterface;
use src\ubis\domain\entity\Region;

/* No vale el underscore en el nombre */

class InfoRegiones extends DatosInfoRepo
{
    public function __construct(
        private RegionRepositoryInterface $regionRepository,
    ) {
        $this->setTxtTitulo(_("regiones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta región?"));
        $this->setTxtBuscar(_("buscar una región (sigla)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\Region');
        $this->setMetodoGestor('getRegiones');

        $this->setRepositoryInterface(RegionRepositoryInterface::class);
    }

    /**
     * @return list<Region>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'region'];
            $aOperador = [];
        } else {
            $aWhere = ['region' => $this->k_buscar];
            $aOperador = ['region' => 'sin_acentos'];
        }

        return $this->regionRepository->getRegiones($aWhere, $aOperador);
    }
}
