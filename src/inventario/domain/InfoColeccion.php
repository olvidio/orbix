<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\ColeccionRepositoryInterface;
use src\inventario\domain\entity\Coleccion;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoColeccion extends DatosInfoRepo
{
    public function __construct(
        private ColeccionRepositoryInterface $coleccionRepository,
    ) {
        $this->setTxtTitulo(_('colecciones'));
        $this->setTxtEliminar(_('¿Está seguro que desea eliminar esta colección?'));
        $this->setTxtBuscar(_("buscar en 'nombre colección'"));
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Coleccion');
        $this->setMetodoGestor('getColecciones');
        $this->setPau('p');

        $this->setRepositoryInterface(ColeccionRepositoryInterface::class);
    }

    /**
     * @return list<Coleccion>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nom_coleccion'];
            $aOperador = [];
        } else {
            $aWhere = ['nom_coleccion' => $this->k_buscar];
            $aOperador = ['nom_coleccion' => 'sin_acentos'];
        }

        return $this->coleccionRepository->getColecciones($aWhere, $aOperador);
    }
}
