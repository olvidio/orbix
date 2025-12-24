<?php

namespace src\asignaturas\domain;

/* No vale el underscore en el nombre */

use src\asignaturas\domain\contracts\DepartamentoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoDepartamentos extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("departamentos"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este departamento?"));
        $this->setTxtBuscar(_("buscar un departamento"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Departamento');
        $this->setMetodoGestor('getDepartamentos');

        $this->setRepositoryInterface(DepartamentoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'departamento');
            $aOperador = [];
        } else {
            $aWhere = array('departamento' => $this->k_buscar);
            $aOperador = array('departamento' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getDepartamentos($aWhere, $aOperador);

        return $Coleccion;
    }
}