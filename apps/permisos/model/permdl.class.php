<?php
namespace permisos\model;

class PermDl extends xPermisos {
  var $classname = "PermDl";
  
  function __construct(){
	  $this->omplir();
  }

  private function omplir() {
		$permission_users["adl"]	= 1;
		$permission_users["pr"]		= 1;
		$permission_users["agd"]	= 1<<1; // 2
		$permission_users["aop"]    = 1<<2; //4,
		$permission_users["des"]    = 1<<3; //8,
		$permission_users["est"]    = 1<<4; //16,
		$permission_users["scdl"]   = 1<<5; //32,
		$permission_users["sg"]     = 1<<6; //64,
		$permission_users["sm"]     = 1<<7; //128,
		$permission_users["soi"]    = 1<<8; //256,
		$permission_users["sr"]     = 1<<9; //512,
		$permission_users["vcsd"]   = 1<<10; //1024,
		$permission_users["dtor"]   = 1<<11; //2048,
		$permission_users["ocs"]    = 1<<12; //4096,
		$permission_users["sddl"]   = 1<<13; //8192,
		$permission_users["nax"]  	= 1<<14; //16384,
		$permission_users["actividades"] =  31735; //31735, // todos menos des(8) y vcsd(1024).
		$permission_users["dir"] 	=  1<<15; //32768,
		$permission_users["pendents"]=  1<<16; //65536,
		$permission_users["admin"]   =  -1; //131071; // todo unos, depende de la máquina, 32 o 64 bits.

		$this->permissions = $permission_users;
  }
}
?>
