<?php

namespace procesos\model;

use core;

/* No vale el underscore en el nombre */

class InfoTareas extends core\DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("Tipos de tareas que se pueden realizar en una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta tarea?"));
        $this->setTxtBuscar(_("tarea a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('procesos\\model\\entity\\ActividadTarea');
        $this->setMetodoGestor('getActividadTareas');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'desc_tarea');
            $aOperador = '';
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new entity\GestorActividadTarea();
        $Coleccion = $oLista->getActividadTareas($aWhere, $aOperador);

        return $Coleccion;
    }
}