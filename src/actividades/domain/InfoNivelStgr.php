<?php

namespace src\actividades\domain;


/* No vale el underscore en el nombre */

use src\actividades\application\repositories\NivelStgrRepository;
use src\shared\domain\DatosInfoRepo;

class InfoNivelStgr extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("nivel del stgr"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este nivel?"));
        $this->setTxtBuscar(_("buscar un nivel"));
        $this->setTxtExplicacion();

        $this->setClase('src\\actividades\\domain\\entity\\NivelStgr');
        $this->setMetodoGestor('getNivelesStgr');
    }

    public function getColeccion()
    {
        $aWhere = [];
        $aOperador = [];
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (!empty($this->k_buscar)) {
            $aWhere['desc_nivel'] = $this->k_buscar;
            $aOperador['desc_nivel'] = 'sin_acentos';
        }
        $aWhere['_ordre'] = 'orden';
        $oLista = new NivelStgrRepository();
        $Coleccion = $oLista->getNivelesStgr($aWhere, $aOperador);

        return $Coleccion;
    }
}