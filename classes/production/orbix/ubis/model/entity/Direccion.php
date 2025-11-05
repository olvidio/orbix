<?php
namespace ubis\model\entity;

use core\ClasePropiedades;

/**
 * Clase que implementa la entidad u_direcciones_global
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */
class Direccion extends ClasePropiedades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */
    /* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */
    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe vuit.
     *
     */
    function __construct($a_id = '')
    {
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public static function NewDireccion($id_direccion)
    {
        $gesDireccion = new GestorDireccionCtr;
        $cDirecciones = $gesDireccion->getDirecciones(array('id_ubi' => $id_direccion));
        if (count($cDirecciones) > 0) {
            $oDireccion = $cDirecciones[0];
        } else {
            $oDireccion = new DireccionCdc($id_direccion);
        }
        return $oDireccion;
    }
}
