<?php
namespace ubis\model\entity;
/**
 * Classe que implementa l'entitat u_centros
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */

class CentroEllas Extends CentroDl {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
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
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_ubi = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_ubi' => $this->iid_ubi);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_centros_dl');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
}