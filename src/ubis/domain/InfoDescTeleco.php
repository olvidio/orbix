<?php

namespace src\ubis\domain;

use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\DescTelecoRepositoryInterface;
use src\ubis\domain\entity\DescTeleco;

/* No vale el underscore en el nombre */

class InfoDescTeleco extends DatosInfoRepo
{
    public function __construct(
        private DescTelecoRepositoryInterface $descTelecoRepository,
    ) {
        $this->setTxtTitulo(_("descripciones de teleco"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta descripción?"));
        $this->setTxtBuscar(_("buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\DescTeleco');
        $this->setMetodoGestor('getDescTeleco');

        $this->setRepositoryInterface(DescTelecoRepositoryInterface::class);
    }

    /**
     * @return list<DescTeleco>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'ubi,persona,orden'];
            $aOperador = [];
        } else {
            $aWhere = ['id_desc_teleco' => $this->k_buscar];
            $aOperador = ['id_desc_teleco' => 'sin_acentos'];
        }

        return $this->descTelecoRepository->getDescsTeleco($aWhere, $aOperador);
    }
}
