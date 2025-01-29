<?php

namespace menus\model;

use core\DatosInfo;

/* No vale el underscore en el nombre */

class InfoGrupMenus extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("grupmenus"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este grupmenu?"));
        $this->setTxtBuscar(_("buscar un grupmenu"));
        $this->setTxtExplicacion();

        $this->setClase('menus\\model\\entity\\GrupMenu');
        $this->setMetodoGestor('getGrupMenus');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = array('_ordre' => 'grup_menu');
            $aOperador = '';
        } else {
            $aWhere = array('grup_menu' => $this->k_buscar);
            $aOperador = array('grup_menu' => 'sin_acentos');
        }
        $oLista = new entity\GestorGrupMenu();
        $Coleccion = $oLista->getGrupMenus($aWhere, $aOperador);

        return $Coleccion;
    }
}