<?php

namespace src\actividadplazas\infrastructure\persistence\postgresql;

use src\actividadplazas\domain\contracts\ActividadPlazasDlRepositoryInterface;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla da_plazas_dl (plazas de cada dl, en la BD de la dl) a la interfaz del repositorio.
 * Hereda del padre la clave (id_activ, id_dl, dl_tabla).
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgActividadPlazasDlRepository extends PgActividadPlazasRepository implements ActividadPlazasDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDB');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('da_plazas_dl');
    }
}
