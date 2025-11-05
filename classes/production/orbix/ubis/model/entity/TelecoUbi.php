<?php
namespace ubis\model\entity;

/**
 * Clase que implementa la entidad d_teleco_ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class TelecoUbi
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe vuit.
     */
    function __construct()
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/


    public static function NewTelecoUbi($id_item)
    {
        $gesTelecoCtr = new GestorTelecoCtr;
        $cTelecoCtr = $gesTelecoCtr->getTelecos(array('id_item' => $id_item));
        if (count($cTelecoCtr) > 0) {
            $oTelecoUbi = $cTelecoCtr[0];
        } else {
            $oTelecoUbi = new TelecoCdc($id_item);
        }
        return $oTelecoUbi;
    }
}
