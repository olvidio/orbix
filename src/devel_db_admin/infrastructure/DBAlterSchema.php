<?php

declare(strict_types=1);

namespace src\devel_db_admin\infrastructure;

use PDO;
use PDOException;
use PDOStatement;
use src\actividadcargos\domain\contracts\CargoRepositoryInterface;
use src\shared\infrastructure\logging\GestorErrores;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

class DBAlterSchema
{
    protected ?PDO $pdoDB = null;
    protected string $schema = '';
    protected string $schema_del = '';

    /** @var list<string> */
    private array $errores = [];

    private bool $continuarEnError = false;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la clase.
     */
    public function __construct(
        private readonly ?CargoRepositoryInterface $cargoRepository = null,
    ) {
    }


    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setDbConexion(PDO $pdoDB): void
    {
        $this->pdoDB = $pdoDB;
    }

    /**
     * Recupera el atributo oDbl de DBAlterSchema
     */
    protected function getPdoDB(): PDO
    {
        if ($this->pdoDB === null) {
            throw new \RuntimeException('DBAlterSchema: conexión PDO no configurada.');
        }

        return $this->pdoDB;
    }

    public function setSchema(string $schema): void
    {
        $this->schema = $schema;
    }

    public function setSchemaDel(string $schema): void
    {
        $this->schema_del = $schema;
    }

    public function setContinuarEnError(bool $continuar): void
    {
        $this->continuarEnError = $continuar;
    }

    /**
     * @return list<string>
     */
    public function consumirErrores(): array
    {
        $errores = $this->errores;
        $this->errores = [];

        return $errores;
    }

    private function gestorErrores(): ?GestorErrores
    {
        $gestor = $_SESSION['oGestorErrores'] ?? null;

        return $gestor instanceof GestorErrores ? $gestor : null;
    }

    private function handleSqlError(string $contexto, \PDO|\PDOStatement $oDbl): void
    {
        $err = $oDbl->errorInfo();
        $mensaje = is_string($err[2] ?? null) ? $err[2] : 'Error SQL desconocido';

        if ($this->continuarEnError) {
            $this->errores[] = $contexto . ': ' . $mensaje;
            $gestor = $this->gestorErrores();
            if ($gestor !== null) {
                $gestor->addErrorAppLastErrorNoThrowText(
                    $mensaje,
                    $contexto,
                    (string) __LINE__,
                    __FILE__,
                );
            }

            return;
        }

        $gestor = $this->gestorErrores();
        if ($gestor !== null) {
            $gestor->addErrorAppLastError($oDbl, $contexto, (string) __LINE__, __FILE__);
        }

    }

    private function handlePdoException(string $contexto, \PDOException $e): bool
    {
        if ($this->continuarEnError) {
            $this->errores[] = $contexto . ': ' . $e->getMessage();
            $gestor = $this->gestorErrores();
            if ($gestor !== null) {
                $gestor->addErrorAppLastErrorNoThrowText(
                    $e->getMessage(),
                    $contexto,
                    (string) __LINE__,
                    __FILE__,
                );
            }

            return false;
        }

        throw $e;
    }

    private function prepareAndExecute(string $sql, string $contexto): bool
    {
        $oDbl = $this->getPdoDB();

        try {
            $oDblSt = $oDbl->prepare($sql);
            if ($oDblSt === false) {
                $this->handleSqlError($contexto . '.prepare', $oDbl);

                return false;
            }
            if ($oDblSt->execute() === false) {
                $this->handleSqlError($contexto . '.execute', $oDblSt);

                return false;
            }

            return true;
        } catch (\PDOException $e) {
            return $this->handlePdoException($contexto, $e);
        }
    }


