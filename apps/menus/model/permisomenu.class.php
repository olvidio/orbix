<?php
namespace menus\model;
use usuarios\model as usuarios;
use permisos\model as permisos;
use core;

/**
 * Classe per saber els permisos d'un usuari sobre els menus.
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 4/12/2010
 */
class PermisoMenu extends permisos\Xpermisos {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * sPermLogin de PermisoMenu
	 *
	 * @var string llista de valors separats per comes amb els permisos.
	 */
	 private $sPermLogin;

	 public $permissions;
	 public $todos;

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 */
	function __construct() {
		$this->iaccion = $_SESSION['iPermMenus'];
		$this->omplir();
	}

  private function omplir() {
		$permissions["adl"]	= 1;
		$permissions["pr"]		= 1;
		$permissions["agd"]	= 1<<1; // 2
		$permissions["aop"]    = 1<<2; //4,
		$permissions["des"]    = 1<<3; //8,
		$permissions["est"]    = 1<<4; //16,
		$permissions["scdl"]   = 1<<5; //32,
		$permissions["sg"]     = 1<<6; //64,
		$permissions["sm"]     = 1<<7; //128,
		$permissions["soi"]    = 1<<8; //256,
		$permissions["sr"]     = 1<<9; //512,
		$permissions["vcsd"]   = 1<<10; //1024,
		$permissions["dtor"]   = 1<<11; //2048,
		$permissions["ocs"]    = 1<<12; //4096,
		$permissions["sddl"]   = 1<<13; //8192,
		$permissions["nax"]  	= 1<<14; //16384,
		$permissions["dir"] 	=  1<<15; //32768,
		$permissions["pendents"]=  1<<16; //65536,
		$permissions["actividades"] =  1<<17; //131072,
		$permissions["admin_sf"]   =  1<<18; //262144,
		$permissions["admin_sv"]   =  1<<25; // uno que se grande, para que sea el último

		$this->permissions = $permissions;
  }

	/* METODES PUBLICS ----------------------------------------------------------*/
	/**
	 * Retorna true o false si és visible o no.
	 *
	 * @param integer permís del menú.
	 * @return boolean
	 */
	function visible($perm_menu) {
		if ($this->have_perm_bit($perm_menu)) {
			return true;
		} else { 
			return false;
		}
	}
	/*
	function visible($menu_perm) {
		$perm_menu = mb_strtolower($perm_menu, 'UTF-8');
		if (strpos($perm_menu,'todos') !== false ) return true;
		if ($this->have_perm($menu_perm)) {
			return true;
		} else { 
			return false;
		}
	}
*/
	
	/**
	 * Retorna true o false si és visible o no.
	 *
	 * @param string permís (o llista csv) del menú.
	 * @return boolean
	 */
	function zvisible($perm_menu) {
		$perm_menu = mb_strtolower($perm_menu, 'UTF-8');
		$llistaperm = explode(",",$perm_menu);
		$aux_temp = false;

		if ($this->sPermLogin === 'admin') {
			return true;
		} else { 
			foreach ($llistaperm as $a => $b) {
				$pos = strpos ($b, "!");
				if ($pos !== false && $pos === 0) {
					$neg = substr ($b, 1);    
					if (strpos($this->sPermLogin,$neg) !== false) {
						return false; // no debe seguir mirando más.
					}
				}
				if ($b === 'todos' && $this->sPermLogin != 'auxiliar') {
					$aux_temp = true;
				} else {
					if (strpos($this->sPermLogin,$b) !== false) {
						$aux_temp = true;
					}
				}
			}
			return $aux_temp;
		}
	}
	/* METODES PRIVATS --------------------------------------------------------- */

}
?>
