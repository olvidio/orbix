<?php

namespace src\actividades\infrastructure\repositories;

use core\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\shared\traits\HandlesPdoErrors;
use src\ubis\domain\contracts\TipoTelecoRepositoryInterface;
use src\utils_database\domain\GenerateIdGlobal;


/**
 * Clase que adapta la tabla a_actividades_dl a la interfaz del repositorio
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 3/12/2025
 */
class PgActividadDlRepository extends PgActividadAllRepository implements ActividadDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(TipoTelecoRepositoryInterface $tipoTelecoRepository)
    {
        parent::__construct($tipoTelecoRepository);
        $oDbl = $GLOBALS['oDBC'];
        $this->setoDbl($oDbl);
        $oDbl_Select = $GLOBALS['oDBC_Select'];
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_actividades_dl');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_actividades_dl_id_auto_seq'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }

    /**
     * @throws \Exception
     */
    public function getNewIdActividad(int $id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }

    public function deleteActividadesEnPeriodoEnProyecto($f_ini, $f_fin): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $status = StatusId::PROYECTO;

        $sql = "DELETE FROM $nom_tabla
                    WHERE f_ini >= '$f_ini' AND f_ini <= '$f_fin' AND status = $status";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function getArrayActividadesEnPeriodoNoEnProyecto($f_ini, $f_fin): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $status = StatusId::PROYECTO;

        $sql = "SELECT id_activ,nom_activ FROM $nom_tabla
                    WHERE f_ini >= '$f_ini' AND f_ini <= '$f_fin' AND status != $status";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);

        $aOpciones = [];
        foreach ($stmt as $row) {
            $id_activ = $row[0];
            $nom_activ = $row[1];
            $aOpciones[$id_activ] = $nom_activ;
        }
        return $aOpciones;
    }
}