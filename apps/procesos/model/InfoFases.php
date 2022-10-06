<?php

namespace procesos\model;

use core;

/* No vale el underscore en el nombre */

class InfoFases extends core\datosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("Fases que se pueden realizar en una actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta fase?"));
        $this->setTxtBuscar(_("fase a buscar"));
        $this->setTxtExplicacion();

        $this->setClase('procesos\\model\\entity\\ActividadFase');
        $this->setMetodoGestor('getActividadFases');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'desc_fase');
            $aOperador = '';
        } else {
            $aWhere = array('nom' => $this->k_buscar);
            $aOperador = array('nom' => 'sin_acentos');
        }
        $oLista = new entity\GestorActividadFase();
        $Coleccion = $oLista->getActividadFases($aWhere, $aOperador);

        return $Coleccion;
    }
}