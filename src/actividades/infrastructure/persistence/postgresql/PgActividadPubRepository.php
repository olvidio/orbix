<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\actividades\domain\contracts\ActividadPubRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\actividades\domain\entity\TiposActividades;


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

    public function __construct(TiposActividades $tiposActividades)
    {
        parent::__construct($tiposActividades);
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('av_actividades_pub');
    }

}