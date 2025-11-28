<?php

namespace src\ubis\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\RegionRepositoryInterface;

class InfoRegiones extends DatosInfoRepo
{
    public function __construct()
    {
        $this->setTxtTitulo(_("regiones"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta región?"));
        $this->setTxtBuscar(_("buscar una región (sigla)"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\Region');
        $this->setMetodoGestor('getRegiones');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'region'];
            $aOperador = [];
        } else {
            $aWhere = ['region' => $this->k_buscar];
            $aOperador = ['region' => 'sin_acentos'];
        }
        $oLista = $GLOBALS['container']->get(RegionRepositoryInterface::class);
        $Coleccion = $oLista->getRegiones($aWhere, $aOperador);

        return $Coleccion;
    }
}
