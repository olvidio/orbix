<?php

namespace src\ubis\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\DatosInfoRepo;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;

class InfoTipoTeleco extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de teleco"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de teleco?"));
        $this->setTxtBuscar(_("buscar un tipo de teleco"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TipoTeleco');
        $this->setMetodoGestor('getTiposTeleco');

        $this->setRepositoryInterface(TipoTelecoRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nombre_teleco');
            $aOperador = [];
        } else {
            $aWhere = array('nombre_teleco' => $this->k_buscar);
            $aOperador = array('nombre_teleco' => 'sin_acentos');
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getTiposTeleco($aWhere, $aOperador);

        return $Coleccion;
    }
}