<?php

namespace src\ubis\domain;

/* No vale el underscore en el nombre */

use src\shared\domain\DatosInfoRepo;
use src\ubis\application\repositories\TipoCentroRepository;

class InfoTipoCtr extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de centro"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de centro?"));
        $this->setTxtBuscar(_("buscar un tipo de centro"));
        $this->setTxtExplicacion();

        $this->setClase('src\\ubis\\domain\\entity\\TipoCentro');
        $this->setMetodoGestor('getTiposCentro');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nombre_tipo_ctr');
            $aOperador = [];
        } else {
            $aWhere = array('nombre_tipo_ctr' => $this->k_buscar);
            $aOperador = array('nombre_tipo_ctr' => 'sin_acentos');
        }
        $oLista = new TipoCentroRepository();
        $Coleccion = $oLista->getTiposCentro($aWhere, $aOperador);

        return $Coleccion;
    }
}