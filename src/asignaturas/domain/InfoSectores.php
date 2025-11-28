<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\SectorRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoSectores extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("sectores"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este sector?"));
        $this->setTxtBuscar(_("buscar un sector"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Sector');
        $this->setMetodoGestor('getSectores');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'sector');
            $aOperador = [];
        } else {
            $aWhere = array('sector' => $this->k_buscar);
            $aOperador = array('sector' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get(SectorRepositoryInterface::class);
        $Coleccion = $oLista->getSectores($aWhere, $aOperador);

        return $Coleccion;
    }
}