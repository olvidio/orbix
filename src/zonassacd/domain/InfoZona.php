<?php

namespace src\zonassacd\domain;

use src\shared\domain\DatosInfoRepo;
use src\zonassacd\domain\contracts\ZonaRepositoryInterface;
use src\zonassacd\domain\entity\Zona;

/* No vale el underscore en el nombre */

class InfoZona extends DatosInfoRepo
{
    public function __construct(
        private ZonaRepositoryInterface $zonaRepository,
    ) {
        $this->setTxtTitulo(_("zonas de misas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta zona?"));
        $this->setTxtBuscar(_("zona a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\zonassacd\\domain\\entity\\Zona');
        $this->setMetodoGestor('getZonas');

        $this->setRepositoryInterface(ZonaRepositoryInterface::class);
    }

    /**
     * @return list<Zona>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'orden'];
            $aOperador = [];
        } else {
            $aWhere = ['nom' => $this->k_buscar];
            $aOperador = ['nom' => 'sin_acentos'];
        }

        return $this->zonaRepository->getZonas($aWhere, $aOperador);
    }
}
