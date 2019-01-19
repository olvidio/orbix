<?php
namespace procesos\model;
use permisos\model as permisos;
class PermAfectados extends permisos\Xpermisos {
  public static $classname = "CuadrosPermActiv";
  public $permissions=array(
				"datos"					=>1,
				"dossiers económicos"	=>2,
				"atención sacd"			=>4,
				"ctr encargados"		=>8,
				"tarifas"				=>16,
				"cargos"				=>32,
				"asistentes"			=>64
		);
}