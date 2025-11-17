<?php

namespace src\profesores\domain;

/* No vale el underscore en el nombre */

use src\profesores\application\repositories\ProfesorTipoRepository;
use src\shared\domain\DatosInfoRepo;
use src\ubis\application\repositories\TipoCentroRepository;

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
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'tipo_profesor');
            $aOperador = [];
        } else {
            $aWhere = array('profesor' => $this->k_buscar);
            $aOperador = array('profesor' => 'sin_acentos');
        }
        $oLista = new ProfesorTipoRepository();
        $Coleccion = $oLista->getProfesorTipos($aWhere, $aOperador);

        return $Coleccion;
    }
}