<?php
namespace cambios\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/cambios/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_av_cambios_usuario_propiedades_pref();
        $this->eliminar_av_cambios_usuario_objeto_pref();
        $this->eliminar_av_cambios_usuario();
        $this->eliminar_av_cambios_anotados();
        $this->eliminar_av_cambios_dl();
    }
    
    public function createAll() {
        $this->create_av_cambios_dl();
        $this->create_av_cambios_anotados();
        $this->create_av_cambios_usuario();
        $this->create_av_cambios_usuario_objeto_pref();
        $this->create_av_cambios_usuario_propiedades_pref();
    }
    
    public function llenarAll() {
        //$this->llenar_a_tipos_actividad();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "av_cambios_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_cambio';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_anotados":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_usuario":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_usuario_objeto_pref":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_usuario_objeto';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "av_cambios_usuario_propiedades_pref":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base."/$tabla.csv";
        return $datosTabla;
    }
    
    
    /**
     * En la BD Comun.
     */
    public function create_av_cambios_dl() {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_dl";
        $tabla_padre = "av_cambios";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (public.$tabla_padre);";

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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE INDEX ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_dl() {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_dl");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * En la BD Comun.
     */
    public function create_av_cambios_anotados() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_anotados";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER anotado SET DEFAULT false;";
        
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_udx ON $nom_tabla USING btree (id_schema_cambio,id_item_cambio); ";
        /* No sirve con tablas heredadas
        $tabla1 = 'public.av_cambios'; //la de public
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT av_cambios_anotados_id_item_cambio_fk
                    FOREIGN KEY (id_schema_cambio,id_item_cambio) REFERENCES $tabla1(id_schema,id_item_cambio) ON DELETE CASCADE; ";
        */
        
        $a_sql[] = "CREATE INDEX ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_schema_cambio_idx ON $nom_tabla USING btree (id_schema_cambio); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_item_cambio_idx ON $nom_tabla USING btree (id_item_cambio); ";
        $a_sql[] = "CREATE INDEX ${tabla}_anotado_idx ON $nom_tabla USING btree (anotado); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_anotados() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_anotados");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    public function create_av_cambios_usuario() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_usuario";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER avisado SET DEFAULT false;";
        
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_udx ON $nom_tabla USING btree (id_schema_cambio,id_item_cambio); ";
        // FOREIGN KEYS
        /* No sirve con tablas heredadas
        $tabla1 = 'public.av_cambios'; // la de public.
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT av_cambios_usuario_id_item_cambio_fk
                    FOREIGN KEY (id_schema_cambio,id_item_cambio) REFERENCES $tabla1(id_schema,id_item_cambio) ON DELETE CASCADE; ";
        */
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_idx ON $nom_tabla USING btree (id_item_cambio, id_usuario, aviso_tipo); ";
        $a_sql[] = "CREATE INDEX ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_usuario() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_usuario");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    public function create_av_cambios_usuario_objeto_pref() {
        $mi_dele = ConfigGlobal::mi_dele();
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_usuario_objeto_pref";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER dl_org SET DEFAULT '$mi_dele'";
        
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        // FOREIGN KEYS
        // con los usuarios no va porque estan en otra base de datos (sv). 
        
        $a_sql[] = "CREATE INDEX ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_usuario_objeto_pref() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_usuario_objeto_pref");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    public function create_av_cambios_usuario_propiedades_pref() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "av_cambios_usuario_propiedades_pref";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        // FOREIGN KEYS
        $tabla1 = $this->getNomTabla('av_cambios_usuario_objeto_pref');
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT av_cambios_usuario_propiedades_pref_id_item_usuario_objeto_fk
                    FOREIGN KEY (id_item_usuario_objeto) REFERENCES $tabla1(id_item_usuario_objeto) ON DELETE CASCADE; ";
        
        $a_sql[] = "CREATE INDEX ${tabla}_${campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_av_cambios_usuario_propiedades_pref() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("av_cambios_usuario_propiedades_pref");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
}