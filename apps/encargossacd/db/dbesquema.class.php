<?php
namespace encargossacd\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    private $dir_base = ConfigGlobal::DIR."/apps/encargossacd/db";
    
    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
        $this->role_vf = '"'. $esquema_sfsv .'"';
    }
    
    public function dropAll() {
        $this->eliminar_encargos_sacd();
        $this->eliminar_encargo_sacd_observ();
        $this->eliminar_encargo_sacd_horario_excepcion();
        $this->eliminar_encargo_sacd_horario();
        $this->eliminar_encargo_horario_excepcion();
        $this->eliminar_encargo_horario();
        $this->eliminar_encargos();
        $this->eliminar_encargo_tipo();
        $this->eliminar_encargo_datos_cgi();
        $this->eliminar_encargo_textos();
    }
    
    public function createAll() {
        $this->create_encargo_tipo();
        $this->create_encargos();
        $this->create_encargos_sacd(); // debe ir antes de los horarios foreign key
        $this->create_encargo_horario();
        $this->create_encargo_horario_excepcion();
        $this->create_encargo_sacd_horario();
        $this->create_encargo_sacd_horario_excepcion();
        $this->create_encargo_sacd_observ();
        $this->create_encargo_datos_cgi();
        $this->create_encargo_textos();
    }
    
    public function llenarAll() {
        $this->llenar_encargo_tipo();
        $this->llenar_encargo_textos();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
			case "encargo_tipo":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
			case "encargos":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_enc';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_horario":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_h';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_horario_excepcion":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_ex';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_sacd_horario":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_sacd_horario_excepcion":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item_ex';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_sacd_observ":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargos_sacd":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_datos_cgi":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
				$id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
			case "encargo_textos":
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
    public function create_encargo_tipo() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_tipo";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_tipo_ukey
                    UNIQUE (id_tipo_enc); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_tipo_enc); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_tipo() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_tipo");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }

    public function create_encargos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargos";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargos_ukey
                    UNIQUE (id_enc); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }
    
    public function create_encargo_horario() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_horario";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_horario_ukey
                    UNIQUE (id_enc, id_item_h); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item_h); ";
        
        $a_sql[] = "CREATE INDEX encargo_horario_f_fin_idx ON $nom_tabla USING btree (f_fin); ";

        $a_sql[] = "CREATE INDEX encargo_horario_f_ini_idx ON $nom_tabla USING btree (f_ini); ";

        $a_sql[] = "CREATE UNIQUE INDEX encargo_horario_id_item_idx ON $nom_tabla USING btree (id_item_h); ";

        $nom_tabla_ref = $this->getNomTabla("encargos");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
            ADD CONSTRAINT encargo_horario_id_enc_fkey FOREIGN KEY (id_enc) REFERENCES $nom_tabla_ref(id_enc) ON DELETE CASCADE; ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_horario() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_horario");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }
    
    public function create_encargo_horario_excepcion() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_horario_excepcion";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_horario_ex_ukey
                    UNIQUE (id_enc, id_item_ex); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item_ex); ";
        
        $a_sql[] = "CREATE INDEX encargo_horario_ex_f_fin_idx ON $nom_tabla USING btree (f_fin); ";

        $a_sql[] = "CREATE INDEX encargo_horario_ex_f_ini_idx ON $nom_tabla USING btree (f_ini); ";

        $a_sql[] = "CREATE UNIQUE INDEX encargo_horario_ex_id_item_idx ON $nom_tabla USING btree (id_item_ex); ";

        $nom_tabla_ref = $this->getNomTabla("encargos");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
            ADD CONSTRAINT encargo_horario_ex_id_enc_fkey FOREIGN KEY (id_enc) REFERENCES $nom_tabla_ref(id_enc) ON DELETE CASCADE; ";
        
        $nom_tabla_ref = $this->getNomTabla("encargo_horario");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
        ADD CONSTRAINT encargo_horario_ex_id_item_h_fk FOREIGN KEY (id_item_h) REFERENCES $nom_tabla_ref(id_item_h) ON DELETE CASCADE; ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_horario_excepcion() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_horario_excepcion");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }

    public function create_encargo_sacd_horario() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_sacd_horario";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla
            ADD CONSTRAINT encargo_sacd_horario_ukey UNIQUE (id_enc, id_item, id_nom); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item, id_nom); ";
        
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_f_fin_idx ON $nom_tabla USING btree (f_fin); ";

        $a_sql[] = "CREATE INDEX encargo_sacd_horario_f_ini_idx ON $nom_tabla USING btree (f_ini); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_id_enc_idx ON $nom_tabla USING btree (id_enc); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_id_nom_idx ON $nom_tabla USING btree (id_nom ); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_sacd_horario_id_item_idx ON $nom_tabla USING btree (id_item); ";
        
        $nom_tabla_ref = $this->getNomTabla("encargos_sacd");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
            ADD CONSTRAINT encargo_sacd_horario_id_item_tarea_sacd_fkey FOREIGN KEY (id_item_tarea_sacd) REFERENCES $nom_tabla_ref(id_item) ON DELETE CASCADE; ";

        $nom_tabla_ref = $this->getNomTabla("encargos");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
            ADD CONSTRAINT encargo_sacd_horario_id_enc_fkey FOREIGN KEY (id_enc) REFERENCES $nom_tabla_ref(id_enc) ON DELETE CASCADE; ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_sacd_horario() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_sacd_horario");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }
    
    public function create_encargo_sacd_horario_excepcion() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_sacd_horario_excepcion";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_sacd_horario_ex_ukey
                    UNIQUE (id_enc, id_item_ex); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc, id_item_ex); ";
        
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_ex_f_fin_idx ON $nom_tabla USING btree (f_fin); ";

        $a_sql[] = "CREATE INDEX encargo_sacd_horario_ex_f_ini_idx ON $nom_tabla USING btree (f_ini); ";

        $a_sql[] = "CREATE UNIQUE INDEX encargo_sacd_horario_ex_id_item_idx ON $nom_tabla USING btree (id_item_ex); ";

        $nom_tabla_ref = $this->getNomTabla("encargos");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
            ADD CONSTRAINT encargo_sacd_horario_ex_id_enc_fkey FOREIGN KEY (id_enc) REFERENCES $nom_tabla_ref(id_enc) ON DELETE CASCADE; ";
        
        $nom_tabla_ref = $this->getNomTabla("encargo_sacd_horario");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
        ADD CONSTRAINT encargo_sacd_horario_ex_id_item_h_fk FOREIGN KEY (id_item_h) REFERENCES $nom_tabla_ref(id_item) ON DELETE CASCADE; ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_sacd_horario_excepcion() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_sacd_horario_excepcion");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }
    
    public function create_encargo_sacd_observ() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_sacd_observ";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_sacd_observ_ukey
                    UNIQUE ($campo_seq); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_sacd_observ_id_nom_idx ON $nom_tabla USING btree (id_nom); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_sacd_observ() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_sacd_observ");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }

    public function create_encargos_sacd() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargos_sacd";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargos_sacd_ukey
                    UNIQUE ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargos_sacd_id_enc_ukey
                    UNIQUE (id_enc, id_nom, modo, f_ini); ";
        
        $nom_tabla_ref = $this->getNomTabla("encargos");
        $a_sql[] = "ALTER TABLE ONLY $nom_tabla
            ADD CONSTRAINT encargos_sacd_id_enc_fkey FOREIGN KEY (id_enc) REFERENCES $nom_tabla_ref(id_enc) ON DELETE CASCADE; ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargos_sacd() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargos_sacd");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }

    public function create_encargo_datos_cgi() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_datos_cgi";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_datos_cgi_pkey
                    UNIQUE ($campo_seq); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_datos_cgi_ukey
                    UNIQUE (id_ubi,curso_ini_any); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX encargo_datos_cgi_id_ubi_idx ON $nom_tabla USING btree (id_ubi); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_datos_cgi() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_datos_cgi");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }

    public function create_encargo_textos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $tabla = "encargo_textos";
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT encargo_textos_ukey
                    UNIQUE (idioma,clave); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_encargo_textos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');

        $datosTabla = $this->infoTable("encargo_textos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }


    /* ###################### LLENAR TABLAS ################################ */
    
    public function llenar_encargo_tipo() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        $this->setConexion('svsf');
        $datosTabla = $this->infoTable("encargo_tipo");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "id_tipo_enc, tipo_enc, mod_horario";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        $this->delPermisoGlobal('svsf');
    }

    public function llenar_encargo_textos() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        $this->setConexion('svsf');
        $datosTabla = $this->infoTable("encargo_textos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $filename = $datosTabla['filename'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $delimiter = "\t"; 
        $null_as = "\\\\N";
        $fields = "idioma, clave, texto";

        // Comprobar que existe el fichero (la ruta esta bien...
        if (!file_exists($filename)) {
            $msg = sprintf(_("no existe el fichero: %s"),$filename);
            exit ($msg);
        }
        
        $oDbl->pgsqlCopyFromFile($nom_tabla, $filename, $delimiter, $null_as, $fields);
        
        $this->delPermisoGlobal('svsf');
    }

 }
