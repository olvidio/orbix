<?php

namespace src\actividades\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\actividades\domain\contracts\ActividadDlRepositoryInterface;
use src\actividades\domain\value_objects\StatusId;
use src\shared\infrastructure\GlobalPdo;
use src\shared\traits\HandlesPdoErrors;
use src\utils_database\domain\GenerateIdGlobal;
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
class PgActividadDlRepository extends PgActividadAllRepository implements ActividadDlRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct(TiposActividades $tiposActividades)
    {
        parent::__construct($tiposActividades);
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('a_actividades_dl');
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('a_actividades_dl_id_auto_seq'::regclass)";
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
    public function getNewIdActividad(int $id): int
    {
        $miRegionDl = ConfigGlobal::mi_region_dl();
        return GenerateIdGlobal::generateIdGlobal($miRegionDl, $this->getNomTabla(), $id);
    }

    public function deleteActividadesEnPeriodoEnProyecto(string $f_ini, string $f_fin): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $status = StatusId::PROYECTO;

        $sql = "DELETE FROM $nom_tabla
                    WHERE f_ini >= '$f_ini' AND f_ini <= '$f_fin' AND status = $status";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * @return array<int, string>
     */
    public function getArrayActividadesEnPeriodoNoEnProyecto(string $f_ini, string $f_fin): array
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();

        $status = StatusId::PROYECTO;

        $sql = "SELECT id_activ,nom_activ FROM $nom_tabla
                    WHERE f_ini >= '$f_ini' AND f_ini <= '$f_fin' AND status != $status";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }
        $aOpciones = [];
        foreach ($stmt as $row) {
            if (!is_array($row)) {
                continue;
            }
            $id_activ = $row[0] ?? 0;
            $nom_activ = $row[1] ?? '';
            $aOpciones[is_numeric($id_activ) ? (int) $id_activ : 0] = is_scalar($nom_activ) ? (string) $nom_activ : '';
        }
        return $aOpciones;
    }

    public function execMaintenanceSql(string $sql): bool
    {
        $oDbl = $this->getoDbl();
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }
}