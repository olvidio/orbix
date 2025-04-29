<?php

namespace menus\model;

use core\DatosInfo;

/* No vale el underscore en el nombre */

class zInfoMetaMenus extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("metamenus"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este metamenu?"));
        $this->setTxtBuscar(_("buscar un metamenú por descripción"));
        $this->setTxtExplicacion();

        $this->setClase('menus\\model\\entity\\MetaMenu');
        $this->setMetodoGestor('getMetamenus');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'url');
            $aOperador = '';
        } else {
            $aWhere = array('descripcion' => $this->k_buscar);
            $aOperador = array('descripcion' => 'sin_acentos');
        }
        $oLista = new entity\GestorMetaMenu();
        $Coleccion = $oLista->getMetamenus($aWhere, $aOperador);

        return $Coleccion;
    }
}