<?php

namespace src\notas\infrastructure\repositories;

use src\notas\domain\contracts\ActaTribunalDlRepositoryInterface;

/**
 * Clase que adapta la tabla e_actas_tribunal_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 27/12/2025
 */
class PgActaTribunalDlRepository extends PgActaTribunalRepository implements ActaTribunalDlRepositoryInterface
{
    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDB'];
        $this->setoDbl($oDbl);
        $this->setNomTabla('e_actas_tribunal_dl');
    }

    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('e_actas_tribunal_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }
}