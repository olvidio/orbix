<?php

namespace personas\model;

use core;

/* No vale el underscore en el nombre */

class Info1004 extends core\DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de traslados"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar este traslado?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('personas\\model\\entity\\Traslado');
        $this->setMetodoGestor('getTraslados');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1004;
    }
}