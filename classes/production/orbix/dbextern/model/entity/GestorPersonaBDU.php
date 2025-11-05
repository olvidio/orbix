<?php

namespace dbextern\model\entity;

use core\ClaseGestor;
use core\Condicion;
use core\Set;

/**
 * GestorPersonaBDU
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaBDU
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/02/2017
 */
class GestorPersonaBDU extends ClaseGestor
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @return GestorPersonaBDU
     *
     */
    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('tmp_bdu');
    }


    /* MÉTODOS PÚBLICOS -----------------------------------------------------------*/

    /**
     * retorna l'array d'objectes de tipus PersonaBDU
     *
     * @param string sQuery la query a executar.
     * @return array Una col·lecció d'objectes de tipus PersonaBDU
     */
    function getPersonaBDUQuery($sQuery = '')
    {
        $oDbl = $this->getoDbl();
        $oPersonaBDUSet = new Set();

        /*
            if (($oDblSt = $oDbl->query($sQuery)) === false) {
                $sClauError = 'GestorPersonaBDU.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                return false;
            }
         *
         */
        foreach ($oDbl->query($sQuery) as $aDades) {
            $a_pkey = array('identif' => $aDades['identif']);
            $oPersonaBDU = new PersonaBDU($a_pkey);
            $oPersonaBDUSet->add($oPersonaBDU);
        }
        return $oPersonaBDUSet->getTot();
    }

}
