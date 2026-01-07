<?php

namespace src\asistentes\infrastructure\repositories;

use src\asistentes\domain\contracts\AsistenteDlRepositoryInterface;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\traits\HandlesPdoErrors;


/**
 * Clase que adapta la tabla d_asistentes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgAsistenteDlRepository extends PgAsistenteRepository implements AsistenteDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(EventBusInterface $eventBus)
    {
        parent::__construct($eventBus);
        $oDbl = $GLOBALS['oDBE'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBE_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_dl');
    }
}