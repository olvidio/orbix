<?php
namespace actividadplazas\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula da_plazas_dl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 31/10/2016
 */
/**
 * Classe que implementa l'entitat ActividadPlazasDl
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 31/10/2016
 */
class ActividadPlazasDl Extends ActividadPlazas {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array sacta
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_dl') && $val_id !== '') $this->iid_dl = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'dl_tabla') && $val_id !== '') $this->sdl_tabla = (string)$val_id; // evitem SQL injection fent cast a string
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sacta = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iitem' => $this->iitem);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('da_plazas_dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}