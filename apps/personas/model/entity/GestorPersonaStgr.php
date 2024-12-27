<?php

namespace personas\model\entity;

use ubis\model\entity\GestorDelegacion;

/**
 * GestorPersonaEx
 *
 * Classe per gestionar la llista d'objectes de la clase PersonaEx
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 11/03/2014
 */
class GestorPersonaStgr extends GestorPersonaGlobal
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /* CONSTRUCTOR -------------------------------------------------------------- */


    function __construct()
    {
        $oDbl = $GLOBALS['oDBP'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('p_de_paso');
    }

    public function getPerosnasOtrosStgr(string $Qapellido1)
    {
        // Buscar todos los que han hecho cv fuera de su dl. están en publicv.p_de_paso
        $aWhere = [];
        $aWhere['apellido1'] = '^' . $Qapellido1;
        $aOperador['apellido1'] = 'sin_acentos';

        $aWhere['situacion'] = 'A';
        //$aWhere['stgr'] = 'b|c1|c2';
        //$aOperador['stgr'] = '~';
        $aWhere['_ordre'] = 'dl,stgr,apellido1,nom';

        return $this->getPersonas($aWhere, $aOperador);

    }
}
