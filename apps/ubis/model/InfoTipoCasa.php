<?php

namespace ubis\model;

use core\DatosInfo;

/* No vale el underscore en el nombre */

class InfoTipoCasa extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("tipos de casa"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este tipo de casa?"));
        $this->setTxtBuscar(_("buscar un tipo de casa"));
        $this->setTxtExplicacion();

        $this->setClase('ubis\\model\\entity\\TipoCasa');
        $this->setMetodoGestor('getTiposCasa');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'nombre_tipo_casa');
            $aOperador = '';
        } else {
            $aWhere = array('nombre_tipo_casa' => $this->k_buscar);
            $aOperador = array('nombre_tipo_casa' => 'sin_acentos');
        }
        $oLista = new entity\GestorTipoCasa();
        $Coleccion = $oLista->getTiposCasa($aWhere, $aOperador);

        return $Coleccion;
    }
}