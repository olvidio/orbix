<?php

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;

class DBEsquemaCreate
{
    /**
     * Esquema de Referencia de Esquema
     *
     * @var string
     */
    private $sRef;
    /**
     * Esquema a Crear de Esquema
     *
     * @var string
     */
    private $sNew;
    /**
     * Directorio donde poner los logs de Esquema
     *
     * @var string
     */
    private $sdir;
    /**
     * Fichero con el volcado del esquema de referencia de Esquema
     *
     * @var string
     */
    private string $sfileRef = '';
    /**
     * Fichero con el volcado del nuevo esquema de Esquema
     *
     * @var string
     */
    private string $sfileNew = '';
    /**
     * Fichero con el log de la accion de Esquema
     *
     * @var string
     */
    private string $sfileLog = '';
    /**
     * Fichero con la lista de secuencias a actualizar del Esquema
     *
     * @var string
     */
    private string $sfileSeq = '';

    private $sDb;
    private $user;
    private $password;

    private $dbname;
    private $Host;
    private $ssh_user;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private ?string $sRegionRef = null;
    private ?string $sDlRef = null;
    private ?string $sDlNew = null;
    private ?string $sRegionNew = null;
    private mixed $sDbRef = null;
    /** @var array<string, mixed> */
    protected array $config = [];

    /**
     * Constructor de la classe.
     *
     */
    function __construct()
    {

    }

