<?php

namespace core;

use actividades\model\entity\GestorActividad;

/**
 * Set
 *
 * Classe per a gestionar una col·lecció d'objectes.
 *
 * @package delegación
 * @subpackage model
 * @author
 * @version 1.0
 * @created 22/9/2010
 */
class Set
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * getTot() Array de objetos
     *
     * @var array
     */
    private $aCollection = array();
    private $count = 0;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    /**
     * Constructor de la classe.
     *
     * @return GestorActividad
     *
     */
    function __construct()
    {
        // constructor buit
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
    function add($oElement)
    {
        $this->aCollection[$this->count++] = $oElement;
    }

    function getTot()
    {
        return $this->aCollection;
    }

    function getElement($count)
    {
        return $this->aCollection[$count];
    }

    function setElement($count, $oElement)
    {
        $this->aCollection[$count] = $oElement;
    }

}