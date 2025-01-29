<?php
namespace ubis\model\entity;

/**
 * Fitxer amb la Classe que accedeix a la taula u_cross_ctr_dir
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */

/**
 * Clase que implementa la entidad u_cross_ctr_dir
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/02/2014
 */
class CtrxDireccion extends UbixDireccion
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     * Si només necessita un valor, se li pot passar un integer.
     * En general se li passa un array amb les claus primàries.
     *
     * @param integer|array iid_ubi,iid_direccion
     *                        $a_id. Un array con los nombres=>valores de las claves primarias.
     */
    function __construct($a_id = '')
    {
        $oDbl = $GLOBALS['oDBP'];
        if (is_array($a_id)) {
            $this->aPrimary_key = $a_id;
            foreach ($a_id as $nom_id => $val_id) {
                if (($nom_id == 'id_ubi') && $val_id !== '') $this->iid_ubi = (int)$val_id;
                if (($nom_id == 'id_direccion') && $val_id !== '') $this->iid_direccion = (int)$val_id;
            }
        }
        $this->setoDbl($oDbl);
        $this->setoDbl_Select($oDbl);
        $this->setNomTabla('u_cross_ctr_dir');
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/
    /* OTROS MÉTODOS  ----------------------------------------------------------*/
    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

}
