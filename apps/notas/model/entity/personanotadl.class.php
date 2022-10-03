<?php
namespace notas\model\entity;

use core;

/**
 * Fitxer amb la Classe que accedeix a la taula e_notas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */

/**
 * Clase que implementa la entidad e_notas_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class PersonaNotaDl extends PersonaNota
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_nom,iid_nivel
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; // evitem SQL injection fent cast a integer
                if (($nom_id == 'id_nivel') && $val_id !== '') $this->iid_nivel = (int)$val_id; // evitem SQL injection fent cast a integer
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_notas_dl');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
}

?>
