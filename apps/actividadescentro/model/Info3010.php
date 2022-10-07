<?php

namespace actividadescentro\model;

use core;

/* No vale el underscore en el nombre */

class Info3010 extends core\DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("centros encargados de la actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este centro?"));
        $this->setTxtBuscar(_("buscar un centro"));
        $this->setTxtExplicacion();

        $this->setClase('actividadescentro\\model\\entity\\CentroEncargado');
        $this->setMetodoGestor('getCentrosEncargados');
        $this->setPau('a');
    }

    public function getId_dossier()
    {
        return 3010;
    }

}