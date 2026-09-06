<?php

declare(strict_types=1);

namespace src\zonassacd\application;

use PDO;
use RuntimeException;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\copias\ContextoCopia;
use Throwable;

/**
 * Traslada el módulo zonassacd de sv-e a comun y llena `zonas_ctr`.
 *
 * Solo toca esquemas que ya tienen el módulo instalado (tabla `zonas` en sv-e).
 * No borra nada en sv-e.
 */
final class ZonasAComun
{
    private const ID_MOD = 24;

    /**
     * @return array{
     *     aplicado: bool,
     *     lineas: list<string>,
     *     esquemas: int,
     *     errores: int
     * }
     */
    public function execute(bool $aplicar = false, string $soloEsquema = ''): array
    {
        $pdoSve = $this->conectar('publicv-e');
        $pdoSv = $this->conectar('publicv');
        $pdoComun = $this->conectar('public');
        $pdoSelect = $this->conectarOpcional('public_select');
        $pdoSf = $this->conectarOpcional('publicf');

        if ($aplicar) {
            $this->crearPadresComun($pdoComun);
            if ($pdoSelect instanceof PDO) {
                $this->crearPadresComun($pdoSelect);
            }
        }

        $lineas = [];
        $errores = 0;
        $hechos = 0;
        foreach ($this->esquemasConModulo($pdoSve, $soloEsquema) as $esquemaSve) {
            $esquemaComun = self::esquemaComunDeSve($esquemaSve);
            try {
                $informe = $this->procesarEsquema(
                    $pdoSve,
                    $pdoSv,
                    $pdoComun,
                    $pdoSelect,
                    $pdoSf,
                    $esquemaSve,
                    $esquemaComun,
                    $aplicar,
                );
                $lineas[] = $informe;
                $hechos++;
            } catch (Throwable $e) {
                $errores++;
                $lineas[] = sprintf('  %-16s ERROR: %s', $esquemaComun, $e->getMessage());
            }
        }

        if ($soloEsquema !== '' && $hechos === 0 && $errores === 0) {
            throw new RuntimeException(sprintf(
                'El esquema "%s" no tiene el módulo zonassacd instalado en sv-e',
                $soloEsquema,
            ));
        }

        return [
            'aplicado' => $aplicar,
            'lineas' => $lineas,
            'esquemas' => $hechos,
            'errores' => $errores,
        ];
    }

    public static function esquemaComunDeSve(string $esquemaSve): string
    {
        return str_ends_with($esquemaSve, 'v') ? substr($esquemaSve, 0, -1) : $esquemaSve;
    }

    /**
     * @return list<string>
     */
    private function esquemasConModulo(PDO $pdoSve, string $soloEsquema): array
    {
        $sql = "SELECT n.nspname
                FROM pg_namespace n
                JOIN pg_class c ON c.relnamespace = n.oid AND c.relkind = 'r'
                WHERE c.relname = 'zonas'
                  AND n.nspname <> 'global'
                  AND n.nspname NOT LIKE 'pg_%'
                  AND right(n.nspname, 1) = 'v'
                ORDER BY n.nspname";
        $stmt = $pdoSve->query($sql);
        if ($stmt === false) {
            throw new RuntimeException('No se pudo listar esquemas con zonas en sv-e');
        }

        $esquemas = [];
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $nombre) {
            if (!is_string($nombre) || !self::esquemaValido($nombre)) {
                continue;
            }
            $comun = self::esquemaComunDeSve($nombre);
            if ($soloEsquema !== '' && $comun !== $soloEsquema && $nombre !== $soloEsquema) {
                continue;
            }
            $esquemas[] = $nombre;
        }