    /**
     * Destructor de la classe.
     *
     * Elimina todos los ficheros creados
     *
     */
    function __destruct()
    {
        if (ConfigGlobal::is_debug_mode()) {
            return;
        }
        foreach ([$this->sfileNew, $this->sfileRef, $this->sfileLog, $this->sfileSeq] as $path) {
            if (is_string($path) && $path !== '') {
                $this->deleteFile($path);
            }
        }
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setConfig($config)
    {
        $this->config = $config;

        $this->setDb($config['dbname']);
        $this->setHost($config['host']);
        $this->setSsh_user($config['ssh_user']);
    }

    public function getDir()
    {
        $this->sdir = empty($this->sdir) ? ConfigGlobal::$directorio . '/log/db' : $this->sdir;
        return $this->sdir;
    }

    public function setDir($dir)
    {
        $this->sdir = $dir;
    }

    public function getRegionRef()
    {
        return $this->sRegionRef;
    }

    public function setRegionRef($region)
    {
        $this->sRegionRef = $region;
    }

    public function getDlRef()
    {
        return $this->sDlRef;
    }

    public function setDlRef($dl)
    {
        $this->sDlRef = $dl;
    }

    public function getRegionNew()
    {
        return $this->sRegionNew;
    }

    public function setRegionNew($region)
    {
        $this->sRegionNew = $region;
    }

    public function getDlNew()
    {
        return $this->sDlNew;
    }

    public function setDlNew($dl)
    {
        $this->sDlNew = $dl;
    }

    public function getNew()
    {
        if ($this->sRegionNew === null || $this->sDlNew === null
            || $this->sRegionNew === '' || $this->sDlNew === '') {
            throw new \RuntimeException(_('Faltan región y dl del esquema (setRegionNew / setDlNew).'));
        }

        $this->sNew = $this->sRegionNew . '-' . $this->sDlNew;
        switch ($this->getDb()) {
            case 'comun':
            case 'comun_select':
            case 'pruebas-comun':
                break;
            case 'sv':
            case 'sv-e':
            case 'sv-e_select':
            case 'pruebas-sv':
            case 'pruebas-sv-e':
                $this->sNew .= 'v';
                break;
            case 'sf':
            case 'sf-e':
            case 'pruebas-sf':
            case 'pruebas-sf-e':
                $this->sNew .= 'f';
                break;
        }
        return $this->sNew;
    }

    public function setNew($esquema)
    {
        $this->sNew = $esquema;
    }

    public function getRef()
    {
        if ($this->sRegionRef === null || $this->sDlRef === null
            || $this->sRegionRef === '' || $this->sDlRef === '') {
            throw new \RuntimeException(_('Faltan región y dl de referencia (setRegionRef / setDlRef).'));
        }

        $this->sRef = $this->sRegionRef . '-' . $this->sDlRef;
        switch ($this->getDb()) {
            case 'comun':
            case 'comun_select':
            case 'pruebas-comun':
                break;
            case 'sv':
            case 'sv-e':
            case 'sv-e_select':
            case 'pruebas-sv':
            case 'pruebas-sv-e':
                $this->sRef .= 'v';
                break;
            case 'sf':
            case 'sf-e':
            case 'pruebas-sf':
            case 'pruebas-sf-e':
                $this->sRef .= 'f';
                break;
        }
        return $this->sRef;
    }

    public function setRef($esquema)
    {
        $this->sRef = $esquema;
    }

    public function getHost()
    {
        return $this->Host;
    }

    public function setHost($Host)
    {
        $this->Host = $Host;
    }

    public function getSsh_user()
    {
        return $this->ssh_user;
    }

    public function setSsh_user($ssh_user)
    {
        $this->ssh_user = $ssh_user;
    }

    public function getDb()
    {
        return $this->sDb;
    }

    public function setDb($db)
    {
        $this->sDb = $db;
    }

    public function getDbRef()
    {
        if (empty($this->sDbRef)) {
            $this->sDbRef = $this->sDb;
        }
        return $this->sDbRef;
    }

    public function setDbRef($db)
    {
        $this->sDbRef = $db;
    }

    public function getFileRef()
    {
        $this->sfileRef = empty($this->sfileRef) ? $this->getDir() . '/' . $this->getRef() . '.' . $this->getDb() . '.sql' : $this->sfileRef;
        return $this->sfileRef;
    }

    public function setFileRef($fileRef)
    {
        $this->sfileRef = $fileRef;
    }

    public function getFileLog()
    {
        $this->sfileLog = empty($this->sfileLog) ? $this->getDir() . '/' . $this->getNew() . '.pg_error.sql' : $this->sfileLog;
        return $this->sfileLog;
    }

    public function setFileLog($fileLog)
    {
        $this->sfileLog = $fileLog;
    }

    public function getFileNew()
    {
        $this->sfileNew = empty($this->sfileNew) ? $this->getDir() . '/' . $this->getNew() . '.' . $this->getDb() . '.sql' : $this->sfileNew;
        return $this->sfileNew;
    }

    public function setFileNew($fileNew)
    {
        $this->sfileNew = $fileNew;
    }

    public function getFileSeq()
    {
        $this->sfileSeq = empty($this->sfileSeq) ? $this->getDir() . '/' . $this->getNew() . '.reset_seq.sql' : $this->sfileSeq;
        return $this->sfileSeq;
    }

    public function setFileSeq($fileSeq)
    {
        $this->sfileSeq = $fileSeq;
    }

    public function cambiar_nombre()
    {
        $dump = file_get_contents($this->getFileRef());
        if ($dump === false) {
            throw new \RuntimeException(sprintf(_('No se pudo leer %s'), $this->getFileRef()));
        }

        $dump_nou = $this->renombrarEsquemaEnVolcado($dump, $this->getRef(), $this->getNew());
        // comentar "CREATE SCHEMA; que ya está creado
        $dump_nou = str_replace('CREATE SCHEMA', '-- CREATE SCHEMA', $dump_nou);
        // cambiar nombre por defecto de la dl i r
        $pattern = "/(SET DEFAULT\s*')" . preg_quote($this->getRegionRef(), '/') . "(')/";
        $replacement = '$1' . $this->getRegionNew() . '$2';
        $dump_nou = preg_replace($pattern, $replacement, $dump_nou);
        $pattern = "/(SET DEFAULT\s*')" . preg_quote($this->getDlRef(), '/') . "(')/";
        $replacement = '$1' . $this->getDlNew() . '$2';
        $dump_nou = preg_replace($pattern, $replacement, $dump_nou);

        $dump_nou = self::normalizarVolcadoPgDumpParaPsql($dump_nou);
        $dump_nou = self::repararVolcadoHeredadoYCompatibilidad($dump_nou);

        $d = file_put_contents($this->getFileNew(), $dump_nou);
        if ($d === false) {
            printf(_("error al escribir el fichero"));
        }
    }

    /**
     * Quita meta-comandos y GUC de pg_dump ≥ 17.6 que psql antiguo no entiende
     * (p. ej. \restrict, SET transaction_timeout).
     */
    public static function normalizarVolcadoPgDumpParaPsql(string $sql): string
    {
        $lines = explode("\n", $sql);
        $out = [];
        foreach ($lines as $line) {
            $trim = ltrim($line);
            if ($trim !== '' && !str_starts_with($trim, '--')) {
                if (preg_match('/^\\\\restrict\\b/', $trim) === 1
                    || preg_match('/^\\\\unrestrict\\b/', $trim) === 1) {
                    continue;
                }
                if (preg_match('/^SET\\s+transaction_timeout\\b/i', $trim) === 1) {
                    continue;
                }
            }
            $out[] = $line;
        }

        return implode("\n", $out);
    }

    /**
     * Ajusta volcados de pg_dump reciente para psql del servidor (tablas INHERITS, NOT NULL sueltos).
     */
    public static function repararVolcadoHeredadoYCompatibilidad(string $sql): string
    {
        $sql = preg_replace(
            '/(INHERITS\s*\([^);]+\))\s*\n\s*(ALTER\s+TABLE)/i',
            "$1;\n$2",
            $sql,
        ) ?? $sql;

        $reparado = preg_replace_callback(
            '/CREATE TABLE ((?:"[^"]+"\.)?\w+)\s*\(\s*\n((?:\s*NOT NULL\s+\w+\s*,?\s*\n)+)\)\s*(\nINHERITS\s*\([^)]+\)\s*;?)/i',
            static function (array $m): string {
                $table = $m[1];
                $inherits = rtrim($m[3]);
                if (!str_ends_with($inherits, ';')) {
                    $inherits .= ';';
                }
                $alters = '';
                if (preg_match_all('/NOT NULL\s+(\w+)/i', $m[2], $cols)) {
                    foreach ($cols[1] as $col) {
                        $alters .= 'ALTER TABLE ONLY ' . $table . ' ALTER COLUMN ' . $col . ' SET NOT NULL;' . "\n";
                    }
                }

                return 'CREATE TABLE ' . $table . " (\n)\n" . $inherits . "\n" . $alters;
            },
            $sql,
        );

        return is_string($reparado) ? $reparado : $sql;
    }

    private function renombrarEsquemaEnVolcado(string $dump, string $ref, string $new): string
    {
        $refQ = preg_quote($ref, '/');
        $reemplazos = [
            '/"' . $refQ . '"/' => '"' . $new . '"',
            '/\b' . $refQ . '\./' => $new . '.',
            '/\bON\s+SCHEMA\s+' . $refQ . '\b/i' => 'ON SCHEMA ' . $new,
            '/\bSCHEMA\s+' . $refQ . '\b/i' => 'SCHEMA ' . $new,
            '/\bOWNER\s+TO\s+"' . $refQ . '"/i' => 'OWNER TO "' . $new . '"',
            '/\bAUTHORIZATION\s+"' . $refQ . '"/i' => 'AUTHORIZATION "' . $new . '"',
            "/public\.idschema\s*\(\s*'" . $refQ . "'::text\s*\)/i" => "public.idschema('" . $new . "'::text)",
            "/\bsearch_path\s*=\s*'" . $refQ . "'/i" => "search_path = '" . $new . "'",
            '/\bSchema:\s*' . $refQ . '\b/i' => 'Schema: ' . $new,
        ];
        $out = $dump;
        foreach ($reemplazos as $patron => $sustituto) {
            $out = preg_replace($patron, $sustituto, $out);
        }

        return $out;
    }

    private function majorVersionPostgresServidor(): ?int
    {
        try {
            $oConfigDB = new ConfigDB('importar');
            $config = $oConfigDB->getEsquema($this->claveEsquemaImportar());
            $pdo = (new DBConnection($config))->getPDO();
            $version = $pdo->query('SHOW server_version')->fetchColumn();
            if (is_string($version) && preg_match('/^(\d+)/', $version, $m) === 1) {
                return (int) $m[1];
            }
        } catch (\Throwable) {
        }

        return null;
    }

    public static function rutaBinarioPostgres(string $herramienta, ?int $majorServidor = null): string
    {
        $candidatos = [];
        if ($majorServidor !== null && $majorServidor > 0) {
            $candidatos[] = '/usr/lib/postgresql/' . $majorServidor . '/bin/' . $herramienta;
        }
        $candidatos[] = '/usr/bin/' . $herramienta;
        foreach ($candidatos as $ruta) {
            if (is_executable($ruta)) {
                return $ruta;
            }
        }

        return '/usr/bin/' . $herramienta;
    }

    private function binarioPostgres(string $herramienta): string
    {
        return self::rutaBinarioPostgres($herramienta, $this->majorVersionPostgresServidor());
    }

    public function crear()
    {
        if ($this->getHost() === 'db' ||
            $this->getHost() === '/var/run/postgresql' ||
            $this->getHost() === 'localhost' ||
            $this->getHost() === '127.0.0.1'
        ) {
            $this->crear_local();
        } else {
            $this->crear_remote();
        }
    }

    /**
     * Importa en el servidor interior (select) el .sql ya generado en el exterior y opcionalmente refresca la suscripción.
     *
     * @return string|null Aviso no bloqueante (p. ej. fallo al refrescar subscripción)
     */
    public function crear_select(string $db): ?string
    {
        $sqlPath = $this->getFileNew();
        if (!is_readable($sqlPath)) {
            throw new \RuntimeException(sprintf(
                _('No se encuentra el volcado SQL para importar en réplica: %s'),
                $sqlPath,
            ));
        }

        $oConnection = new DBConnection($this->config);
        $dsn = $oConnection->getURI();

        $logFile = $this->getFileLog();
        $host = $this->getHost();
        $psql = $this->binarioPostgres('psql');
        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' ' . escapeshellarg($psql) . ' -h ' . escapeshellarg($host)
            . ' -U postgres -q -X -t --pset pager=off -v ON_ERROR_STOP=1 --file='
            . escapeshellarg($sqlPath) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';
        $this->vaciarLog($logFile);
        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 4);

        if ($this->getHost() === 'db') {
            return null;
        }

        $refreshLog = $logFile . '.refresh_sub.sql';

        return (new DBRefresh())->refreshSubscription($host, $db, $dsn, $refreshLog);
    }

