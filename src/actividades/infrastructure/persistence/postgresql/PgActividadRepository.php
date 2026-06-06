<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\actividades\domain\contracts\ActividadRepositoryInterface;
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
class PgActividadRepository extends PgActividadAllRepository implements ActividadRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(TiposActividades $tiposActividades)
    {
        parent::__construct($tiposActividades);
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        // `av_actividades` es vista (UNION…) y no admite INSERT/UPDATE; las escrituras van a la tabla física del esquema.
        $this->setNomTabla('a_actividades_dl');
    }

    protected function getNomTablaSelect(): string
    {
        return 'av_actividades';
    }

}