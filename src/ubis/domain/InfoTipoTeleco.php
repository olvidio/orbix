<?php

namespace src\ubis\domain;

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
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nombre_teleco'];
            $aOperador = [];
        } else {
            $aWhere = ['nombre_teleco' => $this->k_buscar];
            $aOperador = ['nombre_teleco' => 'sin_acentos'];
        }

        return $this->tipoTelecoRepository->getTiposTeleco($aWhere, $aOperador);
    }
}
