<?php

namespace src\actividades\infrastructure\repositories;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\utils_database\domain\GenerateIdGlobal;


/**
 * Clase que adapta la tabla a_actividades_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
class PgActividadPubRepository extends PgActividadAllRepository implements ActividadPubRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(TipoTelecoRepositoryInterface $tipoTelecoRepositor)
    {
        parent::__construct($tipoTelecoRepositor);
        $oDbl = $GLOBALS['oDBPC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBPC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('av_actividades_pub');
    }

}