    private function crear_local()
    {
        $this->leer_local();
        $this->cambiar_nombre();
        $this->importar();
    }

    /**
     * Para la base de datos comun, que está en otro servidor y además con otra versión,
     * No sirve el pg_dump (solo funciona con versiones iguales en los dos extremos)
     */
    private function crear_remote()
    {
        $this->leer_remote();
        $this->cambiar_nombre();
        // Por el momento ya no tengo el otro servidor
        //$this->eliminar_sync();
        $this->importar();
    }


    /**
     * Ejecuta el pg_dump en la máquina remota a través de ssh.
     * No conviene hacerlo directamente, porque si las versiones del postgresql
     * de la máquina remota y local son distintas, da un error.
     *
     * Para poder ejecutar el ssh, se debe autorizar via id_rsa al usuario aquinate.
     */
    private function leer_remote()
    {
        //ssh user@remote_machine "pg_dump -U dbuser -h localhost -C --column-inserts" \
        //    > backup_file_on_your_local_machine.sql
        //  /usr/bin/ssh aquinate@192.168.200.16 "/usr/bin/pg_dump -s --schema=\\\"Acse-crAcse\\\"
        //          -U postgres -h 127.0.0.1 pruebas-comun" > /var/www/pruebas/log/db/Acse-crAcse.comun.sql

        $dbname = $this->getDbName();
        // para conexiones locales via sockets
        $host_local = '/var/run/postgresql';
        // leer esquema
        //$command_ssh = "/usr/bin/ssh aquinate@192.168.200.16";
        $command_ssh = "/usr/bin/ssh -i /var/www/.ssh/id_rsa " . $this->getSsh_user() . "@" . $this->getHost();
        $command_db = "/usr/bin/pg_dump -U postgres -s --schema=\\\\\\\"" . $this->getRef() . "\\\\\\\" ";
        $command_db .= "-h $host_local $dbname";
        $command = "$command_ssh \"$command_db\" > " . $this->getFileRef();
        passthru($command); // no output to capture so no need to store it
        /*
         $command .= " > " . $this->getFileLog() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        $error = file_get_contents($this->getFileLog());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(0): %s<br> mirar: %s<br>"), $command, $this->getFileLog());
            }
        }
        */
    }