    /**
     * @return list<array{type: string, conname: string, columns: list<string>}>
     */
    private function getUniqueConstraints(string $fullName): array
    {
        $oDbl = $this->getPdoDB();
        $sql = "SELECT c.contype, c.conname, a.attname, u.ord
                FROM pg_constraint c
                JOIN LATERAL unnest(c.conkey) WITH ORDINALITY AS u(attnum, ord) ON true
                JOIN pg_attribute a ON a.attrelid = c.conrelid AND a.attnum = u.attnum
                WHERE c.conrelid = '$fullName'::regclass
                  AND c.contype IN ('p', 'u')
                ORDER BY CASE WHEN c.contype = 'u' THEN 0 ELSE 1 END,
                         array_length(c.conkey, 1) ASC,
                         c.conname,
                         u.ord";

        $stmt = $oDbl->query($sql);
        if ($stmt === false) {
            return [];
        }

        $constraints = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!is_array($row)) {
                continue;
            }
            $nameRaw = $row['conname'] ?? null;
            if (!is_string($nameRaw) || $nameRaw === '') {
                continue;
            }
            $name = $nameRaw;
            $typeRaw = $row['contype'] ?? null;
            $attnameRaw = $row['attname'] ?? null;
            $ordRaw = $row['ord'] ?? null;
            if (!is_string($attnameRaw) || $attnameRaw === '') {
                continue;
            }
            if (is_int($ordRaw)) {
                $ord = $ordRaw;
            } elseif (is_string($ordRaw) && is_numeric($ordRaw)) {
                $ord = (int) $ordRaw;
            } else {
                continue;
            }
            if (!isset($constraints[$name])) {
                $constraints[$name] = [
                    'type' => is_string($typeRaw) ? $typeRaw : '',
                    'conname' => $name,
                    'columns' => [],
                ];
            }
            $constraints[$name]['columns'][$ord] = $attnameRaw;
        }

        $result = [];
        foreach ($constraints as $constraint) {
            ksort($constraint['columns']);
            $constraint['columns'] = array_values($constraint['columns']);
            $result[] = $constraint;
        }

        return $result;
    }

    /**
     * Prefiere restricciones UNIQUE frente a PK (p. ej. e_notas_dl: id_nom + id_asignatura).
     *
     * @return array{type: string, conname: string, columns: list<string>}|null
     */
    private function pickConflictConstraint(string $fullName, string $campos): ?array
    {
        $camposSet = array_flip(array_map(trim(...), explode(',', $campos)));
        foreach ($this->getUniqueConstraints($fullName) as $constraint) {
            $allPresent = true;
            foreach ($constraint['columns'] as $column) {
                if (!isset($camposSet[$column])) {
                    $allPresent = false;
                    break;
                }
            }
            if ($allPresent) {
                return $constraint;
            }
        }

        return null;
    }

    /**
     * @param list<string> $pkColumns
     */
    private function buildUpdateSetFromCampos(string $campos, array $pkColumns): string
    {
        $pkSet = array_flip($pkColumns);
        $parts = [];
        foreach (explode(',', $campos) as $campo) {
            $campo = trim($campo);
            if (isset($pkSet[$campo])) {
                continue;
            }
            $parts[] = "$campo = EXCLUDED.$campo";
        }

        return implode(', ', $parts);
    }

    private function buildOnConflictClause(string $conflictTarget, string $update): string
    {
        if ($update === '') {
            return "ON CONFLICT $conflictTarget DO NOTHING";
        }

        return "ON CONFLICT $conflictTarget DO UPDATE SET $update";
    }

    private function buildDefaultUpsertSql(string $fullName, string $campos, string $fullNameDel): string
    {
        $constraint = $this->pickConflictConstraint($fullName, $campos);
        if ($constraint === null) {
            return "INSERT INTO $fullName ($campos) SELECT $campos FROM $fullNameDel";
        }

        $update = $this->buildUpdateSetFromCampos($campos, $constraint['columns']);
        $conflictTarget = '(' . implode(', ', $constraint['columns']) . ')';
        $onConflict = $this->buildOnConflictClause($conflictTarget, $update);

        return "INSERT INTO $fullName ($campos) SELECT $campos FROM $fullNameDel $onConflict";
    }

    /** @param list<string> $pkColumns */ private function buildUpsertSql(string $fullName, string $campos, string $fullNameDel, string $conflictTarget, array $pkColumns): string
    {
        $update = $this->buildUpdateSetFromCampos($campos, $pkColumns);
        $onConflict = $this->buildOnConflictClause($conflictTarget, $update);

        return "INSERT INTO $fullName ($campos) SELECT $campos FROM $fullNameDel $onConflict";
    }


    /**
     *
     * @param array $aDefaults
     *       ['tabla' => '.u_dir_cdc_dl', 'campos' => '...'],
     */
    /** @param list<array{tabla: string, campos: string}> $aInserts */ public function setInserts(array $aInserts): void
    {
        foreach ($aInserts as $cambio) {
            $tabla = $cambio['tabla'];
            $campos = $cambio['campos'];

            $full_name = "\"$this->schema\".$tabla";
            $full_name_del = "\"$this->schema_del\".$tabla";
            if ($this->existeTabla($full_name_del) && $this->existeTabla($full_name)) {
                $this->executeInsert($tabla, $campos);
            }
        }

    }

    private function executeInsert(string $tabla, string $campos): void
    {
        $full_name = "\"$this->schema\".$tabla";
        $full_name_del = "\"$this->schema_del\".$tabla";

        $sql = $this->buildDefaultUpsertSql($full_name, $campos, $full_name_del);
        switch ($tabla) {
            case "a_importadas":
                $sql = $this->buildUpsertSql(
                    $full_name,
                    $campos,
                    $full_name_del,
                    '(id_activ)',
                    ['id_activ'],
                );
                break;
            case "e_actas_dl":
                $sql = $this->buildUpsertSql(
                    $full_name,
                    $campos,
                    $full_name_del,
                    '(acta)',
                    ['acta'],
                );
                break;
            case "d_dossiers_abiertos":
                $sql = $this->buildUpsertSql(
                    $full_name,
                    $campos,
                    $full_name_del,
                    'ON CONSTRAINT d_dossiers_abiertos_pkey',
                    ['tabla', 'id_pau', 'id_tipo_dossier'],
                );
                break;
            case "p_agregados":
            case "p_de_paso_out":
            case "p_numerarios":
            case "p_supernumerarios":
            case "p_sssc":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS p ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT (id_nom) DO UPDATE
                        SET $update
                        WHERE p.situacion = 'A' AND p.f_situacion > EXCLUDED.f_situacion";
                break;
            case "d_asignaturas_activ_dl":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT ON CONSTRAINT d_asignaturas_activ_dl_pkey DO UPDATE
                        SET $update
                        WHERE a.tipo = 'p' AND EXCLUDED.tipo != 'p' ";
                break;
            case "d_asistentes_dl":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT ON CONSTRAINT d_asistentes_pkey DO UPDATE
                        SET $update
                        WHERE a.id_tabla = 'out' AND EXCLUDED.id_tabla = 'dl' ";
                break;
            case "d_asistentes_out":
                $sql = $this->buildUpsertSql(
                    $full_name,
                    $campos,
                    $full_name_del,
                    'ON CONSTRAINT d_asistentes_out_id_activ_id_nom_pk',
                    ['id_activ', 'id_nom'],
                );
                break;
            case "d_cargos_activ_dl":
                $a_campos = explode(',', $campos);
                $update = '';
                foreach ($a_campos as $campo) {
                    $campo = trim($campo);
                    $update .= empty($update) ? '' : ', ';
                    $update .= "$campo = EXCLUDED.$campo";
                }
                $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                        ON CONFLICT ON CONSTRAINT d_cargos_activ_dl_id_activ_id_cargo_pkey DO UPDATE
                        SET $update";
                break;
        }

        $this->prepareAndExecute($sql, 'DBAlterSchema.executeInsert.' . $tabla);
    }

    /**
     * Comprobar si existe la tabla, para evitar errores.
     *
     * @param string $full_name
     * @return boolean
     */
    public function existeTabla(string $full_name): bool
    {

        $oDbl = $this->getPdoDB();
        $sql = "SELECT to_regclass('$full_name'); ";

        $stmt = $oDbl->query($sql);
        if ($stmt === false) {
            return false;
        }
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as $row) {
            $valor = $row[0] ?? null;
            if (is_string($valor) && $valor !== '') {
                return true;
            }
        }
        return false;
    }

    /**
     *
     * @param array $aDefaults
     *       ['tabla' => 'a_actividad_proceso_sf', 'campo' => 'id_schema', 'valor' => "idschema('H-dlx'::text)"],
     */
    /** @param list<array{tabla: string, campo: string, valor: string}> $aDefaults */ public function setDefaults(array $aDefaults): void
    {
        foreach ($aDefaults as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $valor = $cambio['valor'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $this->setColumnDefault($full_name, $campo, $valor);
            }
        }

    }

    public function setColumnDefault(string $nom_tabla, string $nom_column, string $default): void
    {
        $oDbl = $this->getPdoDB();
        $sql = "ALTER TABLE $nom_tabla ALTER COLUMN $nom_column SET DEFAULT $default";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $this->handleSqlError($sClauError, $oDbl);
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $this->handleSqlError($sClauError, $oDblSt);
            }
        }
    }

    /**
     *
     * @param array $aDatos
     *   ['tabla' => 'u_centros_dl', 'campo' => 'id_zona'],
     */
    /** @param list<array{tabla: string, campo: string}> $aDatos */ public function setNullDatos(array $aDatos): void
    {
        $oDbl = $this->getPdoDB();
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $sql = "UPDATE $full_name SET $campo = null; ";

                if (($oDblSt = $oDbl->prepare($sql)) === false) {
                    $sClauError = 'DBAlterSchema.crearSchema.prepare';
                    $sClauError .= ' ' . $sql;

                    $this->handleSqlError($sClauError, $oDbl);
                    continue;
                }

                if ($oDblSt->execute() === false) {
                    $sClauError = 'DBAlterSchema.crearSchema.execute';

                    $this->handleSqlError($sClauError, $oDblSt);
                }
            }
        }
    }


    /**
     *
     * @param array $aDatos
     *      ['tabla' => 'da_plazas_dl', 'campo' => 'id_dl', 'old' => "$id_dl_old", 'new' => "$id_dl_new"],
     */
    /** @param list<array{tabla: string, campo: string, old: string, new: string}> $aDatos */ public function updateDatos(array $aDatos): void
    {
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $old = $cambio['old'];
            $new = $cambio['new'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $this->updateColumn($full_name, $campo, $old, $new);
            }
        }
    }

    public function updateColumn(string $full_name, string $campo, string $old, string $new): void
    {
        $oDbl = $this->getPdoDB();
        $sql = "UPDATE $full_name SET $campo = '$new' WHERE $campo = '$old' ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $this->handleSqlError($sClauError, $oDbl);
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $this->handleSqlError($sClauError, $oDblSt);
            }
        }
    }

    /**
     *
     * @param array $aDatos
     *     ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "$dl_old(.*)", 'replacement' => "$DlNew\1"]
     */
    /** @param list<array{tabla: string, campo: string, pattern: string, replacement: string}> $aDatos */ public function updateDatosRegexp(array $aDatos): void
    {
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $pattern = $cambio['pattern'];
            $replacement = $cambio['replacement'];

            $full_name = "\"$this->schema\".$tabla";
            if ($this->existeTabla($full_name)) {
                $this->updateColumnRegexp($full_name, $campo, $pattern, $replacement);
            }
        }
    }

    public function updateColumnRegexp(string $full_name, string $campo, string $pattern, string $replacement): void
    {
        $oDbl = $this->getPdoDB();
        $sql = "UPDATE $full_name SET $campo = regexp_replace($campo, '$pattern', '$replacement' ,'g') ";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $this->handleSqlError($sClauError, $oDbl);
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $this->handleSqlError($sClauError, $oDblSt);
            }
        }
    }

    public function updateCedidasAll(string $old, string $new): void
    {
        $oDbl = $this->getPdoDB();
        $sql = "UPDATE publicv.da_plazas set cedidas =  regexp_replace(cedidas::text, '\m$old\M', '$new', 'g')::jsonb where cedidas is not null;";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $this->handleSqlError($sClauError, $oDbl);
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $this->handleSqlError($sClauError, $oDblSt);
            }
        }
    }

    public function updatePropietarioAll(string $old, string $new): void
    {
        // conectar con DB sv-e:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('publicv-e'); //de la database comun
        $oConexion = new DBConnection($config);
        $oDbSve = $oConexion->getPDO();

        $sql = "UPDATE global.d_asistentes_dl set propietario = regexp_replace(propietario::text, '\m$old\M', '$new', 'g')::text where propietario is not null;";

        if (($oDblSt = $oDbSve->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;

            $this->handleSqlError($sClauError, $oDbSve);
            return;
        }

        if ($oDblSt->execute() === false) {
            $sClauError = 'DBAlterSchema.crearSchema.execute';

            $this->handleSqlError($sClauError, $oDblSt);
        }
        $sql = "UPDATE publicv.d_asistentes_de_paso set propietario = regexp_replace(propietario::text, '\m$old\M', '$new', 'g')::text where propietario is not null;";

        if (($oDblSt = $oDbSve->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;

            $this->handleSqlError($sClauError, $oDbSve);
            return;
        }

        if ($oDblSt->execute() === false) {
            $sClauError = 'DBAlterSchema.crearSchema.execute';

            $this->handleSqlError($sClauError, $oDblSt);
        }
    }

    /**
     *
     * @param array $aDatos
     *     ['tabla' => 'a_actividades_dl', 'campo' => 'dl_org', 'pattern' => "$dl_old(.*)", 'replacement' => "$DlNew\1"]
     */
    /** @param list<array{tabla: string, campo: string, pattern: string, replacement: string}> $aDatos */ public function updateDatosRegexpTodos(array $aDatos): void
    {
        foreach ($aDatos as $cambio) {
            $tabla = $cambio['tabla'];
            $campo = $cambio['campo'];
            $pattern = $cambio['pattern'];
            $replacement = $cambio['replacement'];

            if ($this->existeTabla($tabla)) {
                $this->updateColumnRegexp($tabla, $campo, $pattern, $replacement);
            }
        }
    }

    /**
     * Para el esquema_del,
     *  pasa las asistencias_out a asistencias_dl para el caso de asistencias a actividades que organiza la dl_new.
     *
     * @param string $dl_new (dl_org)
     */
    public function asistentesOut2Dl(string $dl_new): void
    {
        // conectar con DB comun:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new DBConnection($config);
        $oDbComun = $oConexion->getPDO();
        // Conexión actual (a sv-e).
        $oDbl = $this->getPdoDB();
        // buscar actividades de asistencias out que tengan dl_org = dl_new
        $full_asistentes_out = "\"$this->schema_del\".d_asistentes_out";
        $full_asistentes_dl = "\"$this->schema_del\".d_asistentes_dl";

        // sv-e
        $sQuery = "SELECT DISTINCT id_activ FROM $full_asistentes_out";

        $oDblSt = $oDbl->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'AlterSchemna.asistenteOut';
            $this->handleSqlError($sClauError, $oDbl);
            return;
        }
        $aId_activ = [];
        while (($aDades = $oDblSt->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (!is_array($aDades)) {
                continue;
            }
            $idActivRaw = $aDades['id_activ'] ?? null;
            if (is_int($idActivRaw)) {
                $id_activ = $idActivRaw;
            } elseif (is_string($idActivRaw) && is_numeric($idActivRaw)) {
                $id_activ = (int) $idActivRaw;
            } else {
                continue;
            }
            // buscar la dl_org
            $sql = "SELECT dl_org FROM a_actividades_all WHERE id_activ = $id_activ ";
            $stmtDlOrg = $oDbComun->query($sql);
            if ($stmtDlOrg === false) {
                continue;
            }
            $dl_org = $stmtDlOrg->fetchColumn();
            if ($dl_org == $dl_new) {
                $aId_activ[] = $id_activ;
            }
        }

        if (!empty($aId_activ)) {
            // mover las asistencias_out de $aId_activ a asistencias_dl
            $txt_ids = implode(',', array_map(static fn (int $id): string => (string) $id, $aId_activ));
            $condicion = "id_activ IN ($txt_ids)";
            $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
            $update = $this->buildUpdateSetFromCampos($campos, ['id_activ', 'id_nom']);

            $insert = "INSERT INTO $full_asistentes_dl ($campos) 
                        SELECT $campos FROM $full_asistentes_out WHERE $condicion
                        ON CONFLICT ON CONSTRAINT d_asistentes_pkey DO UPDATE SET $update
                    ";
            if (($oDblSt = $oDbl->query($insert)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';

                $this->handleSqlError($sClauError, $oDbl);
            }
            // las borro de d_asistencias_out
            $delete = "DELETE FROM $full_asistentes_out WHERE $condicion";
            if (($oDblSt = $oDbl->query($delete)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';

                $this->handleSqlError($sClauError, $oDbl);
            }
        }
    }

    /**
     * Para el esquema matriz, pasa las asistencias_out a asistencias_dl para el caso de asistencias
     *  a actividades que están en la dl (las que se han insertado de dl_old) y se borran del
     *  la tabla de a_importadas.
     *
     */
    public function asistentesOut2DlPropia(): void
    {
        // conectar con DB comun:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new DBConnection($config);
        $oDbComun = $oConexion->getPDO();
        // Conexión actual (a sv-e).
        $oDbl = $this->getPdoDB();
        // buscar actividades de asistencias out que esten en a_actividades_dl
        $full_asistentes_out = "\"$this->schema\".d_asistentes_out";
        $full_asistentes_dl = "\"$this->schema\".d_asistentes_dl";

        // sv-e
        $sQuery = "SELECT DISTINCT id_activ FROM $full_asistentes_out";

        $oDblSt = $oDbl->query($sQuery);
        if ($oDblSt === false) {
            $sClauError = 'AlterSchemna.asistenteOut';
            $this->handleSqlError($sClauError, $oDbl);
            return;
        }
        $esquema_comun = substr($this->schema, 0, -1); // quito la v o la f.
        $full_actividades_dl = "\"$esquema_comun\".a_actividades_dl";
        $aId_activ = [];
        while (($aDades = $oDblSt->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (!is_array($aDades)) {
                continue;
            }
            $idActivRaw = $aDades['id_activ'] ?? null;
            if (is_int($idActivRaw)) {
                $id_activ = $idActivRaw;
            } elseif (is_string($idActivRaw) && is_numeric($idActivRaw)) {
                $id_activ = (int) $idActivRaw;
            } else {
                continue;
            }
            // buscar si están en la dl
            $sql = "SELECT id_activ FROM $full_actividades_dl WHERE id_activ = $id_activ ";
            $stmtIdActiv = $oDbComun->query($sql);
            if ($stmtIdActiv === false) {
                continue;
            }
            $idActivDlRaw = $stmtIdActiv->fetchColumn();
            if (is_int($idActivDlRaw)) {
                $aId_activ[] = $idActivDlRaw;
            } elseif (is_string($idActivDlRaw) && is_numeric($idActivDlRaw)) {
                $aId_activ[] = (int) $idActivDlRaw;
            }
        }

        if (!empty($aId_activ)) {
            // mover las asistencias_out de $aId_activ a asistencias_dl
            $txt_ids = implode(',', array_map(static fn (int $id): string => (string) $id, $aId_activ));
            $condicion = "id_activ IN ($txt_ids)";
            $campos = 'id_activ, id_nom, propio, est_ok, cfi, cfi_con, falta, encargo, dl_responsable, observ, id_tabla, plaza, propietario, observ_est';
            $update = $this->buildUpdateSetFromCampos($campos, ['id_activ', 'id_nom']);

            $insert = "INSERT INTO $full_asistentes_dl ($campos) SELECT $campos FROM $full_asistentes_out WHERE $condicion
                        ON CONFLICT ON CONSTRAINT d_asistentes_pkey DO UPDATE SET $update";
            if (($oDblSt = $oDbl->query($insert)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';

                $this->handleSqlError($sClauError, $oDbl);
            }
            // borrar de asistentes_out
            $delete = "DELETE FROM $full_asistentes_dl WHERE $condicion";
            if (($oDblSt = $oDbl->query($delete)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';

                $this->handleSqlError($sClauError, $oDbl);
            }
            // borrar de a_importadas
            $full_importadas = "\"$esquema_comun\".a_importadas";
            $delete = "DELETE FROM $full_importadas WHERE $condicion";
            if (($oDblSt = $oDbComun->query($delete)) === false) {
                $sClauError = 'AlterSchemna.asistenteOut';

                $this->handleSqlError($sClauError, $oDbComun);
            }
        }
    }

    /**
     * Ya se han insertado todas las importadas de dl_del en dl_matriz
     * Es para borrar si queda alguna de la propia dl.
     *
     */
    public function comprobarImportadas(): void
    {
        // conectar con DB comun:
        $oConfigDB = new ConfigDB('importar'); //de la database comun
        $config = $oConfigDB->getEsquema('public'); //de la database comun

        $oConexion = new DBConnection($config);
        $oDbComun = $oConexion->getPDO();

        $esquema_comun = substr($this->schema, 0, -1); // quito la v o la f.
        $full_actividades_dl = "\"$esquema_comun\".a_actividades_dl";
        $full_actividades_importadas = "\"$esquema_comun\".a_importadas";

        $sql = "DELETE FROM $full_actividades_importadas WHERE id_activ IN (SELECT id_activ FROM $full_actividades_dl ) ";
        $oDbComun->query($sql);
    }


    /**
     * Es necesario una función extra para intentar añadir los cargos duplicados
     * por ejemplo los sacd: puede haber dos sacd en la actividad, uno en cada dl con
     * id_cargo=35. Se añade el segundo con id_cargo=36.
     */
    public function insertarCargos(): void
    {
        $oDbl = $this->getPdoDB();
        $tabla = 'd_cargos_activ_dl';
        $campos = 'id_activ, id_cargo, id_nom, puede_agd, observ';

        $full_name = "\"$this->schema\".$tabla";
        $full_name_del = "\"$this->schema_del\".$tabla";

        // buscar cargos repetidos:
        $sql = "SELECT d.id_activ, d.id_cargo, m.id_nom as id_nom_matriz, d.id_nom as id_nom_del
                FROM $full_name m JOIN $full_name_del d ON (m.id_activ=d.id_activ AND m.id_cargo=d.id_cargo)";
        if ($oDbl->query($sql) === false) {
            $sClauError = 'DBAlterSchema.insertarCargos.select';

            $this->handleSqlError($sClauError, $oDbl);
        }
        // tipos de cargo:
        $CargoRepository = $this->cargoRepository;
        if ($CargoRepository === null) {
            $this->errores[] = 'DBAlterSchema.insertarCargos: CargoRepository no configurado.';

            return;
        }
        $stmtCargos = $oDbl->query($sql);
        if ($stmtCargos === false) {
            return;
        }
        while (($aDades = $stmtCargos->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (!is_array($aDades)) {
                continue;
            }
            $idActivRaw = $aDades['id_activ'] ?? null;
            $idCargoRaw = $aDades['id_cargo'] ?? null;
            $idNomMatrizRaw = $aDades['id_nom_matriz'] ?? null;
            $idNomDelRaw = $aDades['id_nom_del'] ?? null;
            if (
                !((is_int($idActivRaw) || (is_string($idActivRaw) && is_numeric($idActivRaw)))
                    && (is_int($idCargoRaw) || (is_string($idCargoRaw) && is_numeric($idCargoRaw)))
                    && (is_int($idNomMatrizRaw) || (is_string($idNomMatrizRaw) && is_numeric($idNomMatrizRaw)))
                    && (is_int($idNomDelRaw) || (is_string($idNomDelRaw) && is_numeric($idNomDelRaw))))
            ) {
                continue;
            }
            $id_activ = (int) $idActivRaw;
            $id_cargo = (int) $idCargoRaw;
            // comprobar que no sea el mismo id_nom...
            $id_nom_matriz = (int) $idNomMatrizRaw;
            $id_nom_del = (int) $idNomDelRaw;
            if ($id_nom_matriz == $id_nom_del) {
                continue;
            }
            $oCargo = $CargoRepository->findById($id_cargo);
            if ($oCargo === null) {
                continue;
            }
            $tipo_cargo = $oCargo->getTipoCargoVo()?->value() ?? '';
            if ($tipo_cargo === '') {
                continue;
            }
            $cargos_de_tipo = $CargoRepository->getArrayCargos($tipo_cargo);
            $txt_ids = implode(',', array_keys($cargos_de_tipo));
            $condicion_cargo = " AND id_cargo IN ($txt_ids)";
            // buscar el id_cargo mayor (del mismo tipo) para la actividad:
            $sql1 = "SELECT id_cargo FROM $full_name WHERE id_activ = $id_activ $condicion_cargo ";
            $sql2 = "SELECT id_cargo FROM $full_name WHERE id_activ = $id_activ $condicion_cargo ";
            $sql = "$sql1 UNION $sql2 ORDER BY 1 DESC";
            $stmtMax = $oDbl->query($sql);
            if ($stmtMax === false) {
                continue;
            }
            $idCargoMaxRaw = $stmtMax->fetchColumn();
            if (is_int($idCargoMaxRaw)) {
                $id_cargo_max = $idCargoMaxRaw;
            } elseif (is_string($idCargoMaxRaw) && is_numeric($idCargoMaxRaw)) {
                $id_cargo_max = (int) $idCargoMaxRaw;
            } else {
                $id_cargo_max = 0;
            }
            $id_cargo_max++;
            // compruebo que está en el rango del tipo cargo, sino lo desprecio.
            if (!empty($cargos_de_tipo[$id_cargo_max])) {
                $updateSql = "UPDATE $full_name_del SET id_cargo = $id_cargo_max WHERE id_activ= $id_activ AND id_cargo = $id_cargo";
                $oDbl->query($updateSql);
            }
        }

        // Ahora si insertar:
        $a_campos = explode(',', $campos);
        $update = '';
        foreach ($a_campos as $campo) {
            $campo = trim($campo);
            $update .= empty($update) ? '' : ', ';
            $update .= "$campo = EXCLUDED.$campo";
        }
        $sql = "INSERT INTO $full_name AS a ($campos) SELECT $campos FROM $full_name_del 
                    ON CONFLICT ON CONSTRAINT d_cargos_activ_dl_id_activ_id_cargo_pkey DO UPDATE SET $update";

        if (($oDblSt = $oDbl->prepare($sql)) === false) {
            $sClauError = 'DBAlterSchema.crearSchema.prepare';
            $sClauError .= ' ' . $sql;
            $this->handleSqlError($sClauError, $oDbl);
        } else {
            if ($oDblSt->execute() === false) {
                $sClauError = 'DBAlterSchema.crearSchema.execute';
                $this->handleSqlError($sClauError, $oDblSt);
            }
        }
    }

    /**
     * Quita las herencias de todas para el esquema $esquema de la conexion (DB)
     *
     * @param \PDO $conexionDB
     * @param string $esquema
     * @return boolean
     */
    public function quitarHerencias(\PDO $conexionDB, string $esquema): bool
    {

        $sql = "SELECT c.relname AS child, p.relname AS parent, n1.nspname as schema_parent, n.nspname as schema_child 
                FROM pg_inherits JOIN pg_class AS c ON (inhrelid=c.oid) 
                    JOIN pg_class as p ON (inhparent=p.oid), pg_namespace n, pg_namespace n1 
                WHERE c.relnamespace = n.oid AND n.nspname='$esquema' AND p.relnamespace = n1.oid;";

        $stmt = $conexionDB->query($sql);
        if ($stmt === false) {
            $sClauError = 'AlterSchemna.asistenteOut';

            $this->handleSqlError($sClauError, $conexionDB);
            return false;
        }
        while (($aDades = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            if (!is_array($aDades)) {
                continue;
            }
            $child = $aDades['child'] ?? null;
            $schema_child = $aDades['schema_child'] ?? null;
            $parent = $aDades['parent'] ?? null;
            $schema_parent = $aDades['schema_parent'] ?? null;
            if (!is_string($child) || !is_string($schema_child) || !is_string($parent) || !is_string($schema_parent)) {
                continue;
            }

            $full_child = "\"$schema_child\".$child";
            $full_parent = "\"$schema_parent\".$parent";

            $sQuery = "ALTER TABLE $full_child NO INHERIT $full_parent ";
            $conexionDB->query($sQuery);
        }
        return true;
    }


}