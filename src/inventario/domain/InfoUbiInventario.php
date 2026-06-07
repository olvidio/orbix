<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\inventario\domain\entity\UbiInventario;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoUbiInventario extends DatosInfoRepo
{
    public function __construct(
        private UbiInventarioRepositoryInterface $ubiInventarioRepository,
    ) {
        $this->setTxtTitulo(_('ubis'));
        $this->setTxtEliminar(_('¿Está seguro que desea eliminar esta casa/centro?'));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\UbiInventario');
        $this->setMetodoGestor('getUbisInventario');
        $this->setPau('p');

        $this->setRepositoryInterface(UbiInventarioRepositoryInterface::class);
    }

    /**
     * @return list<UbiInventario>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nom_ubi'];
            $aOperador = [];
        } else {
            $aWhere = ['nom_ubi' => $this->k_buscar];
            $aOperador = ['nom_ubi' => 'sin_acentos'];
        }

        return $this->ubiInventarioRepository->getUbisInventario($aWhere, $aOperador);
    }
}
