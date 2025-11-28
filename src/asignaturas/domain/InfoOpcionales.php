<?php

namespace src\asignaturas\domain;

use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

/* No vale el underscore en el nombre */

class InfoOpcionales extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("asignaturas opcionales"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta opcional?"));
        $this->setTxtBuscar(_("buscar una asignatura opcional"));
        $this->setTxtExplicacion();

        $this->setClase('src\\asignaturas\\domain\\entity\\Asignatura');
        $this->setMetodoGestor('getAsignaturas');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['nombre_asignatura'] = $this->k_buscar;
            $aOperador['nombre_asignatura'] = 'sin_acentos';
        }
        $aWhere['id_asignatura'] = 3000;
        $aOperador['id_asignatura'] = '>';
        $aWhere['_ordre'] = 'nombre_corto';
        $oLista = $GLOBALS['container']->get(AsignaturaRepositoryInterface::class);
        $Coleccion = $oLista->getAsignaturas($aWhere, $aOperador);

        return $Coleccion;
    }
}