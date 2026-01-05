<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ProcesoTipoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoProcesoTipo extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("Tipos de procesos que puede tener una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de proceso?"));
        $this->setTxtBuscar(_("tipo de proceso a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ProcesoTipo');
        $this->setMetodoGestor('getProcesoTipos');


        $this->setRepositoryInterface(ProcesoTipoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nom_proceso');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->getRepositoryInterface());
        $Coleccion = $oLista->getProcesoTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}