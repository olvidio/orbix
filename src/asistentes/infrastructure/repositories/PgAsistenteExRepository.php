<?php

namespace src\asistentes\infrastructure\repositories;

use core\ClaseRepository;
use core\Condicion;
use core\Set;
use PDO;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\shared\domain\contracts\EventBusInterface;
use src\shared\traits\HandlesPdoErrors;
use web\Desplegable;
use function core\is_true;


/**
 * Clase que adapta la tabla d_asistentes_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 16/12/2025
 */
class PgAsistenteExRepository extends PgAsistenteRepository implements AsistenteExRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(EventBusInterface $eventBus)
    {
        parent::__construct($eventBus);
        $oDbl = $GLOBALS['oDBER'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBER_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_ex');
    }

}