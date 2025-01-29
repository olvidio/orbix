<?php

namespace personas\model;

use core\DatosInfo;

/* No vale el underscore en el nombre */

class Info1006 extends DatosInfo
{

    public function __construct()
    {
        $this->setTxtTitulo(_("dossier de última aistencia a tipo de actividad"));
        $this->setTxtEliminar(_("¿Está seguro que desea eliminar esta actividad?"));
        $this->setTxtBuscar();
        $this->setTxtExplicacion();

        $this->setClase('personas\\model\\entity\\UltimaAsistencia');
        $this->setMetodoGestor('getUltimasAsistencias');
        $this->setPau('p');
    }

    public function getId_dossier()
    {
        return 1006;
    }
}