        return $esquemas;
    }

    private function procesarEsquema(
        PDO $pdoSve,
        PDO $pdoSv,
        PDO $pdoComun,
        ?PDO $pdoSelect,
        ?PDO $pdoSf,
        string $esquemaSve,
        string $esquemaComun,
        bool $aplicar,
    ): string {
        $qSve = ContextoCopia::comillas($esquemaSve);
        $qComun = ContextoCopia::comillas($esquemaComun);
        $qSv = ContextoCopia::comillas($esquemaSve);

        $modInstalado = $this->moduloMarcado($pdoSve, $qSve);
        $origen = [
            'zonas' => $this->contar($pdoSve, "$qSve.zonas"),
            'zonas_grupos' => $this->contar($pdoSve, "$qSve.zonas_grupos"),
            'zonas_sacd' => $this->contar($pdoSve, "$qSve.zonas_sacd"),
        ];
        $centrosSv = $this->contar($pdoSv, "$qSv.u_centros_dl", 'id_zona IS NOT NULL');
        $centrosSf = $this->contar($pdoComun, "$qComun.cu_centros_dlf", 'id_zona IS NOT NULL');
        $destino = [
            'zonas' => $this->contarSiExiste($pdoComun, $esquemaComun, 'zonas'),
            'zonas_grupos' => $this->contarSiExiste($pdoComun, $esquemaComun, 'zonas_grupos'),
            'zonas_sacd' => $this->contarSiExiste($pdoComun, $esquemaComun, 'zonas_sacd'),
            'zonas_ctr' => $this->contarSiExiste($pdoComun, $esquemaComun, 'zonas_ctr'),
        ];

        $avisos = [];
        if (!$modInstalado) {
            $avisos[] = 'm0_mods_installed_dl no marca el módulo 24';
        }
        $filasSf = $this->filasZonasEnSf($pdoSf, $esquemaComun);
        if ($filasSf > 0) {
            $avisos[] = sprintf('sf tiene %d filas en zonas (colisión posible)', $filasSf);
        }

        $linea = sprintf(
            '  %-16s sve zonas=%d grupos=%d sacd=%d ctr(sv=%d sf=%d) comun zonas=%d grupos=%d sacd=%d ctr=%d%s',
            $esquemaComun,
            $origen['zonas'],
            $origen['zonas_grupos'],
            $origen['zonas_sacd'],
            $centrosSv,
            $centrosSf,
            $destino['zonas'],
            $destino['zonas_grupos'],
            $destino['zonas_sacd'],
            $destino['zonas_ctr'],
            $avisos === [] ? '' : '  ! ' . implode('; ', $avisos),
        );

        if (!$aplicar) {
            return $linea;
        }

        $this->crearHijas($pdoComun, $esquemaComun, true);
        if ($pdoSelect instanceof PDO) {
            $this->crearHijas($pdoSelect, $esquemaComun, false);
        }

        $idSchema = $this->idSchemaComun($pdoComun, $esquemaComun);
        $this->copiarTabla($pdoSve, $pdoComun, "$qSve.zonas_grupos", "$qComun.zonas_grupos", [
            'id_grupo', 'nombre_grupo', 'orden',
        ], 'id_grupo', $idSchema);
        $this->copiarTabla($pdoSve, $pdoComun, "$qSve.zonas", "$qComun.zonas", [
            'id_zona', 'nombre_zona', 'orden', 'id_grupo', 'id_nom',
        ], 'id_zona', $idSchema);
        $this->copiarTabla($pdoSve, $pdoComun, "$qSve.zonas_sacd", "$qComun.zonas_sacd", [
            'id_item', 'id_nom', 'id_zona', 'propia', 'dw1', 'dw2', 'dw3', 'dw4', 'dw5', 'dw6', 'dw7',
        ], 'id_item', $idSchema);

        $this->llenarZonasCtr($pdoSv, $pdoComun, $qSv, $qComun, $idSchema);

        return $linea . '  → copiado';
    }

    private function crearPadresComun(PDO $pdo): void
    {
        $sqls = [
            'CREATE TABLE IF NOT EXISTS global.zonas (
                id_schema integer NOT NULL,
                id_zona integer NOT NULL,
                nombre_zona text NOT NULL,
                orden smallint,
                id_grupo integer,
                id_nom integer
            )',
            'CREATE TABLE IF NOT EXISTS global.zonas_grupos (
                id_schema integer NOT NULL,
                id_grupo integer NOT NULL,
                nombre_grupo text,
                orden smallint
            )',
            'CREATE TABLE IF NOT EXISTS global.zonas_sacd (
                id_schema integer NOT NULL,
                id_item integer NOT NULL,
                id_nom integer NOT NULL,
                id_zona smallint NOT NULL,
                propia boolean DEFAULT true NOT NULL,
                dw1 bool DEFAULT true,
                dw2 bool DEFAULT true,
                dw3 bool DEFAULT true,
                dw4 bool DEFAULT true,
                dw5 bool DEFAULT true,
                dw6 bool DEFAULT true,
                dw7 bool DEFAULT true
            )',
            'CREATE TABLE IF NOT EXISTS global.zonas_ctr (
                id_schema integer NOT NULL,
                id_ubi integer NOT NULL,
                id_zona integer NOT NULL
            )',
        ];
        foreach ($sqls as $sql) {
            $pdo->exec($sql);
        }
    }

    private function crearHijas(PDO $pdo, string $esquema, bool $conFk): void
    {
        $q = ContextoCopia::comillas($esquema);
        $esquemaSql = str_replace("'", "''", $esquema);
        $role = $q;

        $this->execAll($pdo, [
            "CREATE TABLE IF NOT EXISTS $q.zonas (
                CONSTRAINT zonas_pkey PRIMARY KEY (id_zona)
            ) INHERITS (global.zonas)",
            "ALTER TABLE $q.zonas ALTER id_schema SET DEFAULT public.idschema('$esquemaSql'::text)",
            "CREATE SEQUENCE IF NOT EXISTS $q.zonas_id_zona_seq",
            "ALTER TABLE $q.zonas ALTER id_zona SET DEFAULT nextval('$q.zonas_id_zona_seq'::regclass)",
            "ALTER TABLE $q.zonas OWNER TO $role",
            "ALTER SEQUENCE $q.zonas_id_zona_seq OWNER TO $role",

            "CREATE TABLE IF NOT EXISTS $q.zonas_grupos (
                CONSTRAINT zonas_grupos_pkey PRIMARY KEY (id_grupo)
            ) INHERITS (global.zonas_grupos)",
            "ALTER TABLE $q.zonas_grupos ALTER id_schema SET DEFAULT public.idschema('$esquemaSql'::text)",
            "CREATE SEQUENCE IF NOT EXISTS $q.zonas_grupos_id_grupo_seq",
            "ALTER TABLE $q.zonas_grupos ALTER id_grupo SET DEFAULT nextval('$q.zonas_grupos_id_grupo_seq'::regclass)",
            "ALTER TABLE $q.zonas_grupos OWNER TO $role",
            "ALTER SEQUENCE $q.zonas_grupos_id_grupo_seq OWNER TO $role",

            "CREATE TABLE IF NOT EXISTS $q.zonas_sacd (
                CONSTRAINT zonas_sacd_pkey PRIMARY KEY (id_item)"
                . ($conFk ? ",
                CONSTRAINT zonas_sacd_id_nom_key UNIQUE (id_nom, id_zona),
                CONSTRAINT zonas_sacd_id_zona_fkey FOREIGN KEY (id_zona)
                    REFERENCES $q.zonas(id_zona) ON DELETE CASCADE" : '') . "
            ) INHERITS (global.zonas_sacd)",
            "ALTER TABLE $q.zonas_sacd ALTER id_schema SET DEFAULT public.idschema('$esquemaSql'::text)",
            "CREATE SEQUENCE IF NOT EXISTS $q.zonas_sacd_id_item_seq",
            "ALTER TABLE $q.zonas_sacd ALTER id_item SET DEFAULT nextval('$q.zonas_sacd_id_item_seq'::regclass)",
            "ALTER TABLE $q.zonas_sacd OWNER TO $role",
            "ALTER SEQUENCE $q.zonas_sacd_id_item_seq OWNER TO $role",

            "CREATE TABLE IF NOT EXISTS $q.zonas_ctr (
                CONSTRAINT zonas_ctr_pkey PRIMARY KEY (id_ubi)"
                . ($conFk ? ",
                CONSTRAINT zonas_ctr_id_zona_fkey FOREIGN KEY (id_zona)
                    REFERENCES $q.zonas(id_zona) ON DELETE CASCADE" : '') . "
            ) INHERITS (global.zonas_ctr)",
            "ALTER TABLE $q.zonas_ctr ALTER id_schema SET DEFAULT public.idschema('$esquemaSql'::text)",
            "CREATE INDEX IF NOT EXISTS zonas_ctr_id_zona_idx ON $q.zonas_ctr (id_zona)",
            "ALTER TABLE $q.zonas_ctr OWNER TO $role",
        ]);
    }

    /**
     * @param list<string> $columnas
     */
    private function copiarTabla(
        PDO $origen,
        PDO $destino,
        string $tablaOrigen,
        string $tablaDestino,
        array $columnas,
        string $pk,
        int $idSchema,
    ): void {
        $columnas = $this->columnasComunes($origen, $tablaOrigen, $destino, $tablaDestino, $columnas);
        if ($columnas === []) {
            throw new RuntimeException("No hay columnas comunes para copiar $tablaOrigen → $tablaDestino");
        }
        $tiposDestino = $this->tiposDeColumnas(
            $destino,
            $this->esquemaDeTablaCualificada($tablaDestino),
            $this->nombreDeTablaCualificada($tablaDestino),
        );
        $cols = implode(', ', $columnas);
        $stmt = $origen->query("SELECT $cols FROM $tablaOrigen");
        if ($stmt === false) {
            throw new RuntimeException("No se pudo leer $tablaOrigen");
        }

        $placeholders = implode(', ', array_map(static fn (string $c): string => ':' . $c, $columnas));
        $insert = $destino->prepare(
            "INSERT INTO $tablaDestino (id_schema, $cols) VALUES (:id_schema, $placeholders)
             ON CONFLICT ($pk) DO NOTHING"
        );
        if ($insert === false) {
            throw new RuntimeException("No se pudo preparar INSERT en $tablaDestino");
        }

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if (!is_array($fila)) {
                continue;
            }
            $params = ['id_schema' => $idSchema];
            foreach ($columnas as $col) {
                $params[$col] = $this->valorParaCopia($fila[$col] ?? null, $tiposDestino[$col] ?? '');
            }
            $insert->execute($params);
        }

        $seq = $tablaDestino . '_' . $pk . '_seq';
        $destino->exec("SELECT SETVAL('$seq'::regclass, COALESCE((SELECT MAX($pk) FROM $tablaDestino), 1), true)");
    }

    private function llenarZonasCtr(PDO $pdoSv, PDO $pdoComun, string $qSv, string $qComun, int $idSchema): void
    {
        $insert = $pdoComun->prepare(
            "INSERT INTO $qComun.zonas_ctr (id_schema, id_ubi, id_zona)
             VALUES (:id_schema, :id_ubi, :id_zona)
             ON CONFLICT (id_ubi) DO UPDATE SET id_zona = EXCLUDED.id_zona"
        );
        if ($insert === false) {
            throw new RuntimeException('No se pudo preparar INSERT en zonas_ctr');
        }

        $this->volcarIdZona($pdoSv, "$qSv.u_centros_dl", $insert, $idSchema);
        $this->volcarIdZona($pdoComun, "$qComun.cu_centros_dlf", $insert, $idSchema);
    }

    /**
     * @param PDO $pdo
     * @param non-empty-string $tabla
     * @param \PDOStatement $insert
     */
    private function volcarIdZona(PDO $pdo, string $tabla, \PDOStatement $insert, int $idSchema): void
    {
        $esquema = $this->esquemaDeTablaCualificada($tabla);
        $nombre = $this->nombreDeTablaCualificada($tabla);
        if (!$this->tablaExiste($pdo, $esquema, $nombre)) {
            return;
        }
        $stmt = $pdo->query("SELECT id_ubi, id_zona FROM $tabla WHERE id_zona IS NOT NULL");
        if ($stmt === false) {
            return;
        }
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if (!is_array($fila) || !isset($fila['id_ubi'], $fila['id_zona'])) {
                continue;
            }
            $insert->execute([
                'id_schema' => $idSchema,
                'id_ubi' => (int) $fila['id_ubi'],
                'id_zona' => (int) $fila['id_zona'],
            ]);
        }
    }

    private function esquemaDeTablaCualificada(string $tabla): string
    {
        $partes = explode('.', $tabla, 2);

        return trim($partes[0], '"');
    }

    private function nombreDeTablaCualificada(string $tabla): string
    {
        $partes = explode('.', $tabla, 2);

        return isset($partes[1]) ? trim($partes[1], '"') : trim($partes[0], '"');
    }

    private function moduloMarcado(PDO $pdoSve, string $qSve): bool
    {
        $esquema = trim($qSve, '"');
        try {
            if (!$this->tablaExiste($pdoSve, $esquema, 'm0_mods_installed_dl')) {
                return false;
            }
            $sql = "SELECT 1 FROM $qSve.m0_mods_installed_dl WHERE id_mod = " . self::ID_MOD;
            if ($this->columnaExiste($pdoSve, $esquema, 'm0_mods_installed_dl', 'active')) {
                $sql .= " AND active = 't'";
            }
            $sql .= ' LIMIT 1';
            $stmt = $pdoSve->query($sql);

            return $stmt !== false && $stmt->fetchColumn() !== false;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param list<string> $deseadas
     * @return list<string>
     */
    private function columnasComunes(
        PDO $origen,
        string $tablaOrigen,
        PDO $destino,
        string $tablaDestino,
        array $deseadas,
    ): array {
        $enOrigen = $this->nombresDeColumnas(
            $origen,
            $this->esquemaDeTablaCualificada($tablaOrigen),
            $this->nombreDeTablaCualificada($tablaOrigen),
        );
        $enDestino = $this->nombresDeColumnas(
            $destino,
            $this->esquemaDeTablaCualificada($tablaDestino),
            $this->nombreDeTablaCualificada($tablaDestino),
        );

        return array_values(array_filter(
            $deseadas,
            static fn (string $col): bool => isset($enOrigen[$col], $enDestino[$col]),
        ));
    }

    /**
     * @return array<string, true>
     */
    private function nombresDeColumnas(PDO $pdo, string $esquema, string $tabla): array
    {
        return array_fill_keys(array_keys($this->tiposDeColumnas($pdo, $esquema, $tabla)), true);
    }

    /**
     * @return array<string, string>
     */
    private function tiposDeColumnas(PDO $pdo, string $esquema, string $tabla): array
    {
        $stmt = $pdo->prepare(
            'SELECT a.attname, t.typname
             FROM pg_catalog.pg_attribute a
             JOIN pg_catalog.pg_class c ON c.oid = a.attrelid
             JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
             JOIN pg_catalog.pg_type t ON t.oid = a.atttypid
             WHERE n.nspname = ? AND c.relname = ? AND a.attnum > 0 AND NOT a.attisdropped'
        );
        if ($stmt === false) {
            return [];
        }
        $stmt->execute([$esquema, $tabla]);
        $tipos = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            if (!is_array($fila) || !isset($fila['attname'], $fila['typname'])) {
                continue;
            }
            $tipos[(string) $fila['attname']] = (string) $fila['typname'];
        }

        return $tipos;
    }

    private function valorParaCopia(mixed $valor, string $tipo): mixed
    {
        $vacio = $valor === null || $valor === '' || $valor === [];
        if (in_array($tipo, ['bool', 'boolean'], true) || is_bool($valor)) {
            if ($vacio) {
                return 't';
            }
            if (is_bool($valor)) {
                return $valor ? 't' : 'f';
            }
            $s = strtolower(trim((string) $valor));
            if (in_array($s, ['t', 'true', '1', 'on'], true)) {
                return 't';
            }
            if (in_array($s, ['f', 'false', '0', 'off'], true)) {
                return 'f';
            }

            return 't';
        }
        if ($vacio && in_array($tipo, ['int2', 'int4', 'int8', 'smallint', 'integer', 'bigint'], true)) {
            return null;
        }

        return $valor;
    }

    private function columnaExiste(PDO $pdo, string $esquema, string $tabla, string $columna): bool
    {
        return isset($this->nombresDeColumnas($pdo, $esquema, $tabla)[$columna]);
    }

    private function filasZonasEnSf(?PDO $pdoSf, string $esquemaComun): int
    {
        if (!$pdoSf instanceof PDO) {
            return 0;
        }
        $esquemaSf = $esquemaComun . 'f';
        if (!self::esquemaValido($esquemaSf) || !$this->tablaExiste($pdoSf, $esquemaSf, 'zonas')) {
            return 0;
        }

        return $this->contar($pdoSf, ContextoCopia::comillas($esquemaSf) . '.zonas');
    }

    private function idSchemaComun(PDO $pdoComun, string $esquema): int
    {
        $stmt = $pdoComun->prepare('SELECT public.idschema(?)');
        if ($stmt === false) {
            throw new RuntimeException('No se pudo preparar idschema');
        }
        $stmt->execute([$esquema]);
        $id = $stmt->fetchColumn();
        if (!is_numeric($id)) {
            throw new RuntimeException(sprintf('idschema(%s) no devolvió un id', $esquema));
        }

        return (int) $id;
    }

    private function contar(PDO $pdo, string $tabla, string $where = ''): int
    {
        $sql = 'SELECT count(*) FROM ' . $tabla;
        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }
        $stmt = $pdo->query($sql);
        if ($stmt === false) {
            return 0;
        }
        $n = $stmt->fetchColumn();

        return is_numeric($n) ? (int) $n : 0;
    }

    private function contarSiExiste(PDO $pdo, string $esquema, string $tabla): int
    {
        if (!$this->tablaExiste($pdo, $esquema, $tabla)) {
            return 0;
        }

        return $this->contar($pdo, ContextoCopia::comillas($esquema) . '.' . $tabla);
    }

    private function tablaExiste(PDO $pdo, string $esquema, string $tabla): bool
    {
        $stmt = $pdo->prepare(
            'SELECT 1 FROM pg_catalog.pg_class c
             JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace
             WHERE n.nspname = ? AND c.relname = ? AND c.relkind = \'r\' LIMIT 1'
        );
        if ($stmt === false) {
            return false;
        }
        $stmt->execute([$esquema, $tabla]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * @param list<string> $sqls
     */
    private function execAll(PDO $pdo, array $sqls): void
    {
        foreach ($sqls as $sql) {
            $pdo->exec($sql);
        }
    }

    private function conectar(string $clave): PDO
    {
        $oConfigDB = new ConfigDB('importar');
        // getEsquema exige un bloque `default` en importar.inc; en este cluster
        // el host/puerto vive en comun.conn.inc / sv.conn.inc. La conexión de
        // mantenimiento toma esas plantillas public* y, si hace falta, el default prestado.
        $config = $clave === 'public_select' || $clave === 'publicv-e_select'
            ? $oConfigDB->getConexionImportarReplica($clave)
            : $oConfigDB->getConexionMantenimiento($clave);
        $pdo = (new DBConnection($config))->getPDO();
        $pdo->exec('SET search_path TO public');

        return $pdo;
    }

    private function conectarOpcional(string $clave): ?PDO
    {
        try {
            return $this->conectar($clave);
        } catch (Throwable) {
            return null;
        }
    }

    private static function esquemaValido(string $esquema): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_-]+$/', $esquema);
    }
}
