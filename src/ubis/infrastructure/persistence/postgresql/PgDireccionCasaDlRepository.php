<?php

namespace src\ubis\infrastructure\persistence\postgresql;
use src\shared\infrastructure\GlobalPdo;

use src\shared\config\ConfigGlobal;
use src\ubis\domain\contracts\DireccionCasaDlRepositoryInterface;
use src\ubis\domain\contracts\PlanoOperationsTrait;
use src\utils_database\domain\GenerateIdGlobal;

/**
 * Clase que adapta la tabla u_dir_ctr a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 21/11/2025
 */
class PgDireccionCasaDlRepository extends PgDireccionRepository implements DireccionCasaDlRepositoryInterface
{
    use PlanoOperationsTrait;

    public function __construct()
    {
        parent::__construct();
        $oDbl = GlobalPdo::get('oDBC');
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_cdc_dl');
    }

    // Wrapper methods para mantener la interfaz simple
    /**
     * @return array<string, mixed>
     */
    public function downloadPlano(int $id_direccion): array
    {
        return $this->planoDownload($id_direccion);
    }

    public function uploadPlano(int $id_direccion, ?string $nom, ?string $extension, string $fichero): void
    {
        $this->planoUpload($id_direccion, $nom, $extension, $fichero);
    }

    public function deletePlano(int $id_direccion): void
    {
        $this->planoBorrar($id_direccion);
    }

    protected function getPdoConnection(): \PDO
    {
        return $this->getoDbl();
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_dir_cdc_dl_id_auto_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }

    /**
     * @throws \Exception
     */
    public function getNewIdDireccion(int $id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}
