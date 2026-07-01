<?php

namespace src\encargossacd\infrastructure\persistence\postgresql;

use PDO;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdRepositoryInterface;
use src\encargossacd\domain\entity\PropuestaEncargoSacd;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\traits\HandlesPdoErrors;

class PgPropuestaEncargoSacdRepository extends ClaseRepository implements PropuestaEncargoSacdRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBE'));
        $this->setoDbl_select(GlobalPdo::get('oDBE_Select'));
        $this->setNomTabla('propuesta_encargos_sacd');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<PropuestaEncargoSacd>
     */
    public function getPropuestasEncargoSacd(array $aWhere = [], array $aOperators = []): array
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
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
            if (in_array($sOperador, ['BETWEEN', 'IS NULL', 'IS NOT NULL', 'OR', 'IN', 'NOT IN', 'TXT'], true)) {
                unset($aWhere[$camp]);
            }
        }
        $sCondicion = implode(' AND ', $aCondicion);
        if ($sCondicion !== '') {
            $sCondicion = ' WHERE ' . $sCondicion;
        }
        $sOrdre = '';
        $ordreVal = $aWhere['_ordre'] ?? null;
        if (is_string($ordreVal) && $ordreVal !== '') {
            $sOrdre = ' ORDER BY ' . $ordreVal;
        }
        unset($aWhere['_ordre'], $aWhere['_limit']);
        $sQry = "SELECT * FROM $nom_tabla " . $sCondicion . $sOrdre;
        $stmt = $this->prepareAndExecute($oDbl, $sQry, $aWhere, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return [];
        }

        $out = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $aDatos) {
            if (!is_array($aDatos)) {
                continue;
            }
            $aDatos['f_ini'] = (new ConverterDate('date', $aDatos['f_ini']))->fromPg();
            $aDatos['f_fin'] = (new ConverterDate('date', $aDatos['f_fin']))->fromPg();
            $normalized = [];
            foreach ($aDatos as $key => $value) {
                $normalized[(string) $key] = $value;
            }
            $out[] = PropuestaEncargoSacd::fromArray($normalized);
        }

        return $out;
    }

    public function findById(int $id_item): ?PropuestaEncargoSacd
    {
        $rows = $this->getPropuestasEncargoSacd(['id_item' => $id_item]);

        return $rows[0] ?? null;
    }

    public function existenLasTablas(): bool
    {
        $oDbl = $this->getoDbl_Select();
        $nom_tabla = $this->getNomTabla();
        $schema_name = ConfigGlobal::mi_region_dl();
        $sql = "SELECT EXISTS (
            SELECT FROM information_schema.tables
            WHERE table_schema = '$schema_name'
            AND table_name = '$nom_tabla'
        )";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) && filter_var($row['exists'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    public function Eliminar(PropuestaEncargoSacd $propuesta): bool
    {
        $id_item = $propuesta->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE id_item = $id_item";

        return $this->pdoExec($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }

    public function Guardar(PropuestaEncargoSacd $propuesta): bool
    {
        $id_item = $propuesta->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $id_item <= 0 || $this->isNew($id_item);

        if ($bInsert && $id_item <= 0) {
            $id_item = $this->getNewId();
            $propuesta->setId_item($id_item);
        }

        $aDatos = $propuesta->toArrayForDatabase([
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = "
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					modo                     = :modo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					id_nom_new               = :id_nom_new";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_enc,id_nom,modo,f_ini,f_fin,id_nom_new)';
            $valores = '(:id_item,:id_enc,:id_nom,:modo,:f_ini,:f_fin,:id_nom_new)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    private function isNew(int $id_item): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE id_item = $id_item";
        $stmt = $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if ($stmt === false) {
            return true;
        }

        return !$stmt->rowCount();
    }

    public function getNewId(): int
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval('propuesta_encargos_sacd_id_item_seq'::regclass)";
        $stmt = $oDbl->query($sQuery);
        if ($stmt === false) {
            return 0;
        }
        $id = $stmt->fetchColumn();

        return is_numeric($id) ? (int) $id : 0;
    }

    public function borrarTabla(): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DROP TABLE IF EXISTS $nom_tabla CASCADE";

        return $this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__) !== false;
    }

    public function crearTabla(): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $this->borrarTabla();

        $sql = "CREATE TABLE $nom_tabla AS
                SELECT id_schema, id_item, id_enc, id_nom, modo, f_ini, f_fin, id_nom AS id_nom_new
                FROM encargos_sacd WHERE f_fin IS NULL";
        if ($this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__) === false) {
            return false;
        }

        $esquema_sfsv = ConfigGlobal::mi_region_dl();
        $id_seq = 'propuesta_encargos_sacd_id_item_seq';
        $campo_seq = 'id_item';
        $a_sql = [
            "CREATE SEQUENCE IF NOT EXISTS $id_seq",
            "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE",
            "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass)",
            "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$esquema_sfsv'::text)",
            "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_ukey UNIQUE ($campo_seq)",
            "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq)",
            "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargos_sacd_id_enc_ukey
                    UNIQUE (id_enc, id_nom_new, modo, f_ini)",
            "SELECT setval('$id_seq', COALESCE((SELECT MAX($campo_seq)+1 FROM $nom_tabla), 1), FALSE)
                    FROM information_schema.key_column_usage
                    WHERE constraint_name LIKE '%pkey%'
                    ORDER BY table_name",
        ];

        $oDbl->beginTransaction();
        foreach ($a_sql as $sqlStep) {
            if ($this->pdoExec($oDbl, $sqlStep, __METHOD__, __FILE__, __LINE__) === false) {
                $oDbl->rollBack();

                return false;
            }
        }
        $oDbl->commit();

        return true;
    }
}
