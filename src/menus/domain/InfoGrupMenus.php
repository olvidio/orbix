<?php

namespace src\menus\domain;

/* No vale el underscore en el nombre */

use src\menus\domain\contracts\GrupMenuRepositoryInterface;
use src\shared\domain\DatosInfoRepo;

class InfoGrupMenus extends DatosInfoRepo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("grupmenus"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este grupmenu?"));
        $this->setTxtBuscar(_("buscar un grupmenú por descripción"));
        $this->setTxtExplicacion();

        $this->setClase('src\\menus\\domain\\entity\\GrupMenu');
        $this->setMetodoGestor('getGrupmenus');

        $this->setRepositoryInterface(GrupMenuRepositoryInterface::class);
    }

    public function getColeccion()
    {
        // para el datos_sql.php
        // Si se quiere listar una selección, $this->k_buscar
        if (empty($this->k_buscar)) {
            $aWhere = ['_ordre' => 'orden'];
            $aOperador = [];
        } else {
            $aWhere = ['descripcion' => $this->k_buscar];
            $aOperador = ['descripcion' => 'sin_acentos'];
        }
        $oLista = $GLOBALS['container']->get($this->repoInterface);
        $Coleccion = $oLista->getGrupMenus($aWhere, $aOperador);

        return $Coleccion;
    }
}