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
    private $sfileRef;
    /**
     * Fichero con el volcado del nuevo esquema de Esquema
     *
     * @var string
     */
    private $sfileNew;
    /**
     * Fichero con el log de la accion de Esquema
     *
     * @var string
     */
    private $sfileLog;
    /**
     * Fichero con la lista de secuencias a actualizar del Esquema
     *
     * @var string
     */
    private $sfileSeq;

    private $sDb;
    private $user;
    private $password;

    private $dbname;
    private $Host;
    private $ssh_user;

    /* CONSTRUCTOR -------------------------------------------------------------- */
    private mixed $sRegionRef;
    private mixed $sDlRef;
    private mixed $sDlNew;
    private mixed $sRegionNew;
    private mixed $sDbRef;
    protected array $config;

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
        // No los borro si estoy en debug
        if (!ConfigGlobal::is_debug_mode()) {
            $this->deleteFile($this->getFileNew());
            $this->deleteFile($this->getFileRef());
            $this->deleteFile($this->getFileLog());
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
        $this->sNew = $this->getRegionNew() . '-' . $this->getDlNew();
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
        $this->sRef = $this->getRegionRef() . '-' . $this->getDlRef();
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

    private function renombrarEsquemaEnVolcado(string $dump, string $ref, string $new): string
    {
        $refQ = preg_quote($ref, '/');
        $out = preg_replace('/"' . $refQ . '"/', '"' . $new . '"', $dump);
        $out = preg_replace('/\b' . $refQ . '\./', $new . '.', $out);
        $out = preg_replace('/\bON\s+SCHEMA\s+' . $refQ . '\b/i', 'ON SCHEMA ' . $new, $out);
        $out = preg_replace('/\bSCHEMA\s+' . $refQ . '\b/i', 'SCHEMA ' . $new, $out);

        return str_replace($ref, $new, $out);
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

    public function crear_select(string $db)
    {
        // es para las copias locales del servidor externo.
        // Ya tenemos los archivos creados,
        // leer_local()
        // cambiar_nombre()
        // sólo hay que importalos al servidor interior
        // importar():

        //cambiar la conexión
        $oConnection = new DBConnection($this->config);
        $dsn = $oConnection->getURI();

        $logFile = $this->getFileLog();
        $host = $this->getHost();
        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' /usr/bin/psql -h ' . escapeshellarg($host)
            . ' -U postgres -q -X -t --pset pager=off --file='
            . escapeshellarg($this->getFileNew()) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';
        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 4);


        ///// REFRESCAR LA SUBSCRIPCIÓN ///////////
        /// No para develop
        if ($this->getHost() !== 'db') {
            $host = $this->getHost();
            $fileLog = $this->getFileLog();
            $DBRefresh = new DBRefresh();
            $DBRefresh->refreshSubscription($host, $db, $dsn, $fileLog);
        }


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

        $command = 'LC_ALL=C /usr/bin/pg_dump -h ' . escapeshellarg($host)
            . ' -U postgres -s -n ' . escapeshellarg($this->patronPgDumpSchema($schema))
            . ' --file=' . escapeshellarg($fileRef)
            . ' ' . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

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

        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' /usr/bin/psql -h ' . escapeshellarg($host)
            . ' -U postgres -q -X -t --pset pager=off --file='
            . escapeshellarg($this->getFileNew()) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 2);
    }

    public function eliminar(): void
    {
        $esquema = $this->getNew();
        $sql = 'DROP SCHEMA IF EXISTS "' . $esquema . '" CASCADE;';
        $dsn = $this->getConexionImportar();
        $logFile = $this->getFileLog();
        $host = $this->getHost();

        $command = "LC_ALL=C PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -h " . escapeshellarg($host)
            . " -q -X -t --pset pager=off -c " . escapeshellarg($sql)
            . ' ' . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 3);
        // Quizá hay que hacerlo dos veces:
        passthru($command);
        $this->lanzarSiLogPsqlConError($command, $logFile, 4);
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
        $config = $oConfigDB->getEsquema($this->claveEsquemaImportar());

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
        throw new \RuntimeException(sprintf(
            _('Error pg_dump/psql (comando %1$d). Log: %2$s.%3$s'),
            $numeroComando,
            $logFile,
            $detalle !== '' ? ' ' . $detalle : ' ' . $command,
        ));
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
