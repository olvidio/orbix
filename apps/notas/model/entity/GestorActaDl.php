<?php
namespace notas\model\entity;

/**
 * GestorActa
 *
 * Classe per gestionar la llista d'objectes de la clase Acta
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class GestorActaDl extends GestorActa
{
    function __construct()
    {
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas_dl');
    }

}
