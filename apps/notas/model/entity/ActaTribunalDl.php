<?php

namespace notas\model\entity;

/**
 * Fitxer amb la Classe que accedeix a la taula e_actas_tribunal_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_actas_tribunal_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class ActaTribunalDl extends ActaTribunal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_item
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id === 'id_schema') && $val_id !== '') {
                    $this->iid_schema = (int)$val_id;
                } 
                if (($nom_id === 'id_item') && $val_id !== '') {
                    $this->iid_item = (int)$val_id;
                } 
            }
        } else {
            if (isset($a_id) && $a_id !== '') {
                $this->iid_item = (integer)$a_id; 
                $this->aPrimary_key = array('id_item' => $this->iid_item);
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas_tribunal_dl');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

}