<?php

namespace src\menus\domain;


/* No vale el underscore en el nombre */

use src\menus\application\repositories\MetaMenuRepository;
use src\shared\DatosInfoRepo;

class InfoMetaMenus extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("metamenus"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este metamenu?"));
        $this->setTxtBuscar(_("buscar un metamenú por descripción"));
        $this->setTxtExplicacion();

        $this->setClase('src\\menus\\domain\\entity\\MetaMenu');
        $this->setMetodoGestor('getMetamenus');
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'url'];
            $aOperador = [];
        } else {
            $aWhere = ['descripcion' => $this->k_buscar];
            $aOperador = ['descripcion' => 'sin_acentos'];
        }
        $oLista = new MetaMenuRepository();
        $Coleccion = $oLista->getMetamenus($aWhere, $aOperador);

        return $Coleccion;
    }
}