<?php

namespace src\zonassacd\db;

use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\utils_database\domain\entity\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 *
 * Las tablas de zonas están en la BD comun: las lee también la instalación sf
 * (plan de misas) y la DMZ a través de comun_select.
 */
class DBEsquema extends DBAbstract
{

    private string $dir_base = ServerConf::DIR . "/src/zonassacd/db";

    public function __construct(?string $esquema_sfsv = null)
    {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll(): void
    {
        $this->eliminar_zonas_ctr();
        $this->eliminar_zonas();
        $this->eliminar_zonas_grupos();
        $this->eliminar_zonas_sacd();
        // eliminar las tablas en la DBSelect para la sincronización.
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->dropAllSelect();
        }
    }

    public function createAll(): void
    {
        $this->create_zonas();
        $this->create_zonas_grupos();
        $this->create_zonas_sacd();
        $this->create_zonas_ctr();
        // crear las tablas en la DBSelect para la sincronización.
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->createAllSelect();
        }
    }

    public function llenarAll(): void
    {
        $this->llenar_zonas();
        $this->llenar_zonas_grupos();
    }

    /**
     * @return array{tabla: string, nom_tabla: string, campo_seq: string, id_seq: string, filename: string}
     */
    protected function infoTable(string $tabla): array
    {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "zonas":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_zona';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "zonas_grupos":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_grupo';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "zonas_sacd":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            default:
                // zonas_ctr no tiene secuencia: la clave es el id_ubi del centro.
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";
        return $datosTabla;
    }

    public function create_zonas(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "zonas";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_zona)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_zonas(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("zonas");

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_zonas_grupos(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "zonas_grupos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_grupo)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_zonas_grupos(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("zonas_grupos");

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_zonas_sacd(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "zonas_sacd";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $nom_zonas = $this->getNomTabla('zonas');
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_item),
                        CONSTRAINT zonas_sacd_id_nom_key UNIQUE (id_nom, id_zona),
                        CONSTRAINT zonas_sacd_id_zona_fkey FOREIGN KEY (id_zona)
                            REFERENCES $nom_zonas(id_zona) ON DELETE CASCADE
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_zonas_sacd(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("zonas_sacd");

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * Relación centro-zona: un centro pertenece como mucho a una zona, y la ausencia
     * de fila significa que no tiene ninguna asignada.
     *
     * Vive en comun (y no como columna de las tablas de centros) porque el centro
     * puede ser de sv (`u_centros_dl`, BD sv) o de sf (`cu_centros_dlf`, comun), y
     * ambos casos se consultan desde el plan de misas, cuyas tablas están en comun.
     */
    public function create_zonas_ctr(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "zonas_ctr";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_zonas = $this->getNomTabla('zonas');
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_ubi),
                        CONSTRAINT zonas_ctr_id_zona_fkey FOREIGN KEY (id_zona)
                            REFERENCES $nom_zonas(id_zona) ON DELETE CASCADE
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS zonas_ctr_id_zona_idx ON $nom_tabla (id_zona);";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_zonas_ctr(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("zonas_ctr");

        $this->eliminar($datosTabla['nom_tabla']);

        $this->delPermisoGlobal('comun');
    }

    //// LLENAR
    public function llenar_zonas(): void
    {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');

        $datosTabla = $this->infoTable("zonas");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        $nom_zonas_sacd = $this->getNomTabla('zonas_sacd');
        $nom_zonas_ctr = $this->getNomTabla('zonas_ctr');

        $a_sql = [];
        // Las tablas que apuntan a zonas tienen un foreign key: con CASCADE se
        // llevaría por delante sus filas, así que se quita y se vuelve a poner.
        $a_sql[] = "ALTER TABLE $nom_zonas_sacd DROP CONSTRAINT IF EXISTS zonas_sacd_id_zona_fkey; ";
        $a_sql[] = "ALTER TABLE $nom_zonas_ctr DROP CONSTRAINT IF EXISTS zonas_ctr_id_zona_fkey; ";
        $a_sql[] = "TRUNCATE $nom_tabla RESTART IDENTITY;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_zona, nombre_zona, orden, id_grupo, id_nom";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        if ($oDbl === null) {
            throw new \RuntimeException(_('No hay conexión a la base de datos para importar zonas.'));
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql = [];
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $a_sql = [];
        $a_sql[] = "ALTER TABLE $nom_zonas_sacd
                        ADD CONSTRAINT zonas_sacd_id_zona_fkey FOREIGN KEY (id_zona) REFERENCES $nom_tabla(id_zona) ON DELETE CASCADE; ";
        $a_sql[] = "ALTER TABLE $nom_zonas_ctr
                        ADD CONSTRAINT zonas_ctr_id_zona_fkey FOREIGN KEY (id_zona) REFERENCES $nom_tabla(id_zona) ON DELETE CASCADE; ";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function llenar_zonas_grupos(): void
    {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');

        $datosTabla = $this->infoTable("zonas_grupos");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_grupo, nombre_grupo, orden";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        if ($oDbl === null) {
            throw new \RuntimeException(_('No hay conexión a la base de datos para importar zonas_grupos.'));
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function llenar_zonas_sacd(): void
    {
        $this->addPermisoGlobal('comun');
        $this->setConexion('comun');

        $datosTabla = $this->infoTable("zonas_sacd");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_item, id_nom, id_zona, propia";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        if ($oDbl === null) {
            throw new \RuntimeException(_('No hay conexión a la base de datos para importar zonas_sacd.'));
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
}
