<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\AsignaturaTipoRepositoryInterface;
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

        $this->setRepositoryInterface(AsignaturaTipoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'tipo_asignatura');
            $aOperador = [];
        } else {
            $aWhere = array('tipo_asignatura' => $this->k_buscar);
            $aOperador = array('tipo_asignatura' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getAsignaturaTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}