<?php
namespace cartaspresentacion\model\entity;
use core;
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
class CartaPresentacionDl Extends CartaPresentacion {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_direccion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
    function __construct($a_id='') {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach($a_id as $nom_id=>$val_id) {
                if (($nom_id == 'id_direccion') && $val_id !== '') $this->iid_direccion = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('du_presentacion_dl');
    }

	/* METODES PUBLICS ----------------------------------------------------------*/

	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/
}
