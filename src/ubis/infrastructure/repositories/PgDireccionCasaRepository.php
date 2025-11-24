<?php

namespace src\ubis\infrastructure\repositories;


/**
 * Clase que adapta la tabla u_dir_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class PgDireccionCasaRepository extends PgDireccionRepository
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBPC'];
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_cdc');
    }

}