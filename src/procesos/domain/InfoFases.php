<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ActividadFaseRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoFases extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("Fases que se pueden realizar en una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta fase?"));
        $this->setTxtBuscar(_("fase a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ActividadFase');
        $this->setMetodoGestor('getActividadFases');

        $this->setRepositoryInterface(ActividadFaseRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'desc_fase');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->getRepositoryInterface());
        $Coleccion = $oLista->getActividadFases($aWhere, $aOperador);

        return $Coleccion;
    }
}