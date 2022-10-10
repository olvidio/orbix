<?php

namespace actividadestudios\model\entity;
/**
 * Fitxer amb la Classe que accedeix a la taula d_asignaturas_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */

/**
 * Clase que implementa la entidad d_asignaturas_activ_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */
class ActividadAsignaturaDl extends ActividadAsignatura
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_activ,iid_asignatura
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDB'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; 
                if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; 
            }
        }
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_asignaturas_activ_dl');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>