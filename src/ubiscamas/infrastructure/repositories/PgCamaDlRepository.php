<?php

namespace src\ubiscamas\infrastructure\repositories;

use src\ubiscamas\domain\contracts\CamaDlRepositoryInterface;

/**
 * Clase que adapta la tabla du_camas_dl a la interfaz del repositorio
 * Extiende de PgCamaRepository
 *
 * @package orbix
 * @subpackage ubiscamas
 * @author Daniel Serrabou
 * @version 1.0
 * @created 12/03/2026
 */
class PgCamaDlRepository extends PgCamaRepository implements CamaDlRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('du_camas_dl');
    }
}
