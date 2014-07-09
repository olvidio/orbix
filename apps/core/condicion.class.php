<?php
namespace core;
/**
 * Condicion
 *
 * Classe per a gestionar les condicions de cerca a la Base de Dades
 *
 * @package delegación
 * @subpackage model
 * @author 
 * @version 1.0
 * @created 22/9/2010
 */
class Condicion {
	/* ATRIBUTS ----------------------------------------------------------------- */


	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @return GestorActividad
	 *
	 */
	function __construct() {
		// constructor buit
	}

	/* METODES PUBLICS -----------------------------------------------------------*/
	public function getCondicion($campo,$operador,$valor) {
	   if (isset($operador) && $operador !='') {
			switch($operador) {
				case 'IS NOT NULL':
				case 'IS NULL':
					$sCondi = "$campo $operador";
					break;
				case 'BETWEEN':
					$val1 = strtok($valor,',');
					$val2 = strtok(',');
					$sCondi = "$campo >= $val1 AND $campo <= $val2";
					break;
				case '!~':
					$sCondi = "$campo::text !~ :$campo";
					break;
				case '~':
					$sCondi = "$campo::text ~ :$campo";
					break;
				case '~INV':
					$sCondi = ":$campo::text ~ $campo";
					break;
				case 'sin_acentos':
					$sCondi = "public.sin_acentos($campo::text)  ~* public.sin_acentos(:$campo::text)";
					break;
				case '&':
					$sCondi = "($campo & :$campo) = :$campo";
					break;
				default:
					$sCondi = "$campo $operador :$campo";
			}
		} else {
			$sCondi = "$campo = :$campo";
		}
		return $sCondi;
	}

}
?>
