<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\ubis\domain\contracts\DireccionCentroRepositoryInterface;
use src\ubis\domain\contracts\PlanoOperationsTrait;

/**
 * Clase que adapta la tabla u_dir_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class PgDireccionCentroRepository extends PgDireccionRepository implements DireccionCentroRepositoryInterface
{
    use PlanoOperationsTrait;

    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBP');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl);
        $this->setNomTabla('u_dir_ctr');
    }

    /**
     * @return array<string, mixed>
     */
    public function downloadPlano(int $id_direccion): array
    {
        return $this->planoDownload($id_direccion);
    }

    protected function getPdoConnection(): \PDO
    {
        return $this->getoDbl();
    }

}