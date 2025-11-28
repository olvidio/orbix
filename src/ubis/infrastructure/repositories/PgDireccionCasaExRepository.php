<?php

namespace src\ubis\infrastructure\repositories;

use core\ConfigGlobal;
use src\ubis\domain\contracts\DireccionCasaExRepositoryInterface;
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
class PgDireccionCasaExRepository extends PgDireccionRepository implements DireccionCasaExRepositoryInterface
{
    use PlanoOperationsTrait;

    public function __construct()
    {
        parent::__construct();
        $oDbl = $GLOBALS['oDBR'];
        $oDbl_Select = $GLOBALS['oDBR_Select'];
        $this->setoDbl($oDbl);
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('u_dir_cdc_ex');
    }

    // Wrapper methods para mantener la interfaz simple
    public function downloadPlano(int $id_direccion): array
    {
        return $this->planoDownload($id_direccion);
    }

    public function uploadPlano(int $id_direccion, ?string $nom, ?string $extension, $fichero): void
    {
        $this->planoUpload($id_direccion, $nom, $extension, $fichero);
    }

    public function deletePlano(int $id_direccion): void
    {
        $this->planoBorrar($id_direccion);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('u_dir_cdc_ex_id_item_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    /**
     * @throws \Exception
     */
    public function getNewIdDireccion($id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }
}