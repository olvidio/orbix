<?php
namespace procesos\model;
use permisos\model as permisos;
class PermAfectados extends permisos\Xpermisos {
  public static $classname = "CuadrosPermActiv";
  public $permissions = ['datos'	        =>1,
                      'economic'	    =>2,
                      'sacd'		    =>4,
                      'ctr'		    =>8,
                      'tarifa'	    =>16,
                      'cargos'	    =>32,
                      'asistentes'    =>64,
                      'asistentesSacd'=>128,
                  ];
}