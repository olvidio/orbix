<?php

namespace src\inventario\domain;

use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\entity\Lugar;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoLugar extends DatosInfoRepo
{
    public function __construct(
        private LugarRepositoryInterface $lugarRepository,
    ) {
        $this->setTxtTitulo(_('centro o casa'));
        $this->setTxtEliminar(_('¿Está seguro que desea eliminar esta casa/centro?'));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('src\\inventario\\domain\\entity\\Lugar');
        $this->setMetodoGestor('getLugares');
        $this->setPau('p');

        $this->setRepositoryInterface(LugarRepositoryInterface::class);
    }

    /**
     * @return list<Lugar>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nom_lugar'];
            $aOperador = [];
        } else {
            $aWhere = ['nom_lugar' => $this->k_buscar];
            $aOperador = ['nom_lugar' => 'sin_acentos'];
        }

        return $this->lugarRepository->getLugares($aWhere, $aOperador);
    }
}
