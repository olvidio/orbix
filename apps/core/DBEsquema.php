<?php

namespace core;
class DBEsquema
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
        // cambiar nombre esquema
        $dump_nou = str_replace($this->getRef(), $this->getNew(), $dump);
        // comentar "CREATE SCHEMA; que ya está creado
        $dump_nou = str_replace('CREATE SCHEMA', '-- CREATE SCHEMA', $dump_nou);
        // cambiar nombre por defecto de la dl i r
        $pattern = "/(SET DEFAULT\s*')" . $this->getRegionRef() . "(')/";
        $replacement = "$1" . $this->getRegionNew() . "$2";
        $dump_nou = preg_replace($pattern, $replacement, $dump_nou);
        $pattern = "/(SET DEFAULT\s*')" . $this->getDlRef() . "(')/";
        $replacement = "$1" . $this->getDlNew() . "$2";
        $dump_nou = preg_replace($pattern, $replacement, $dump_nou);

        $d = file_put_contents($this->getFileNew(), $dump_nou);
        if ($d === false) printf(_("error al escribir el fichero"));
    }

    public function crear()
    {
        if ($this->getHost() === '/var/run/postgresql' || $this->getHost() === 'localhost' || $this->getHost() === '127.0.0.1') {
            $this->crear_local();
        } else {
            $this->crear_remote();
        }
    }
    public function crear_select() {
        // de momento nada:
        return TRUE;
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
        $command_db = "/usr/bin/pg_dump -s --schema=\\\\\\\"" . $this->getRef() . "\\\\\\\" ";
        $command_db .= "-U postgres -h $host_local $dbname";
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

    private function leer_local()
    {
        //pg_dump --dbname=postgresql://username:password@host:port/database > file.sql
        // crear archivo con el password
        $dsn = $this->getConexion('ref');
        // leer esquema
        $command = "/usr/bin/pg_dump -s --schema=\\\"" . $this->getRef() . "\\\" ";
        $command .= "--file=" . $this->getFileRef() . " ";
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $this->getFileLog() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLog());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(1): %s<br> mirar: %s<br>"), $command, $this->getFileLog());
            }
        }
    }

    public function importar()
    {
        // crear archivo con el password
        $dsn = $this->getConexion('new');
        // Importar el esquema en la base de datos comun
        $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -q  -X -t --pset pager=off ";
        $command .= "--file=" . $this->getFileNew() . " ";
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $this->getFileLog() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLog());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(2): %s<br> mirar en: %s<br>"), $command, $this->getFileLog());
            }
        }
    }

    public function eliminar()
    {
        $dsn = $this->getConexion('new');
        $esquema = $this->getNew();
        $sql = "DROP SCHEMA IF EXISTS \\\"" . $esquema . "\\\" CASCADE;";

        $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -q -X -t --pset pager=off";
        $command .= " -c \"" . $sql . "\" ";
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $this->getFileLog() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLog());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(3): %s<br> mirar en: %s<br>"), $command, $this->getFileLog());
            }
        }
        //Quizá hay que hacerlo dos veces:
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLog());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(4): %s<br> mirar en: %s<br>"), $command, $this->getFileLog());
            }
        }
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
            case 'comun_select':
                $oConfigDB = new ConfigDB('comun_select'); //de la database sv
                $config = $oConfigDB->getEsquema($esquema); //de la database sv
                break;
            case 'sv-e_select':
                $oConfigDB = new ConfigDB('sv-e_select'); //de la database sv
                $config = $oConfigDB->getEsquema($esquema); //de la database sv
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
