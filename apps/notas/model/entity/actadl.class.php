<?php
namespace notas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula e_actas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat e_actas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class ActaDl Extends Acta {
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
				if (($nom_id == 'acta') && $val_id !== '') $this->sacta = (string)$val_id; // evitem SQL injection fent cast a string
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sacta = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('acta' => $this->sacta);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_actas_dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
	/* METODES GET i SET --------------------------------------------------------*/
}
?>
