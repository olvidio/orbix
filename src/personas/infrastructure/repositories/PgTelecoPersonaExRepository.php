<?php

namespace src\personas\infrastructure\repositories;

use src\personas\domain\contracts\TelecoPersonaExRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_teleco_personas a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 12/12/2025
 */
class PgTelecoPersonaExRepository extends PgTelecoPersonaRepository implements TelecoPersonaExRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBR'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('d_teleco_personas_ex');
    }


    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('d_teleco_personas_ex_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

}