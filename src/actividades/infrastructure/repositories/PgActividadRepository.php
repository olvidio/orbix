<?php

namespace src\actividades\infrastructure\repositories;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;


/**
 * Clase que adapta la tabla a_actividades_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
class PgActividadRepository extends PgActividadAllRepository implements ActividadRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(TipoTelecoRepositoryInterface $tipoTelecoRepository)
    {
        parent::__construct($tipoTelecoRepository);
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('av_actividades');
    }

}