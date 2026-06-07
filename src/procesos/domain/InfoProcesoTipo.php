<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoProcesoTipo extends DatosInfoRepo
{
    public function __construct(
        private readonly ProcesoTipoRepositoryInterface $procesoTipoRepository,
    ) {
        $this->setTxtTitulo(_("Tipos de procesos que puede tener una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de proceso?"));
        $this->setTxtBuscar(_("tipo de proceso a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ProcesoTipo');
        $this->setMetodoGestor('getProcesoTipos');

        $this->setRepositoryInterface(ProcesoTipoRepositoryInterface::class);
    }

    /**
     * @return list<object>
     */
    public function getColeccion(): array
    {
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'nom_proceso'];
            $aOperador = [];
        } else {
            $aWhere = ['nom' => $this->k_buscar];
            $aOperador = ['nom' => 'sin_acentos'];
        }

        return $this->procesoTipoRepository->getProcesoTipos($aWhere, $aOperador);
    }
}
