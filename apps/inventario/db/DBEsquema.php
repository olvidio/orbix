<?php

namespace inventario\db;

use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de [global] En este caso public
 */
class DBEsquema extends DBAbstract
{

    private $dir_base = ConfigGlobal::DIR . "/apps/inventario/db";

    public function __construct($esquema_sfsv = NULL)
    {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        //$this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->esquema = $esquema_sfsv;
        $this->vf = substr($esquema_sfsv, -1); // solo la v o la f.
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll()
    {
        $this->eliminar_whereis();
        $this->eliminar_ubis();
        $this->eliminar_tipo_documento();
        $this->eliminar_lugares();
        $this->eliminar_egm();
        $this->eliminar_equipajes();
        $this->eliminar_documentos();
        $this->eliminar_colecciones();
    }

    public function createAll()
    {
        $this->create_colecciones();
        $this->create_documentos();
        $this->create_equipajes();
        $this->create_egm();
        $this->create_lugares();
        $this->create_tipo_documento();
        $this->create_ubis();
        $this->create_whereis();
    }

    public function llenarAll()
    {
        $this->llenar_colecciones();
        $this->llenar_documentos();
        $this->llenar_equipajes();
        $this->llenar_egm();
        $this->llenar_lugares();
        $this->llenar_tipo_documento();
        $this->llenar_ubis();
        $this->llenar_whereis();
    }

    private function infoTable($tabla)
    {
        $datosTabla = [];
        switch ($tabla) {
            case "i_colecciones":
                $datosTabla['tabla'] = "i_colecciones_dl";
                $nom_tabla = $this->getNomTabla("i_colecciones_dl");
                $campo_seq = 'id_coleccion';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_documentos":
                $datosTabla['tabla'] = "i_documentos_dl";
                $nom_tabla = $this->getNomTabla("i_documentos_dl");
                $campo_seq = 'id_doc';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_egm":
                $datosTabla['tabla'] = "i_egm_dl";
                $nom_tabla = $this->getNomTabla("i_egm_dl");
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_equipajes":
                $datosTabla['tabla'] = "i_equipajes_dl";
                $nom_tabla = $this->getNomTabla("i_equipajes_dl");
                $campo_seq = 'id_equipaje';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_lugares":
                $datosTabla['tabla'] = "i_lugares_dl";
                $nom_tabla = $this->getNomTabla("i_lugares_dl");
                $campo_seq = 'id_lugar';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_tipo_documento":
                $datosTabla['tabla'] = "i_tipo_documento_dl";
                $nom_tabla = $this->getNomTabla("i_tipo_documento_dl");
                $campo_seq = 'id_tipo_doc';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_ubis":
                $datosTabla['tabla'] = "i_ubis_dl";
                $nom_tabla = $this->getNomTabla("i_ubis_dl");
                $campo_seq = 'id_ubi';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
            case "i_whereis":
                $datosTabla['tabla'] = "i_whereis_dl";
                $nom_tabla = $this->getNomTabla("i_whereis_dl");
                $campo_seq = 'id_item_whereis';
                $id_seq = $nom_tabla . "_" . $campo_seq . "_seq";
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";
        return $datosTabla;
    }

    public function create_colecciones()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_colecciones";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_coleccion); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_colecciones()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_colecciones");

        $nom_tabla = $datosTabla['nom_tabla'];
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_documentos()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_documentos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_doc); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";

