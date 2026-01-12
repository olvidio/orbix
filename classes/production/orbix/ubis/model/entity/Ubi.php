<?php

namespace ubis\model\entity;

use core\ConfigGlobal;

/**
 * Clase que implementa la entidad ubis
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class Ubi
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


    public static function NewUbi($id_ubi)
    {
        $oUbi = null;
        if (ConfigGlobal::is_dmz()) {
            $gesCentro = new GestorCentroEllos();
            // para la sf (comienza por 2).
            if (substr($id_ubi, 0, 1) == 2) {
                $gesCentro = new GestorCentroEllas();
            }
            $cCentros = $gesCentro->getCentros(array('id_ubi' => $id_ubi));
            if (!empty($cCentros)) {
                $oUbi = $cCentros[0];
            }
        } else {
            // para la sf (comienza por 2).
            if (substr($id_ubi, 0, 1) == 2) {
                // Si soy sv solo tengo acceso a los de la dl,
                // En caso contrario puedo ver los de todas las regiones.
                if (ConfigGlobal::mi_sfsv() == 1) {
                    $gesCentro = new GestorCentroEllas();
                } else {
                    $gesCentro = new GestorCentro();
                }
            } else {
                // Si soy sf solo tengo acceso a los de la dl,
                // En caso contrario puedo ver los de todas las regiones.
                if (ConfigGlobal::mi_sfsv() == 2) {
                    $gesCentro = new GestorCentroEllos();
                } else {
                    $gesCentro = new GestorCentro();
                }
            }
            $cCentros = $gesCentro->getCentros(array('id_ubi' => $id_ubi));
            if (!empty($cCentros)) {
                $oUbi = $cCentros[0];
            } else {
                $oUbi = new Casa($id_ubi);
            }
        }
        return $oUbi;
    }
}