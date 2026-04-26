<?php

namespace src\asistentes\infrastructure\persistence\postgresql;

use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use PDO;
use src\asistentes\domain\contracts\AsistenteExRepositoryInterface;
use src\asistentes\domain\contracts\AsistenteRepositoryInterface;
use src\asistentes\domain\entity\Asistente;
use src\shared\domain\contracts\UnitOfWorkInterface;
use src\shared\traits\HandlesPdoErrors;
use frontend\shared\web\Desplegable;
use function src\shared\domain\helpers\is_true;


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

    public function __construct(UnitOfWorkInterface $unitOfWork)
    {
        parent::__construct($unitOfWork);
        $oDbl = $GLOBALS['oDBER'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBER_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('d_asistentes_ex');
    }

}