        $a_sql[] = "CREATE INDEX {$tabla}_id_lugar_key ON $nom_tabla USING btree (id_lugar); ";
        $a_sql[] = "CREATE INDEX {$tabla}_id_tipo_doc ON $nom_tabla USING btree (id_tipo_doc); ";
        $a_sql[] = "CREATE INDEX {$tabla}_id_ubi ON $nom_tabla USING btree (id_ubi); ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_documentos()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_documentos");

        $nom_tabla = $datosTabla['nom_tabla'];
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_egm()
    {
        // (debe estar después de crear la de equipajes)
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_egm";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT egm_equipaje_grupo UNIQUE (id_equipaje, id_grupo);";

        $a_sql[] = "CREATE INDEX {$tabla}_id_equipaje_id_grup ON $nom_tabla USING btree (id_equipaje, id_grupo); ";

        $datosTablaA = $this->infoTable('i_equipajes');
        $nom_tabla_equipajes = $datosTablaA['nom_tabla'];
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT egm_id_equipaje_fk 
                    FOREIGN KEY (id_equipaje) REFERENCES $nom_tabla_equipajes(id_equipaje) ON DELETE CASCADE; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_egm()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_egm");

        $nom_tabla = $datosTabla['nom_tabla'];
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_equipajes()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_equipajes";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_equipaje); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_equipajes()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_equipajes");

        $nom_tabla = $datosTabla['nom_tabla'];
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_lugares()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_lugares";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_lugar); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_lugares()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_lugares");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_tipo_documento()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_tipo_documento";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_tipo_doc); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_tipo_documento()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_tipo_documento");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_ubis()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_ubis";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_ubi); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT ubis_nom_ubi UNIQUE (nom_ubi); ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_ubis()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_ubis");

        $nom_tabla = $datosTabla['nom_tabla'];
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }

    public function create_whereis()
    {
        // (se debe crear después documentos y egm)
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $tabla = "i_whereis";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        $campo_seq = $datosTabla['campo_seq'];

        $nom_tabla_parent = 'global';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                        INCREMENT BY 1
                        MINVALUE 1
                        MAXVALUE 9223372036854775807
                        START WITH 1
                        NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role_vf;";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT nextval('$id_seq'::regclass); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item_whereis); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role_vf; ";
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT whereis_id_doc_id_egm UNIQUE (id_item_egm, id_doc); ";

        $datosTablaA = $this->infoTable('i_documentos');
        $nom_tabla_documentos = $datosTablaA['nom_tabla'];
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT whereis_id_doc_fkey 
                FOREIGN KEY (id_doc) REFERENCES $nom_tabla_documentos(id_doc) ON DELETE CASCADE; ";

        $datosTablaA = $this->infoTable('i_egm');
        $nom_tabla_egm = $datosTablaA['nom_tabla'];
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla ADD CONSTRAINT whereis_id_item_egm_fk
                FOREIGN KEY (id_item_egm) REFERENCES $nom_tabla_egm(id_item) ON DELETE CASCADE; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function eliminar_whereis()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_whereis");

        $nom_tabla = $datosTabla['nom_tabla'];
        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv');
    }


    /* ###################### LLENAR TABLAS ################################ */

    public function llenar_colecciones()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_colecciones");

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
        $fields = "id_coleccion, nom_coleccion, agrupar";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_documentos()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_documentos");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY CASCADE;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_doc, id_tipo_doc, id_ubi, id_lugar, f_recibido, f_asignado, observ, f_ult_comprobacion,
         en_busqueda, perdido, f_perdido, eliminado, f_eliminado, num_reg, num_ini, num_fin, identificador, num_ejemplares";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_egm()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_egm");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY CASCADE;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_equipaje, id_grupo, id_lugar, id_item, texto";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_equipajes()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_equipajes");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY CASCADE;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_equipaje, ids_activ, lugar, f_ini, f_fin, id_ubi_activ, nom_equipaje, cabecera, pie, cabecerab";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_lugares()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_lugares");

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
        $fields = "id_lugar, id_ubi, nom_lugar";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_tipo_documento()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_tipo_documento");

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
        $fields = "id_tipo_doc, nom_doc, sigla, observ, id_coleccion, bajo_llave, vigente, numerado";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_ubis()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_ubis");

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
        $fields = "id_ubi, nom_ubi, id_ubi_activ";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }

    public function llenar_whereis()
    {
        $this->addPermisoGlobal('sfsv');

        $datosTabla = $this->infoTable("i_whereis");

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;

        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY CASCADE;";
        $this->executeSql($a_sql);

        $delimiter = "\t";
        $null_as = "\\\\N";
        $fields = "id_item_whereis, id_item_egm, id_doc";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"), $filename);
            exit ($msg);
        }

        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv');
    }


}