<?php

namespace src\procesos\domain;

use src\procesos\domain\contracts\ActividadTareaRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoTareas extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("Tipos de tareas que se pueden realizar en una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta tarea?"));
        $this->setTxtBuscar(_("tarea a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('src\\procesos\\domain\\entity\\ActividadTarea');
        $this->setMetodoGestor('getActividadTareas');


        $this->setRepositoryInterface(ActividadTareaRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'desc_tarea');
            $aOperador = [];
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->getRepositoryInterface());
        $Coleccion = $oLista->getActividadTareas($aWhere, $aOperador);

        return $Coleccion;
    }
}