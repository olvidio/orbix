<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\asignaturas\domain\entity\Sector;
use src\shared\domain\DatosInfoRepo;

class InfoSectores extends DatosInfoRepo
{
    public function __construct(
        private SectorRepositoryInterface $sectorRepository,
    ) {
        $this->setTxtTitulo(_("sectores"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este sector?"));
        $this->setTxtBuscar(_("buscar un sector"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Sector');
        $this->setMetodoGestor('getSectores');

        $this->setRepositoryInterface(SectorRepositoryInterface::class);
    }

    /**
     * @return list<Sector>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'sector'];
            $aOperador = [];
        } else {
            $aWhere = ['sector' => $this->k_buscar];
            $aOperador = ['sector' => 'sin_acentos'];
        }

        return $this->sectorRepository->getSectores($aWhere, $aOperador);
    }
}
