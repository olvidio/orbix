<?php
namespace encargossacd\model\entity;
/**
 * Fitxer amb la Classe que accedeix a la taula propuesta_encargo_sacd_horario
 *
 * @package orbix
 * @subpackage encargosacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */
/**
 * Classe que implementa l'entitat propuesta_encargo_sacd_horario
 *
 * @package orbix
 * @subpackage encargosacd
 * @author Daniel Serrabou
 * @version 1.0
 * @created 29/04/2021
 */
class PropuestaEncargoSacdHorario Extends EncargoSacdHorario {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_item,iid_enc,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_enc') && $val_id !== '') $this->iid_enc = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('propuesta_encargo_sacd_horario');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	
}