<?php

namespace src\asignaturas\infrastructure\persistence\postgresql;

use PDO;
use src\asignaturas\domain\contracts\AsignaturaRepositoryInterface;
use src\asignaturas\domain\entity\Asignatura;
use src\asignaturas\domain\value_objects\PlanEstudios;
use src\shared\domain\helpers\FuncTablasSupport;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\infrastructure\persistence\postgresql\Set;
use src\shared\traits\HandlesPdoErrors;
use stdClass;

class PgAsignaturaRepository extends ClaseRepository implements AsignaturaRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $oDbl = GlobalPdo::get('oDBPC');
        $this->setoDbl($oDbl);
        $oDbl_Select = GlobalPdo::get('oDBPC_Select');
        $this->setoDbl_select($oDbl_Select);
        $this->setNomTabla('xa_asignaturas');
    }

    /**
     * @param array<string, mixed> $aWhere
     */
    public function getJsonAsignaturas(array $aWhere): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $planEstudios = isset($aWhere['plan_estudios']) && is_numeric($aWhere['plan_estudios'])
            ? (int) $aWhere['plan_estudios']
            : null;
        unset($aWhere['plan_estudios']);

        $sCondi = '';
        foreach ($aWhere as $camp => $val) {
            if ($camp === 'nombre_asignatura' && !empty($val) && is_scalar($val)) {
                $valStr = (string) $val;
                $sCondi .= "WHERE active=true AND nombre_asignatura ILIKE '%$valStr%'";
            }
            if ($camp === 'id' && !empty($val) && is_scalar($val)) {
                $valStr = (string) $val;
                if ($sCondi !== '') {
                    $sCondi .= " AND id_asignatura = $valStr";
                } else {
                    $sCondi .= "WHERE id_asignatura = $valStr";
                }
            }
        }
        if ($planEstudios !== null) {
            $sCondi .= ($sCondi !== '' ? ' AND ' : 'WHERE ') . "$planEstudios = ANY(plan_estudios)";
        }
        $sOrdre = " ORDER BY id_nivel";
        $sLimit = " LIMIT 25";
        $sQuery = "SELECT DISTINCT id_asignatura,nombre_asignatura,id_nivel FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return '[]';
        }

        $json = '[';
        $i = 0;
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $id_asignatura = $aClave['id_asignatura'] ?? $aClave[0] ?? null;
            $nombre_asignatura = $aClave['nombre_asignatura'] ?? $aClave[1] ?? null;
            if (!is_scalar($id_asignatura) || !is_scalar($nombre_asignatura)) {
                continue;
            }
            $i++;
            $idStr = (string) $id_asignatura;
            $nombreStr = str_replace('"', '\\"', (string) $nombre_asignatura);
            $nombreStr = str_replace("'", "\\'", $nombreStr);
            $json .= ($i > 1) ? ',' : '';
            $json .= "{\"value\":\"$idStr\",\"label\":\"$nombreStr\"}";
        }
        $json .= ']';

        return $json;
    }

    /**
     * @return array<int|string, array{nombre_asignatura: mixed, creditos: mixed}>
     */
    public function getArrayAsignaturasCreditos(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_asignatura, nombre_asignatura, creditos FROM $nom_tabla ORDER BY id_nivel";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $row) {
            if (!is_array($row)) {
                continue;
            }
            $id = $row['id_asignatura'] ?? $row[0] ?? null;
            $nombre = $row['nombre_asignatura'] ?? $row[1] ?? null;
            $creditos = $row['creditos'] ?? $row[2] ?? null;
            if (!is_int($id) && !is_string($id)) {
                if (!is_numeric($id)) {
                    continue;
                }
                $id = (int) $id;
            }
            $aOpciones[$id] = ['nombre_asignatura' => $nombre, 'creditos' => $creditos];
        }
        return $aOpciones;
    }

    /**
     * @return array<int|string, string>
     */
    public function getArrayAsignaturasConSeparador(
        bool $op_genericas = true,
        ?int $planEstudios = PlanEstudios::PLAN_2026,
    ): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sWhere = "WHERE active = 't' ";
        if ($planEstudios !== null) {
            $sWhere .= ' AND :plan = ANY(plan_estudios)';
        }
        if (!$op_genericas) {
            $genericas = $this->getListaOpGenericas('csv');
            $sWhere .= " AND id_nivel NOT IN ($genericas)";
        }
        $sQuery = "SELECT id_asignatura, nombre_asignatura, CASE WHEN id_nivel < 3000 THEN xa_asignaturas.id_nivel ELSE 3001 END AS op FROM $nom_tabla $sWhere ORDER BY op,nombre_asignatura;";
        if ($planEstudios !== null) {
            $stmt = $this->prepareAndExecute($oDbl, $sQuery, ['plan' => $planEstudios], __METHOD__, __FILE__, __LINE__);
        } else {
            $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        $c = 0;
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            $clave = $aClave['id_asignatura'] ?? $aClave[0] ?? null;
            $val = $aClave['nombre_asignatura'] ?? $aClave[1] ?? null;
            $id_op = $aClave['op'] ?? $aClave[2] ?? null;
            if (!is_scalar($val) || $id_op === null) {
                continue;
            }
            if (!is_int($clave) && !is_string($clave)) {
                if (!is_numeric($clave)) {
                    continue;
                }
                $clave = (int) $clave;
            }
            if (is_numeric($id_op) && (int) $id_op > 3000 && $c < 1) {
                $aOpciones[3000] = '----------';
                $c = 1;
            }
            $aOpciones[$clave] = (string) $val;
        }

        return $aOpciones;
    }

    public function getListaOpGenericas(string $formato = ''): string
    {
        switch ($formato) {
            case 'json':
                return "[\"1230\",\"1231\",\"1232\",\"2430\",\"2431\",\"2432\",\"2433\",\"2434\"]";
            case 'csv':
            default:
                return "1230,1231,1232,2430,2431,2432,2433,2434";
        }
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getArrayAsignaturas(): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $sQuery = "SELECT id_asignatura,nombre_corto FROM $nom_tabla ORDER BY id_asignatura";
        $stmt = $this->pdoQuery($oDbl, $sQuery, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $aOpciones = [];
        foreach ($stmt as $aClave) {
            if (!is_array($aClave)) {
                continue;
            }
            if (isset($aClave['id_asignatura'])) {
                $idKey = $aClave['id_asignatura'];
                if (!is_int($idKey) && !is_string($idKey)) {
                    if (!is_numeric($idKey)) {
                        continue;
                    }
                    $idKey = (int) $idKey;
                }
                $nombreCorto = $aClave['nombre_corto'] ?? null;
                $aOpciones[$idKey] = is_scalar($nombreCorto) ? (string) $nombreCorto : null;
            } elseif (isset($aClave[0])) {
                $idKey = $aClave[0];
                if (!is_int($idKey) && !is_string($idKey)) {
                    if (!is_numeric($idKey)) {
                        continue;
                    }
                    $idKey = (int) $idKey;
                }
                $nombreCorto = $aClave[1] ?? null;
                $aOpciones[$idKey] = is_scalar($nombreCorto) ? (string) $nombreCorto : null;
            }
        }

        return $aOpciones;
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     */
    public function getAsignaturasAsJson(array $aWhere = [], array $aOperators = []): string
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $jsonAsignaturas = [];
        $oCondicion = new Condicion();
        $aCondi = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondi[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN' || $sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondi = implode(' AND ', $aCondi);
        if ($sCondi !== '') {
            $sCondi = " WHERE " . $sCondi;
        }
        $sLimit = '';
        $sOrdre = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondi . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return '[]';
        }

        foreach ($stmt as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = $this->normalizeRowFromDb($aDatos);
            $oAsignatura = Asignatura::fromArray($normalized);
            $oMin = new stdClass();
            $oMin->id_asignatura = $oAsignatura->getIdAsignaturaVo()->value();
            $oMin->id_nivel = $oAsignatura->getIdNivelVo()->value();
            $oMin->nombre_asignatura = $oAsignatura->getNombre_asignatura();
            $oMin->creditos = $oAsignatura->getCreditos();
            $encoded = json_encode($oMin);
            if (is_string($encoded)) {
                $jsonAsignaturas[] = $encoded;
            }
        }
        $result = json_encode($jsonAsignaturas);
        return is_string($result) ? $result : '[]';
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<Asignatura>
     */
    public function getAsignaturas(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $AsignaturaSet = new Set();
        $oCondicion = new Condicion();
        $aCondicion = [];
        foreach ($aWhere as $camp => $val) {
            if ($camp === '_ordre' || $camp === '_limit') {
                continue;
            }
            $sOperador = $aOperators[$camp] ?? '';
            if ($a = $oCondicion->getCondicion($camp, $sOperador, $val)) {
                $aCondicion[] = $a;
            }
            if ($sOperador === 'BETWEEN' || $sOperador === 'IS NULL' || $sOperador === 'IS NOT NULL' || $sOperador === 'OR') {
                unset($aWhere[$camp]);
            }
            if ($sOperador === 'IN' || $sOperador === 'NOT IN' || $sOperador === 'TXT') {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = " WHERE " . $sCondicion;
        }
        $sOrdre = '';
        $sLimit = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        if (isset($aWhere['_ordre'])) {
            unset($aWhere['_ordre']);
        }
        $limitVal = $aWhere['_limit'] ?? null;
        if ((is_string($limitVal) || is_int($limitVal)) && (string) $limitVal !== '') {
            $sLimit = ' LIMIT ' . $limitVal;
        }
        if (isset($aWhere['_limit'])) {
            unset($aWhere['_limit']);
        }
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre . $sLimit;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $normalized = $this->normalizeRowFromDb($aDatos);
            $AsignaturaSet->add(Asignatura::fromArray($normalized));
        }
        /** @var list<Asignatura> $items */
        $items = array_values($AsignaturaSet->getTot());
        return $items;
    }

    public function Eliminar(Asignatura $Asignatura): bool
    {
        $id_asignatura = $Asignatura->getIdAsignaturaVo()->value();
        $planPg = FuncTablasSupport::arrayPhp2pg($Asignatura->getPlanEstudiosVo()?->toArray() ?? []);
        if ($planPg === '' || $planPg === '{}') {
            return false;
        }
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_asignatura = $id_asignatura AND plan_estudios = '$planPg'::integer[]";
        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(Asignatura $Asignatura): bool
    {
        $id_asignatura = $Asignatura->getIdAsignaturaVo()->value();
        $planArray = $Asignatura->getPlanEstudiosVo()?->toArray() ?? [];
        $planPg = FuncTablasSupport::arrayPhp2pg($planArray);
        if ($planPg === '' || $planPg === '{}') {
            return false;
        }
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($id_asignatura, $planArray);

        $aDatos = $Asignatura->toArrayForDatabase();
        if ($bInsert === false) {
            unset($aDatos['id_asignatura']);
            $update = "
					id_nivel                 = :id_nivel,
					nombre_asignatura        = :nombre_asignatura,
					nombre_corto             = :nombre_corto,
					creditos                 = :creditos,
					year                     = :year,
					id_sector                = :id_sector,
					active                   = :active,
					id_tipo                  = :id_tipo,
					plan_estudios            = :plan_estudios";
            $sql = "UPDATE $nom_tabla SET $update
                WHERE id_asignatura = $id_asignatura AND plan_estudios = '$planPg'::integer[]";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = "(id_asignatura,id_nivel,nombre_asignatura,nombre_corto,creditos,year,id_sector,active,id_tipo,plan_estudios)";
            $valores = "(:id_asignatura,:id_nivel,:nombre_asignatura,:nombre_corto,:creditos,:year,:id_sector,:active,:id_tipo,:plan_estudios)";
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    /**
     * @param list<int> $plan_estudios
     */
    private function isNew(int $id_asignatura, array $plan_estudios): bool
    {
        $planPg = FuncTablasSupport::arrayPhp2pg($plan_estudios);
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT 1 FROM $nom_tabla WHERE id_asignatura = $id_asignatura AND plan_estudios = '$planPg'::integer[]";
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
    public function datosById(int $id_asignatura, int|array|null $plan_estudios = null): array|false
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        if (is_array($plan_estudios)) {
            $planPg = FuncTablasSupport::arrayPhp2pg($plan_estudios);
            $sql = "SELECT * FROM $nom_tabla
                WHERE id_asignatura = $id_asignatura
                  AND plan_estudios = '$planPg'::integer[]";
        } elseif ($plan_estudios !== null) {
            $sql = "SELECT * FROM $nom_tabla
                WHERE id_asignatura = $id_asignatura
                  AND $plan_estudios = ANY(plan_estudios)
                ORDER BY cardinality(plan_estudios) ASC
                LIMIT 1";
        } else {
            $sql = "SELECT * FROM $nom_tabla WHERE id_asignatura = $id_asignatura LIMIT 1";
        }
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $aDatos = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!is_array($aDatos)) {
            return false;
        }
        return $this->normalizeRowFromDb($aDatos);
    }

    /**
     * @param array<string|int, mixed> $aDatos
     * @return array<string, mixed>
     */
    private function normalizeRowFromDb(array $aDatos): array
    {
        if (is_string($aDatos['plan_estudios'] ?? null)) {
            $aDatos['plan_estudios'] = FuncTablasSupport::arrayPgInteger2php($aDatos['plan_estudios']);
        }

        $result = [];
        foreach ($aDatos as $key => $value) {
            $result[(string) $key] = $value;
        }

        return $result;
    }

    public function findById(int $id_asignatura, int|array|null $plan_estudios = null): ?Asignatura
    {
        $aDatos = $this->datosById($id_asignatura, $plan_estudios);
        if ($aDatos === false) {
            return null;
        }
        return Asignatura::fromArray($aDatos);
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('xa_asignaturas_id_asignatura_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }
}
