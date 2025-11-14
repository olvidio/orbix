<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */


use src\asignaturas\application\repositories\AsignaturaTipoRepository;
use src\asignaturas\application\repositories\SectorRepository;
use src\shared\domain\DatosInfoRepo;

class InfoAsignaturaTipo extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de asignaturas"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de asignatura?"));
        $this->setTxtBuscar(_("buscar un tipo de asignatura"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\AsignaturaTipo');
        $this->setMetodoGestor('getAsignaturaTipos');
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
        $oLista = new AsignaturaTipoRepository();
        $Coleccion = $oLista->getAsignaturaTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}