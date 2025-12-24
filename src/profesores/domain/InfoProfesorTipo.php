<?php

namespace src\profesores\domain;

/* No vale el underscore en el nombre */

use src\profesores\domain\contracts\ProfesorTipoRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoProfesorTipo extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de profesores"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de profesor?"));
        $this->setTxtBuscar(_("buscar un tipo de profesor"));
        $this->setTxtExplicacion();

        $this->setClase('src\\profesores\\domain\\entity\\ProfesorTipo');
        $this->setMetodoGestor('getProfesorTipos');

        $this->setRepositoryInterface(ProfesorTipoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'tipo_profesor');
            $aOperador = [];
        } else {
            $aWhere = array('tipo_profesor' => $this->k_buscar);
            $aOperador = array('tipo_profesor' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getProfesorTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}