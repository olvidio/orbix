<?php

namespace src\shared\infrastructure\persistence\postgresql;

use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\infrastructure\persistence\DBConnection;


use src\utils_database\domain\entity\DBAbstract;

class DBTabla extends DBAbstract
{
    /**
     * oDbl de Esquema
     *
     * @var object
     */
    protected $oDbl;
    /**
     * Tablas de Esquema
     *
     * @var array
     */
    private $aTablas;
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
    private $sfileLogR;
    /**
     * Fichero con el log de la accion de Esquema
     *
     * @var string
     */
    private $sfileLogW;

    private $Host;
    private $ssh_user;

    private $sDb;
    private mixed $dbname;

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
            $this->deleteFile($this->getFileLogR());
            $this->deleteFile($this->getFileLogW());
        }
    }

    /* MÉTODOS GET y SET --------------------------------------------------------*/

    public function setConfig($config)
    {
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

    public function getTablas()
    {
        return $this->aTablas;
    }

    public function setTablas($tablas)
    {
        $this->aTablas = $tablas;
    }

    public function getNew()
    {
        return $this->sNew;
    }

    public function setNew($esquema)
    {
        $this->sNew = $esquema;
        $this->sfileNew = '';
    }

    public function getRef()
    {
        return $this->sRef;
    }

    public function setRef($esquema)
    {
        $this->sRef = $esquema;
        $this->sfileRef = '';
    }

    public function getHost()
    {
        return $this->Host;
    }

    public function setHost($host)
    {
        $this->Host = $host;
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

    public function getFileRef()
    {
        $this->sfileRef = empty($this->sfileRef) ? $this->getDir() . '/dbRef' . $this->getRef() . '.' . $this->getDb() . '.sql' : $this->sfileRef;
        return $this->sfileRef;
    }

    public function setFileRef($fileRef)
    {
        $this->sfileRef = $fileRef;
    }

    public function getFileLogR()
    {
        $this->sfileLogR = empty($this->sfileLogR) ? $this->getDir() . '/pg_error_read.' . $this->getDb() . '.sql' : $this->sfileLogR;
        return $this->sfileLogR;
    }

    public function setFileLogR($fileLog)
    {
        $this->sfileLogR = $fileLog;
    }

    public function getFileLogW()
    {
        $this->sfileLogW = empty($this->sfileLogW) ? $this->getDir() . '/pg_error_write.' . $this->getDb() . '.sql' : $this->sfileLogW;
        return $this->sfileLogW;
    }

    public function setFileLogW($fileLog)
    {
        $this->sfileLogW = $fileLog;
    }

    public function getFileNew()
    {
        $this->sfileNew = empty($this->sfileNew) ? $this->getDir() . '/dbNew' . $this->getNew() . '.' . $this->getDb() . '.sql' : $this->sfileNew;
        return $this->sfileNew;
    }

    public function setFileNew($fileNew)
    {
        $this->sfileNew = $fileNew;
    }

    /**
     * Pasa tablas de sv a sv-e
     * para la base de datos sv-e, que está en otro servidor y además con otra versión,
     * No sirve el pg_dump (solo funciona con versiones iguales en los dos extremos)
     */
    public function mover($configRef, $configNew)
    {


        $this->setConfig($configRef);
        $this->leer_local(FALSE);

        $this->cambiar_nombre_fichero();
        // Para crear tablas, permiso de superusuario...
        $this->setConfig($configNew);

        //devuelve FALSE si falla algo.
        return $this->importarAsAdmin();
    }

    /**
     * Para la base de datos comun, que está en otro servidor y además con otra versión,
     * No sirve el pg_dump (solo funciona con versiones iguales en los dos extremos)
     */
    public function copiar()
    {
        if ($this->getHost() === 'db' ||
            $this->getHost() === '/var/run/postgresql' ||
            $this->getHost() === 'localhost' ||
            $this->getHost() === '127.0.0.1'
        ) {
            $this->copiar_local();
        } else {
            $this->copiar_remote();
        }
    }

    private function copiar_remote()
    {
        $this->prepararFicherosVolcadoCopiar();
        $this->leer_remote();
        $this->cambiar_nombre();
        $this->vaciarTablasDestino();
        $this->importar();
        $this->actualizar_schema();
    }

    private function copiar_local()
    {
        $this->prepararFicherosVolcadoCopiar();
        $this->leer_local();
        $this->cambiar_nombre();
        $this->vaciarTablasDestino();
        $this->importar();
        $this->actualizar_schema();
    }

    private function prepararFicherosVolcadoCopiar(): void
    {
        foreach ([$this->getFileRef(), $this->getFileNew()] as $path) {
            if (is_string($path) && $path !== '' && is_file($path)) {
                $this->deleteFile($path);
            }
        }
    }

    public function cambiar_nombre()
    {
        $partesRef = explode('-', $this->getRef(), 2);
        $partesNew = explode('-', $this->getNew(), 2);
        if (count($partesRef) < 2 || count($partesNew) < 2) {
            throw new \InvalidArgumentException(_('Esquema ref o destino con formato región-dl no válido.'));
        }
        [$crRef, $dlRef] = $partesRef;
        [$crNew, $dlNew] = $partesNew;

        $dump = file_get_contents($this->getFileRef());
        if ($dump === false) {
            throw new \RuntimeException(sprintf(_('No se pudo leer el volcado de referencia: %s'), $this->getFileRef()));
        }

        $dump_nou = str_replace($this->getRef(), $this->getNew(), $dump);
        $pattern = "/(SET DEFAULT\s*')".preg_quote($crRef, '/')."(')/";
        $dump_nou = preg_replace($pattern, '$1' . $crNew . '$2', $dump_nou) ?? $dump_nou;
        $pattern = "/(SET DEFAULT\s*')".preg_quote($dlRef, '/')."(')/";
        $dump_nou = preg_replace($pattern, '$1' . $dlNew . '$2', $dump_nou) ?? $dump_nou;

        if (file_put_contents($this->getFileNew(), $dump_nou) === false) {
            throw new \RuntimeException(sprintf(_('No se pudo escribir %s'), $this->getFileNew()));
        }
    }

    public function cambiar_nombre_fichero()
    {
        $dump = file_get_contents($this->getFileRef());
        $d = file_put_contents($this->getFileNew(), $dump);
        if ($d === false) exit (_("error al escribir el fichero"));
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
        $sTablas = '';
        foreach ($this->aTablas as $tabla => $param) {
            $sTablas .= " -t \\\\\\\"" . $this->getRef() . "\\\\\\\".$tabla ";
        }
        //ssh user@remote_machine "pg_dump -U dbuser -h localhost -C --column-inserts" \
        //    > backup_file_on_your_local_machine.sql
        //  /usr/bin/ssh aquinate@192.168.200.16 "/usr/bin/pg_dump -s --schema=\\\"Acse-crAcse\\\"
        //          -U postgres -h 127.0.0.1 pruebas-comun" > /var/www/pruebas/log/db/Acse-crAcse.comun.sql

        $dbname = $this->getDbName();
        $host_local = $this->getHost();

        // leer esquema
        //$command_ssh = "/usr/bin/ssh aquinate@192.168.200.16";
        $command_ssh = "/usr/bin/ssh " . $this->getSsh_user() . "@" . $this->getHost();
        $command_db = "/usr/bin/pg_dump -a" . $sTablas . " ";
        $command_db .= "-U postgres -h $host_local $dbname";
        $command = "$command_ssh \"$command_db\" > " . $this->getFileRef();
        //echo "$command<br>";
        passthru($command); // no output to capture so no need to store it
    }

    private function leer_local(bool $data_only = true): void
    {
        $partesTabla = [];
        foreach ($this->aTablas as $tabla => $param) {
            $partesTabla[] = '-t ' . escapeshellarg($this->tablaPgDumpCalificada($this->getRef(), $tabla));
        }

        $dsn = $this->getConexionImportar();
        $logFile = $this->getFileLogR();
        $host = $this->getHost();
        $soloDatos = $data_only ? '-a ' : '';

        $command = 'LC_ALL=C /usr/bin/pg_dump -h ' . escapeshellarg($host)
            . ' -U postgres ' . $soloDatos
            . implode(' ', $partesTabla) . ' '
            . '--file=' . escapeshellarg($this->getFileRef()) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        passthru($command);
        $this->lanzarSiLogConError($command, $logFile, 1, 'pg_dump');
    }

    private function tablaPgDumpCalificada(string $schema, string $tabla): string
    {
        $schema = str_replace('"', '', $schema);

        return '"' . $schema . '".' . $tabla;
    }

    public function eliminarTabla($nom_tabla)
    {
        // OJO Corresponde al esquema sf/sv, no al comun.
        /*
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
         */
        $this->addPermisoGlobal('sfsv');

        /*
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);
        */

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
        // Devolver los valores al estado original
        //$this->esquema = $esquema_org;
        //$this->role = $role_org;
    }

    public function importarAsAdmin()
    {
        $dsn = $this->getConexionAdmin('publicv-e');
        $logFile = $this->getFileLogW();
        $host = $this->getHost();

        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' /usr/bin/psql -h ' . escapeshellarg($host)
            . ' -U postgres -q -X -t --pset pager=off --file='
            . escapeshellarg($this->getFileNew()) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        passthru($command);
        if ($this->logPsqlSinErrorReal($logFile)) {
            return true;
        }

        if (ConfigGlobal::is_debug_mode()) {
            $error = (string) file_get_contents($logFile);
            echo sprintf(_('PSQL ERROR IN COMMAND(2): %1$s<br><br> mirar en: %2$s'), $command, $logFile);
            echo '<br>' . _('Si sólo salen números, son las filas que se ha insertado: Está bien.');
            echo '<pre>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</pre>';
        }

        return false;
    }

    public function importar(): void
    {
        $dsn = $this->getConexionImportar();
        $logFile = $this->getFileLogW();
        $host = $this->getHost();

        $command = 'LC_ALL=C PGOPTIONS=' . escapeshellarg('--client-min-messages=warning')
            . ' /usr/bin/psql -h ' . escapeshellarg($host)
            . ' -U postgres -q -X -t --pset pager=off --file='
            . escapeshellarg($this->getFileNew()) . ' '
            . escapeshellarg($dsn)
            . ' > ' . escapeshellarg($logFile) . ' 2>&1';

        passthru($command);
        $this->lanzarSiLogConError($command, $logFile, 3, 'psql');
    }

    /**
     * Para actualizar el campo id_schema
     */
    public function actualizar_schema()
    {
        $oDbl = $this->getConexionPDO('new');
        foreach ($this->aTablas as $tabla => $param) {
            if (!empty($param['id_schema']) && $param['id_schema'] === 'yes') {
                $sqlSchema = "UPDATE $tabla SET id_schema = DEFAULT;";
                if ($oDbl->query($sqlSchema) === false) {
                    $sClauError = 'DBTabla.schema.execute';
                    $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                    return false;
                }
            }
        }
    }

    private function getConfigConexion($esq = 'ref')
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

    private function getConexionAdmin($esquema = 'ref')
    {
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema($esquema);
        $this->dbname = $config['dbname'];

        $oConnection = new DBConnection($config);
        $dsn = $oConnection->getURI();

        return $dsn;
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
        $this->getConexion('ref');
        return $this->dbname;
    }

    private function getConexionPDO($esquema = 'ref')
    {
        $config = $this->getConfigConexion($esquema);

        $oConnection = new DBConnection($config);
        return $oConnection->getPDO();
    }

    /**
     * Paso copiar: las tablas de config ya existen (vacías) tras crear esquema; si se reintenta
     * tras un fallo parcial hay que vaciarlas antes del COPY.
     */
    private function vaciarTablasDestino(): void
    {
        $tablas = array_keys($this->aTablas);
        if ($tablas === []) {
            return;
        }

        $schema = str_replace('"', '', $this->getNew());
        $qualified = [];
        foreach ($tablas as $tabla) {
            $qualified[] = '"' . $schema . '"."' . str_replace('"', '', $tabla) . '"';
        }

        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getConexionMantenimiento($this->claveEsquemaImportar());
        $pdo = (new DBConnection($config))->getPDO();
        $sql = 'TRUNCATE TABLE ' . implode(', ', $qualified) . ' RESTART IDENTITY CASCADE';
        $pdo->exec($sql);
    }

    private function claveEsquemaImportar(): string
    {
        return match ($this->sDb) {
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
            if (preg_match('/^COPY \d+$/', $line) === 1) {
                continue;
            }
            if (preg_match('/^INSERT 0 \d+$/', $line) === 1) {
                continue;
            }
            if (preg_match('/^\d+$/', $line) === 1) {
                continue;
            }

            return false;
        }

        return true;
    }

    private function lanzarSiLogConError(string $command, string $logFile, int $numeroComando, string $herramienta): void
    {
        if ($this->logPsqlSinErrorReal($logFile)) {
            return;
        }

        $detalle = trim((string) file_get_contents($logFile));
        throw new \RuntimeException(sprintf(
            _('Error %1$s (comando %2$d). Origen «%3$s» → destino «%4$s». Log: %5$s.%6$s'),
            $herramienta,
            $numeroComando,
            $this->getRef(),
            $this->getNew(),
            $logFile,
            $detalle !== '' ? ' ' . $detalle : ' ' . $command,
        ));
    }

    private function deleteFile($file)
    {
        $command = '/bin/rm -f ' . escapeshellarg($file);
        passthru($command);
    }
}