    private function leer_local(): void
    {
        $dsn = $this->getConexionImportar();
        $logFile = $this->getFileLog();
        $host = $this->getHost();
        $schema = $this->getRef();
        $fileRef = $this->getFileRef();

        $pgDump = $this->binarioPostgres('pg_dump');
        $command = 'LC_ALL=C ' . escapeshellarg($pgDump) . ' -h ' . escapeshellarg($host)
            . ' -U postgres -s -n ' . escapeshellarg($this->patronPgDumpSchema($schema))
            . ' --file=' . escapeshellarg($fileRef)
            . ' ' . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        $this->vaciarLog($logFile);
        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 1);
    }

    /**
     * pg_dump -n interpreta patrones (psql \dn): nombres con guión deben ir entre comillas dobles.
     */
    private function patronPgDumpSchema(string $schema): string
    {
        $schema = str_replace('"', '', $schema);

        return '"' . $schema . '"';
    }

    public function importar(): void
    {
        $dsn = $this->getConexionImportar();
        $logFile = $this->getFileLog();
        $host = $this->getHost();

        $psql = $this->binarioPostgres('psql');
        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' ' . escapeshellarg($psql) . ' -h ' . escapeshellarg($host)
            . ' -U postgres -q -X -t --pset pager=off -v ON_ERROR_STOP=1 --file='
            . escapeshellarg($this->getFileNew()) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        $this->vaciarLog($logFile);
        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 2);
    }

    /**
     * @param string|null $nombreEsquema si se indica, no hace falta setRegionNew/setDlNew (p. ej. `B-crBv`)
     */
    public function eliminar(?string $nombreEsquema = null): void
    {
        $esquema = $nombreEsquema ?? $this->getNew();
        $this->asegurarRutaLogParaEsquema($esquema);
        $this->vaciarLog($this->getFileLog());
        $this->eliminarEsquemaPorPdo($esquema);
        // Segunda pasada por si quedaron objetos huérfanos tras el traslado a resto
        $this->eliminarEsquemaPorPdo($esquema);
    }

    private function asegurarRutaLogParaEsquema(string $esquema): void
    {
        if ($this->sfileLog === '') {
            $this->sfileLog = $this->getDir() . '/' . $esquema . '.pg_error.sql';
        }
    }

    private function eliminarEsquemaPorPdo(string $esquema): void
    {
        $config = (new ConfigDB('importar'))->getConexionMantenimiento($this->claveEsquemaImportar());
        $pdo = (new DBConnection($config))->getPDO();
        $qEsquema = '"' . str_replace('"', '""', $esquema) . '"';
        $sqlDrop = 'DROP SCHEMA IF EXISTS ' . $qEsquema . ' CASCADE';

        try {
            $pdo->exec($sqlDrop);
        } catch (\PDOException $e) {
            if (!$this->esErrorPropietarioEsquema($e)) {
                throw new \RuntimeException(sprintf(
                    _('No se pudo eliminar el esquema «%1$s»: %2$s'),
                    $esquema,
                    $e->getMessage(),
                ), 0, $e);
            }
            $pdo->exec('ALTER SCHEMA ' . $qEsquema . ' OWNER TO CURRENT_USER');
            $pdo->exec($sqlDrop);
        }
    }

    private function esErrorPropietarioEsquema(\PDOException $e): bool
    {
        $msg = $e->getMessage();

        return str_contains($msg, 'must be owner')
            || str_contains($msg, 'debe ser dueño')
            || str_contains($msg, '42501');
    }

    private function vaciarLog(string $logFile): void
    {
        file_put_contents($logFile, '');
    }

    private function claveEsquemaImportar(): string
    {
        return match ($this->getDb()) {
            'comun', 'comun_select', 'pruebas-comun' => 'public',
            'sv', 'pruebas-sv' => 'publicv',
            'sv-e', 'sv-e_select', 'pruebas-sv-e' => 'publicv-e',
            'sf', 'sf-e', 'pruebas-sf', 'pruebas-sf-e' => 'publicf',
            default => 'public',
        };
    }

    private function getConexionImportar(): string
    {
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getConexionMantenimiento($this->claveEsquemaImportar());

        return (new DBConnection($config))->getURI();
    }

    /** Ignora avisos perl de locale en stderr; solo cuenta líneas de error reales de psql/pg_dump. */
    private function logPsqlSinErrorReal(string $path): bool
    {
        if (!is_readable($path)) {
            return true;
        }
        $contents = file_get_contents($path);
        if ($contents === false || trim($contents) === '') {
            return true;
        }
        foreach (explode("\n", $contents) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, 'perl: warning:')) {
                continue;
            }

            return false;
        }

        return true;
    }

    private function lanzarSiLogPsqlConError(string $command, string $logFile, int $numeroComando): void
    {
        if ($this->logPsqlSinErrorReal($logFile)) {
            return;
        }

        $detalle = trim((string) file_get_contents($logFile));
        $msg = sprintf(
            _('Error pg_dump/psql (comando %1$d). Log: %2$s.%3$s'),
            $numeroComando,
            $logFile,
            $detalle !== '' ? ' ' . $detalle : ' ' . $command,
        );
        if ($detalle !== ''
            && str_contains($detalle, 'NOT NULL')
            && str_contains($detalle, 'syntax error')) {
            $msg .= ' ' . _(
                'Suele deberse a pg_dump y psql de versiones distintas; tras desplegar, el cliente debe coincidir con la major del servidor (p. ej. /usr/lib/postgresql/15/bin/).',
            );
        }

        throw new \RuntimeException($msg);
    }

    /**
     * Eliminar los triggers de bucardo. (si existen)
     * La definición de si hay que sincronizar se hará desde otro sitio.
     */
    private function eliminar_sync()
    {
        $dump = file_get_contents($this->getFileNew());

        $pattern = "/^.*bucardo.*$/im";
        $replacement = '';
        $dump_nou = preg_replace($pattern, $replacement, $dump);

        $d = file_put_contents($this->getFileNew(), $dump_nou);
        if ($d === false) printf(_("error al escribir el fichero"));
    }

    protected function getConfigConexion($esq = 'ref')
    {
        // No he conseguido que funcione con ~/.pgpass.
        if ($esq === 'ref') {
            $esquema = $this->getRef();
        } elseif ($esq === 'new') {
            $esquema = $this->getNew();
        }
        switch ($this->sDb) {
            case 'pruebas-comun':
            case 'comun':
                $oConfigDB = new ConfigDB('comun'); //de la database comun
                $config = $oConfigDB->getEsquema($esquema); //de la database comun
                break;
            case 'pruebas-sv':
            case 'sv':
                $oConfigDB = new ConfigDB('sv'); //de la database sv
                $config = $oConfigDB->getEsquema($esquema); //de la database sv
                break;
            case 'pruebas-sf':
            case 'sf':
                $oConfigDB = new ConfigDB('sf'); //de la database sf
                $config = $oConfigDB->getEsquema($esquema); //de la database sf
                break;
            case 'pruebas-sv-e':
            case 'sv-e':
                $oConfigDB = new ConfigDB('sv-e'); //de la database sv
                $config = $oConfigDB->getEsquema($esquema); //de la database sv
                break;
            case 'pruebas-sf-e':
            case 'sf-e':
                $oConfigDB = new ConfigDB('sf-e'); //de la database sf
                $config = $oConfigDB->getEsquema($esquema); //de la database sf
                break;
        }

        return $config;
    }

    private function getConexion($esquema = 'ref')
    {
        $config = $this->getConfigConexion($esquema);
        $this->dbname = $config['dbname'];

        $oConnection = new DBConnection($config);
        $dsn = $oConnection->getURI();

        return $dsn;
    }

    private function getDbName()
    {
        $this->getConexion();
        return $this->dbname;
    }

    private function deleteFile($file)
    {
        $command = "/bin/rm -f " . $file;
        passthru($command); // no output to capture so no need to store it
    }
}
