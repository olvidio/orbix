<?php
namespace ubis\model;
use permisos\model as permisos;
use core;

class CuadrosLabor extends permisos\Xpermisos {
  public static $classname = "CuadrosLabor";
  public $permissions=array(
				"sr"			=>512,
				"n"				=>256,
				"agd"			=>128,
				"sg"			=>64,
				"club"			=>16,
				"bachilleres"	=>8,
				"univ"			=>4,
				"jóvenes"		=>2,
				"mayores"		=>1
		);


  public function __construct() {
	  $miSfsv=core\ConfigGlobal::mi_sfsv();

	  if ($miSfsv == 1) { $this->permissions['sss+'] = 32; }
	  if ($miSfsv == 2) { $this->permissions['nax'] = 32; }
	}

}
?>
