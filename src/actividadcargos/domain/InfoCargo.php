<?php

namespace src\actividadcargos\domain;

/* No vale el underscore en el nombre */

use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoCargo extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("cargos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este cargo?"));
        $this->setTxtBuscar(_("buscar un cargo"));
        $this->setTxtExplicacion();

        $this->setClase('src\\actividadcargos\\domain\\entity\\Cargo');
        $this->setMetodoGestor('getCargos');

        $this->setRepositoryInterface(CargoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'cargo');
            $aOperador = [];
        } else {
            $aWhere = array('cargo' => $this->k_buscar);
            $aOperador = array('cargo' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getCargos($aWhere, $aOperador);

        return $Coleccion;
    }
}