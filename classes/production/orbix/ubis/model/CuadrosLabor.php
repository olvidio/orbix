<?php

namespace ubis\model;

use core\ConfigGlobal;
use permisos\model\XPermisos;

class CuadrosLabor extends XPermisos
{
    public $classname = "CuadrosLabor";

    public function generarArrayTraducido()
    {
        $tipos = [_("sr") => 512,
            _("n") => 256,
            _("agd") => 128,
            _("sg") => 64,
            _("club") => 16,
            _("bachilleres") => 8,
            _("univ") => 4,
            _("jóvenes") => 2,
            _("mayores") => 1,
        ];
        return $tipos;
    }

    public function __construct()
    {
        $miSfsv = ConfigGlobal::mi_sfsv();

       $this->permissions= $this->generarArrayTraducido();

        if ($miSfsv == 1) {
            $this->permissions[_("sss+")] = 32;
        }
        if ($miSfsv == 2) {
            $this->permissions[_("nax")] = 32;
        }
    }

    public function getTxtTiposLabor()
    {
        return array_flip($this->permissions);
    }

}