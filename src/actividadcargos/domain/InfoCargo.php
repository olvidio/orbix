<?php

namespace src\actividadcargos\domain;

/* No vale el underscore en el nombre */

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\actividadcargos\domain\entity\Cargo;
use src\shared\domain\DatosInfoRepo;

class InfoCargo extends DatosInfoRepo
{
    public function __construct(
        private CargoRepositoryInterface $cargoRepository,
    ) {
        $this->setTxtTitulo(_("cargos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este cargo?"));
        $this->setTxtBuscar(_("buscar un cargo"));
        $this->setTxtExplicacion();

        $this->setClase('src\\actividadcargos\\domain\\entity\\Cargo');
        $this->setMetodoGestor('getCargos');

        $this->setRepositoryInterface(CargoRepositoryInterface::class);
    }

    /**
     * @return list<Cargo>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'cargo'];
            $aOperador = [];
        } else {
            $aWhere = ['cargo' => $this->k_buscar];
            $aOperador = ['cargo' => 'sin_acentos'];
        }

        return $this->cargoRepository->getCargos($aWhere, $aOperador);
    }
}
