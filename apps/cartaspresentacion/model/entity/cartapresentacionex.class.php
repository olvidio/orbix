<?php
namespace cartaspresentacion\model\entity;

/**
 * Fitxer amb la Classe que accedeix a la taula du_presentacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/3/2019
 */
/**
 * Classe que implementa l'entitat du_presentacion
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/3/2019
 */
class CartaPresentacionEx Extends CartaPresentacion {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBR'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_ubi = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_ubi' => $this->iid_ubi);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('du_presentacion_ex');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
}
