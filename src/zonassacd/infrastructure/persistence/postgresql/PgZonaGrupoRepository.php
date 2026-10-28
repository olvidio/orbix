<?php

namespace src\zonassacd\infrastructure\persistence\postgresql;

use PDO;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\domain\criteria\Criteria;
use src\shared\infrastructure\persistence\postgresql\LegacyWhereAdapter;
use src\shared\infrastructure\persistence\postgresql\PgCriteriaConverter;
use src\shared\traits\HandlesPdoErrors;
use src\shared\traits\RunsCriteriaQueries;
use src\zonassacd\domain\contracts\ZonaGrupoRepositoryInterface;
use src\zonassacd\domain\criteria\ZonaGrupoField;
use src\zonassacd\domain\entity\ZonaGrupo;

class PgZonaGrupoRepository extends ClaseRepository implements ZonaGrupoRepositoryInterface
{
    use HandlesPdoErrors;
    use RunsCriteriaQueries;

    /** Campo (valor del enum) => columna, solo si difieren: aquí coinciden todos. */
    private const COLUMNAS = [];

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('zonas_grupos');
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayZonaGrupos(string $sCondicion = ''): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_grupo, nombre_grupo
				FROM $nom_tabla
				$sCondicion
				ORDER BY orden";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        while ($aRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!is_array($aRow) || !isset($aRow['id_grupo'], $aRow['nombre_grupo'])) {
                continue;
            }
            $aOpciones[$aRow['id_grupo']] = (string) $aRow['nombre_grupo'];
        }
        return $aOpciones;
    }

    /**
     * Grupos de zona que cumplen el criterio.
     *
     * @return list<ZonaGrupo>
     */
    public function matching(Criteria $criteria): array
    {
        $conv = new PgCriteriaConverter(ZonaGrupoField::class, self::COLUMNAS);
        $zonasGrupo = [];
        foreach ($this->rowsMatching($criteria, $conv) as $aDatos) {
            $zonasGrupo[] = ZonaGrupo::fromArray($aDatos);
        }

        return $zonasGrupo;
    }

    /**
     * @deprecated usar matching(Criteria)
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<ZonaGrupo>
     */
    public function getZonasGrupo(array $aWhere = [], array $aOperators = []): array
    {
        $adapter = new LegacyWhereAdapter(ZonaGrupoField::class, self::COLUMNAS);
        return $this->matching($adapter->toCriteria($aWhere, $aOperators));
    }

    public function Eliminar(ZonaGrupo $ZonaGrupo): bool
    {
        $id_grupo = $ZonaGrupo->getId_grupo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_grupo = $id_grupo";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(ZonaGrupo $ZonaGrupo): bool
    {
        $id_grupo = $ZonaGrupo->getId_grupo();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_grupo);

        $aDatos = $ZonaGrupo->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_grupo']);
            $update = "
					nombre_grupo             = :nombre_grupo,
					orden                    = :orden";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_grupo = $id_grupo";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_grupo,nombre_grupo,orden)';
            $valores = '(:id_grupo,:nombre_grupo,:orden)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_grupo): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_grupo = $id_grupo";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }
        if (!$stmt->rowCount()) {
            return true;
        }
        return false;
    }

    /**
     * @return array<string, mixed>|false
     */
    public function datosById(int $id_grupo): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_grupo = $id_grupo";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }
        return $result;
    }

    public function findById(int $id_grupo): ?ZonaGrupo
    {
        $aDatos = $this->datosById($id_grupo);
        if ($aDatos === false) {
            return null;
        }
        return ZonaGrupo::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('zonas_grupos_id_grupo_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
