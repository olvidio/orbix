<?php

namespace src\encargossacd\infrastructure\persistence\postgresql;

use PDO;
use src\encargossacd\domain\contracts\PropuestaEncargoSacdHorarioRepositoryInterface;
use src\encargossacd\domain\entity\EncargoSacdHorario;
use src\shared\infrastructure\GlobalPdo;
use src\shared\infrastructure\persistence\ClaseRepository;
use src\shared\infrastructure\persistence\ConverterDate;
use src\shared\infrastructure\persistence\postgresql\Condicion;
use src\shared\traits\HandlesPdoErrors;

class PgPropuestaEncargoSacdHorarioRepository extends ClaseRepository implements PropuestaEncargoSacdHorarioRepositoryInterface
{
    use HandlesPdoErrors;

    public function __construct()
    {
        $this->setoDbl(GlobalPdo::get('oDBE'));
        $this->setoDbl_select(GlobalPdo::get('oDBE_Select'));
        $this->setNomTabla('propuesta_encargo_sacd_horario');
    }

    /**
     * @param array<string, mixed> $aWhere
     * @param array<string, string> $aOperators
     * @return list<EncargoSacdHorario>
     */
    public function getEncargoSacdHorarios(array $aWhere = [], array $aOperators = []): array
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
            $out[] = EncargoSacdHorario::fromArray($normalized);
        }

        return $out;
    }

    public function Guardar(EncargoSacdHorario $horario): bool
    {
        $id_item = $horario->getId_item();
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $id_item <= 0 || $this->isNew($id_item);

        if ($bInsert && $id_item <= 0) {
            $id_item = $this->getNewId();
            $horario->setId_item($id_item);
        }

        $aDatos = $horario->toArrayForDatabase([
            'h_ini' => fn($v) => (new ConverterDate('time', $v))->toPg(),
            'h_fin' => fn($v) => (new ConverterDate('time', $v))->toPg(),
            'f_ini' => fn($v) => (new ConverterDate('date', $v))->toPg(),
            'f_fin' => fn($v) => (new ConverterDate('date', $v))->toPg(),
        ]);

        if ($bInsert === false) {
            unset($aDatos['id_item']);
            $update = "
					id_enc                   = :id_enc,
					id_nom                   = :id_nom,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					dia_ref                  = :dia_ref,
					dia_num                  = :dia_num,
					mas_menos                = :mas_menos,
					dia_inc                  = :dia_inc,
					h_ini                    = :h_ini,
					h_fin                    = :h_fin,
					id_item_tarea_sacd       = :id_item_tarea_sacd";
            $sql = "UPDATE $nom_tabla SET $update WHERE id_item = $id_item";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        } else {
            $campos = '(id_item,id_enc,id_nom,f_ini,f_fin,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin,id_item_tarea_sacd)';
            $valores = '(:id_item,:id_enc,:id_nom,:f_ini,:f_fin,:dia_ref,:dia_num,:mas_menos,:dia_inc,:h_ini,:h_fin,:id_item_tarea_sacd)';
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            $stmt = $this->pdoPrepare($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        }
        if ($stmt === false) {
            return false;
        }

        return $this->pdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
    }

    public function cambiarSacd(int $id_enc, int $id_sacd_old, int $id_sacd_new): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "UPDATE $nom_tabla SET id_nom = :id_sacd_new WHERE id_enc = :id_enc AND id_nom = :id_sacd_old";

        return $this->prepareAndExecute($oDbl, $sql, [
            'id_sacd_new' => $id_sacd_new,
            'id_enc' => $id_enc,
            'id_sacd_old' => $id_sacd_old,
        ], __METHOD__, __FILE__, __LINE__) !== false;
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
        $sQuery = "select nextval('propuesta_encargo_sacd_horario_id_item_seq'::regclass)";
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

        $sql = "CREATE TABLE $nom_tabla AS (
            SELECT h.*
            FROM encargo_sacd_horario h JOIN propuesta_encargos_sacd e ON (h.id_item_tarea_sacd = e.id_item)
            WHERE h.f_fin IS NULL )";
        if ($this->pdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__) === false) {
            return false;
        }

        $esquema_sfsv = \src\shared\config\ConfigGlobal::mi_region_dl();
        $id_seq = 'propuesta_encargo_sacd_horario_id_item_seq';
        $campo_seq = 'id_item';
        $nom_tabla_ref = "\"$esquema_sfsv\".propuesta_encargos_sacd";
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
            "ALTER TABLE $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_ukey UNIQUE ($campo_seq)",
            "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item, id_nom)",
            "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT propuesta_encargo_sacd_horario_id_item_tarea_sacd_fkey
                     FOREIGN KEY (id_item_tarea_sacd) REFERENCES $nom_tabla_ref(id_item) ON DELETE CASCADE",
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
