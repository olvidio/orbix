<?php
namespace ubis\model\entity;

/**
 * GestorTelecoUbi
 *
 * Classe per gestionar la llista d'objectes de la clase TelecoUbi
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class GestorTelecoCtrEx extends GestorTelecoCtr
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorTelecoUbi
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_teleco_ctr_ex');
    }
    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    /* METODES PROTECTED --------------------------------------------------------*/
    /* MÉTODOS GET y SET --------------------------------------------------------*/
}

?>
