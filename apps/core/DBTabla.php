<?php

namespace core;

use devel\model\DBAbstract;

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
    }

    public function getRef()
    {
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
        $this->setFileRef($this->getDir() . '/dbRef' . $this->getRef() . '.' . $db . '.sql');
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
        $this->leer_remote();
        $this->cambiar_nombre();
        $this->importar();
        $this->actualizar_schema();
    }

    private function copiar_local()
    {
        $this->leer_local();
        $this->cambiar_nombre();
        $this->importar();
        $this->actualizar_schema();
    }

    public function cambiar_nombre()
    {
        $esqemaRef = $this->getRef();
        $crRef = strtok($esqemaRef, '-');
        $dlRef = strtok('-');
        $esqemaNew = $this->getNew();
        $crNew = strtok($esqemaNew, '-');
        $dlNew = strtok('-');

        $dump = file_get_contents($this->getFileRef());
        // cambiar nombre esquema
        $dump_nou = str_replace($this->getRef(), $this->getNew(), $dump);
        // cambiar nombre por defecto de la dl i r
        $pattern = "/(SET DEFAULT\s*')$crRef(')/";
        $replacement = "$1$crNew$2";
        $dump_nou = preg_replace($pattern, $replacement, $dump_nou);
        $pattern = "/(SET DEFAULT\s*')$dlRef(')/";
        $replacement = "$1$dlNew$2";
        $dump_nou = preg_replace($pattern, $replacement, $dump_nou);

        $d = file_put_contents($this->getFileNew(), $dump_nou);
        if ($d === false) exit (_("error al escribir el fichero"));
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

    private function leer_local($data_only = TRUE)
    {
        $a = ($data_only) ? '-a' : '';
        $sTablas = '';
        foreach ($this->aTablas as $tabla => $param) {
            $sTablas .= "-t \\\"" . $this->getRef() . "\\\".$tabla ";
        }
        //pg_dump --dbname=postgresql://username:password@host:port/database > file.sql
        // crear archivo con el password
        $dsn = $this->getConexion('ref');
        // leer esquema
        $command = "/usr/bin/pg_dump -h " . $this->getHost() . "  -U postgres $a $sTablas";
        $command .= "--file=" . $this->getFileRef() . " ";
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $this->getFileLogR() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLogR());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PG_DUMP ERROR IN COMMAND(1): %s<br> mirar: %s<br>"), $command, $this->getFileLogR());
            }
        }
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
        // crear archivo con el password
        $dsn = $this->getConexionAdmin('publicv-e');

        $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -h " . $this->getHost() . " -U postgres -q -X -t ";
        $command .= "--pset pager=off ";
        $command .= "--file=" . $this->getFileNew() . " ";
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $this->getFileLogW() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLogW());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(2): %s<br><br> mirar en: %s"), $command, $this->getFileLogW());
                echo "<br>" . _("Si sólo salen números, son las filas que se ha insertado: Está bien.");
                echo "<pre>$error</pre>";
                return FALSE;
            }
        }
        return TRUE;
    }

    public function importar()
    {
        // crear archivo con el password
        $dsn = $this->getConexion('new');

        $command = "PGOPTIONS='--client-min-messages=warning' /usr/bin/psql -h " . $this->getHost() . " -U postgres -q -X -t ";
        $command .= "--pset pager=off ";
        $command .= "--file=" . $this->getFileNew() . " ";
        $command .= "\"" . $dsn . "\"";
        $command .= " > " . $this->getFileLogW() . " 2>&1";
        passthru($command); // no output to capture so no need to store it
        // read the file, if empty all's well
        $error = file_get_contents($this->getFileLogW());
        if (trim($error) != '') {
            if (ConfigGlobal::is_debug_mode()) {
                echo sprintf(_("PSQL ERROR IN COMMAND(3): %s<br><br> mirar en: %s"), $command, $this->getFileLogW());
                echo "<br>" . _("Si sólo salen números, son las filas que se ha insertado: Está bien.");
                echo "<pre>$error</pre>";
            }
        }
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

    private function deleteFile($file)
    {
        $command = "/bin/rm -f " . $file;
        passthru($command); // no output to capture so no need to store it
    }
}
