<?php

namespace src\ubis\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\DatosInfoRepo;
use src\ubis\application\repositories\DescTelecoRepository;

class InfoDescTeleco extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("descripciones de teleco"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta descripción?"));
        $this->setTxtBuscar(_("buscar por tipo teleco"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\DescTeleco');
        $this->setMetodoGestor('getDescTeleco');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'tipo_teleco');
            $aOperador = [];
        } else {
            $aWhere = array('tipo_teleco' => $this->k_buscar);
            $aOperador = array('tipo_teleco' => 'sin_acentos');
        }
        $oLista = new DescTelecoRepository();
        $Coleccion = $oLista->getDescsTeleco($aWhere, $aOperador);

        return $Coleccion;
    }
}