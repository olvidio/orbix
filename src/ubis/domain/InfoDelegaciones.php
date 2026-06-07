<?php

namespace src\ubis\domain;

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
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'region'];
            $aOperador = [];
        } else {
            $aWhere = ['dl' => $this->k_buscar];
            $aOperador = ['dl' => 'sin_acentos'];
        }

        return $this->delegacionRepository->getDelegaciones($aWhere, $aOperador);
    }
}
