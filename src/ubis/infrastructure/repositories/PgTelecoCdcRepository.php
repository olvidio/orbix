<?php

namespace src\ubis\infrastructure\repositories;


/**
 * Clase que adapta la tabla d_teleco_cdc a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 19/11/2025
 */
class PgTelecoCdcRepository extends PgTelecoUbiRepository
{

    public function __construct()
    {
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_teleco_cdc');
    }

    public function getNewId()
    {
        throw new \Exception(_("Este repositorio no admite la generación de nuevos IDs."));
    }
}