<?php
namespace procesos\db;
use core\ConfigGlobal;
use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract {

    public function __construct($esquema_sfsv=NULL) {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv,0,-1); // quito la v o la f.
        $this->role = '"'. $this->esquema .'"';
    }
    
    public function dropAll() {
        $this->eliminar_a_actividad_proceso();
        $this->eliminar_a_tipos_proceso();
        $this->eliminar_a_procesos();
        $this->eliminar_a_fases();
        $this->eliminar_a_tareas();
        $this->eliminar_aux_usuarios_perm();
    }
    
    public function createAll() {
        $this->create_a_actividad_proceso();
        $this->create_a_tipos_procesos();
        $this->create_a_tareas();
        $this->create_a_fases();
        $this->create_a_procesos();
        $this->create_aux_usuarios_perm();
    }
    
    public function llenarAll() {
        $this->llenar_a_actividad_proceso();
        $this->llenar_a_tipos_procesos();
        $this->llenar_a_tareas();
        $this->llenar_a_fases();
        $this->llenar_a_procesos();
        $this->llenar_aux_usuarios_perm();
    }
    
    private function infoTable($tabla) {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "a_actividad_proceso":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_tipos_proceso":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_tipo_proceso';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_tareas":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_tarea';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_fases":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_fase';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "a_procesos":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
            case "aux_usuarios_perm":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_item';
                $id_seq = $nom_tabla."_".$campo_seq."_seq";
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        return $datosTabla;
    }
    
    
    /**
     * En la BD Comun.
     * Tiene un foreing key con el id_activ. Entiendo que no hay problemas con sf, ya
     * los procesoso podrian ser distintos, pero no interfieren los ids.
     */
    public function create_a_actividad_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_actividad_proceso";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER completado SET DEFAULT false;";
        
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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_tipo_proceso_key
                    UNIQUE (id_tipo_proceso, id_activ, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        // No va con tablas heredadas
        //$a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_actividad_proceso_id_activ_fk
        //            FOREIGN KEY (id_activ) REFERENCES public.a_actividades_all(id_activ) ON DELETE CASCADE; ";
        
        $a_sql[] = "CREATE INDEX a_actividad_proceso_n_orden ON $nom_tabla USING btree (n_orden); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        /*
        $a_sql[] = "GRANT DELETE ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT INSERT ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT REFERENCES ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT SELECT ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT TRIGGER ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT TRUNCATE ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        $a_sql[] = "GRANT UPDATE ON $nom_tabla TO $this->role WITH GRANT OPTION; ";
        */
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_actividad_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_actividad_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_tipos_procesos() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tipos_proceso";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_tipo_proceso); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tipos_proceso() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tipos_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
    
    public function create_a_tareas() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

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
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_id_tarea_key ON $nom_tabla USING btree (id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_tareas() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tareas");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_fases() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_fases";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        //secuencia
        $a_sql[] = "CREATE SEQUENCE IF NOT EXISTS $id_seq;";
        $a_sql[] = "ALTER SEQUENCE $id_seq
                    INCREMENT BY 1
                    MINVALUE 1
                    MAXVALUE 9223372036854775807
                    START WITH 1
                    NO CYCLE;";
        $a_sql[] = "ALTER SEQUENCE $id_seq OWNER TO $this->role;";
        
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER $campo_seq SET DEFAULT (10 * nextval('$id_seq'::regclass)); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sf SET DEFAULT false; ";
        $a_sql[] = "ALTER TABLE $nom_tabla ALTER sv SET DEFAULT true; ";
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_fase); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_fases() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_fases");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_procesos() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_procesos";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

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
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_procesos_ukey
                        UNIQUE (id_tipo_proceso, id_fase, id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        
        $a_sql[] = "CREATE UNIQUE INDEX ${tabla}_idx ON $nom_tabla USING btree (id_tipo_proceso, id_fase, id_tarea); ";
            
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }
    public function eliminar_a_procesos() {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_procesos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $tabla = "aux_usuarios_perm";
        $datosTabla = $this->infoTable($tabla);
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

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
        
        
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        
        $a_sql[] = "CREATE INDEX ${tabla}_id_fase_fin ON $nom_tabla USING btree (id_fase_fin); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_fase_ini ON $nom_tabla USING btree (id_fase_ini); ";
        $a_sql[] = "CREATE INDEX ${tabla}_id_usuario ON $nom_tabla USING btree (id_usuario); ";
        $a_sql[] = "CREATE INDEX ${tabla}_tipo_activ ON $nom_tabla USING btree (id_tipo_activ_txt); ";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        $this->executeSql($a_sql);
        
        $this->delPermisoGlobal('svsf');
    }
    public function eliminar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('svsf');
        
        $datosTabla = $this->infoTable("aux_usuarios_perm");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];
        
        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;" ;
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('svsf');
    }

    /* ###################### LLENAR TABLAS ################################ */
    public function llenar_a_actividad_proceso(){
        // empty;
    }
    public function llenar_a_tipos_procesos() {
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_tipos_proceso");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $rows = [];
        $delimiter = "\t"; 
        $null_as = "NULL";
        $fields = "id_tipo_proceso, nom_proceso";
        
        $rows[] = "3	ca n";
        $rows[] = "4	general";
        $rows[] = "5	general no dlb";
        $rows[] = "6	ca no dl";
        
        $oDbl->pgsqlCopyFromArray($nom_tabla, $rows, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
    }
    public function llenar_a_tareas() {
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_tareas");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $oDbl = $this->oDbl;
        
        // Borrar los posibles datos de la tabla
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $rows = [];
        $delimiter = "\t"; 
        $null_as = "NULL";
        $fields = "id_fase, id_tarea, desc_tarea";

        $rows[] = "10	1	propuesta";
        $rows[] = "20	3	calendario, ok oficina";
        $rows[] = "30	2	calendario, comprobación";
        $rows[] = "40	7	ok a todo";
        $rows[] = "50	10	propuesta sacd";
        $rows[] = "60	12	ok sacd";
        $rows[] = "110	21	ok asignación de cl";
        $rows[] = "120	24	distribución comentario carta, catecismo...";
        $rows[] = "130	19	ok profesores";
        $rows[] = "160	16	ok asistentes";
        $rows[] = "180	18	envío E43 otras dl";
        $rows[] = "200	20	ok plan de estudio personal";
        $rows[] = "210	22	maleta material de estudios";
        $rows[] = "240	26	vsm/dagd hablar con el cl de ca/cv";
        $rows[] = "250	27	vest hablar con el director de estudios del ca";
        $rows[] = "260	28	ca en marcha";
        $rows[] = "260	25	avisar admon de nº asistentes y sacd";
        $rows[] = "360	23	preajuste des";

        $oDbl->pgsqlCopyFromArray($nom_tabla, $rows, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
        
    }
    public function llenar_a_fases() {
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_fases");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $rows = [];
        $delimiter = "\t"; 
        $null_as = "NULL";
        $fields = "id_fase, desc_fase, sf, sv";

		$rows[] = "10	Crear (proyecto)	True	True";
		$rows[] = "20	ok oficina correspondiente	True	True";
		$rows[] = "30	ok des i tal	False	True";
		$rows[] = "40	aprobado dl	True	True";
		$rows[] = "50	propuesta atención sacd	True	True";
		$rows[] = "60	ok atención sacd	True	True";
		$rows[] = "70	propuesta plan de estudios	False	True";
		$rows[] = "80	propuesta distribución cl	False	True";
		$rows[] = "90	ok plan de estudios	False	True";
		$rows[] = "100	ok distribución de plazas	False	True";
		$rows[] = "110	ok distribución cl	False	True";
		$rows[] = "120	posibles ca para cada ctr	False	True";
		$rows[] = "130	cuadro profesorado	False	True";
		$rows[] = "140	minuta ctr	False	True";
		$rows[] = "160	ok asistentes	False	True";
		$rows[] = "170	confirmación a los ctr	False	True";
		$rows[] = "180	envio E43 a otras dl	False	True";
		$rows[] = "190	ok profesores	False	True";
		$rows[] = "200	ok plan de estudios personal	False	True";
		$rows[] = "210	maleta material de estudios	False	True";
		$rows[] = "220	ok distribución cl	False	True";
		$rows[] = "230	distribución cfi	False	True";
		$rows[] = "240	reunión cl ca con d dl	False	True";
		$rows[] = "250	reunión d.est con vest	False	True";
		$rows[] = "260	actividad en marcha	False	True";
		$rows[] = "270	activiadad finalizada	False	True";
		$rows[] = "280	ok actas y notas	False	True";
		$rows[] = "290	ok E43 enviados	False	True";
		$rows[] = "300	ok E43 recibidos	False	True";
		$rows[] = "310	guardar historial	False	True";
		$rows[] = "320	Eliminada (borrable)	True	True";
		$rows[] = "330	Aprobada (actual)	True	True";
		$rows[] = "340	Terminada	True	True";
		$rows[] = "360	Crear inicial des (proyecto)	True	True";

        $oDbl->pgsqlCopyFromArray($nom_tabla, $rows, $delimiter, $null_as, $fields);
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq)/10 FROM $nom_tabla) )";
        $this->executeSql($a_sql);
    }
    public function llenar_a_procesos() {
        $this->setConexion('comun');
        $datosTabla = $this->infoTable("a_procesos");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $rows = [];
        $delimiter = "\t"; 
        $null_as = "NULL";
        $fields = "id_item, id_tipo_proceso, n_orden, id_fase, id_tarea, status, of_responsable, id_fase_previa, id_tarea_previa, mensaje_requisito";

		$rows[] = "1	3	1	10	0	1	des	10	NULL	NULL";
		$rows[] = "6	3	4	50	10	2	des	40	7	Actividad todavia no aprobada y tal";
		$rows[] = "7	4	3	10	1	1	des	NULL	NULL	no hay";
		$rows[] = "8	4	5	50	10	2	des	10	NULL	aaaa";
		$rows[] = "13	3	8	160	0	2	est	600	NULL	NULL";
		$rows[] = "14	4	4	330	0	2	des	100	NULL	NULL";
		$rows[] = "15	4	10	60	0	2	des	50	NULL	debe proponer la atn sacd";
		$rows[] = "16	4	20	340	0	3	des	NULL	NULL	NULL";
		$rows[] = "17	3	2	330	0	2	des	NULL	NULL	NULL";
		$rows[] = "18	3	9	340	0	3	est	NULL	NULL	NULL";
		$rows[] = "19	3	5	60	12	2	des	50	NULL	rews";
		$rows[] = "20	5	1	330	0	2	est	NULL	NULL	NULL";
		$rows[] = "21	5	2	340	0	3	est	NULL	NULL	NULL";
		$rows[] = "23	6	1	330	0	2	est	NULL	NULL	NULL";
		$rows[] = "24	6	2	180	18	2	est	NULL	NULL	NULL";
		$rows[] = "25	6	3	300	0	2	est	NULL	NULL	NULL";
		$rows[] = "26	6	4	340	0	3	est	NULL	NULL	NULL";

        $oDbl->pgsqlCopyFromArray($nom_tabla, $rows, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
    }
    
    public function llenar_aux_usuarios_perm() {
        // OJO Corresponde al esquema sf/sv, no al comun.
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"'. $this->esquema .'"';
        // (debe estar después de fijar el role)
        $this->setConexion('svsf');

        $datosTabla = $this->infoTable("aux_usuarios_perm");
        
        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];
        $id_seq = $datosTabla['id_seq'];
        $oDbl = $this->oDbl;
        
        $a_sql = [];
        $a_sql[0] = "TRUNCATE $nom_tabla RESTART IDENTITY;" ;
        $this->executeSql($a_sql);
        
        $rows = [];
        $delimiter = "\t"; 
        $null_as = "NULL";
        $fields = "id_item, id_usuario, id_tipo_activ_txt, id_fase_ini, id_fase_fin, accion, afecta_a, dl_propia, id_fases";
        
		$rows[] = "26	434	2.....	10	340	3	1	t	NULL";
		$rows[] = "27	434	1.....	10	340	1	1	t	NULL";
		$rows[] = "32	538	1.....	10	340	3	1	t	NULL";
		$rows[] = "37	540	2.....	10	340	3	4	t	NULL";
		$rows[] = "41	446	1.....	10	340	7	15	t	NULL";
		$rows[] = "42	446	2.....	10	340	3	15	t	NULL";
		$rows[] = "43	451	2.....	10	340	3	15	t	NULL";
		$rows[] = "196	539	13....	330	340	3	29	t	NULL";
		$rows[] = "131	4120	......	10	340	3	15	t	NULL";
		$rows[] = "132	4121	......	10	340	3	15	t	NULL";
		$rows[] = "49	560	1.....	10	340	1	1	t	NULL";
		$rows[] = "51	461	......	10	340	3	15	t	NULL";
		$rows[] = "250	584	11....	10	330	31	9	t	NULL";
		$rows[] = "129	586	1.....	10	340	31	18	t	NULL";
		$rows[] = "58	566	1.....	10	340	3	1	t	NULL";
		$rows[] = "59	566	2.....	330	340	1	1	t	NULL";
		$rows[] = "84	540	2.....	10	340	1	1	t	NULL";
		$rows[] = "38	541	1.....	10	340	3	31	t	NULL";
		$rows[] = "65	572	1.....	10	340	3	9	t	NULL";
		$rows[] = "89	559	1.....	10	340	1	1	t	NULL";
		$rows[] = "136	5124	1.....	10	340	3	18	t	NULL";
		$rows[] = "135	5124	1.....	10	340	1	1	t	NULL";
		$rows[] = "137	5124	2.....	10	340	3	9	t	NULL";
		$rows[] = "138	5124	2.....	10	340	31	18	t	NULL";
		$rows[] = "99	541	2.....	10	340	3	20	t	NULL";
		$rows[] = "98	541	2.....	10	340	1	9	t	NULL";
		$rows[] = "232	5158	2.....	330	340	3	25	t	NULL";
		$rows[] = "48	559	2.....	10	340	3	9	t	NULL";
		$rows[] = "140	5125	1.....	10	340	3	3	t	NULL";
		$rows[] = "145	5130	1.....	10	340	1	1	t	NULL";
		$rows[] = "213	577	2.....	330	340	3	4	f	NULL";
		$rows[] = "130	497	......	10	340	3	15	t	NULL";
		$rows[] = "260	5158	1.....	330	340	1	1	t	NULL";
		$rows[] = "251	497	112...	10	340	3	127	t	NULL";
		$rows[] = "159	559	2.....	330	340	3	9	f	NULL";
		$rows[] = "150	541	1.....	330	340	3	31	f	NULL";
		$rows[] = "149	4129	134...	330	330	3	1	f	NULL";
		$rows[] = "162	5124	2.....	330	340	3	9	f	NULL";
		$rows[] = "171	497	......	330	340	3	15	f	NULL";
		$rows[] = "203	4109	......	330	340	3	15	f	NULL";
		$rows[] = "204	4120	......	330	340	3	15	f	NULL";
		$rows[] = "177	4143	......	330	340	3	11	f	NULL";
		$rows[] = "249	541	112...	160	340	3	100	t	NULL";
		$rows[] = "224	443	1.....	330	340	15	31	f	NULL";
		$rows[] = "255	575	112...	10	340	15	127	t	NULL";
		$rows[] = "259	4138	1.....	330	340	3	77	f	NULL";
		$rows[] = "173	5140	2.....	330	340	3	15	t	NULL";
		$rows[] = "244	5168	2.....	60	340	3	9	t	NULL";
		$rows[] = "228	581	......	10	340	3	2	t	NULL";
		$rows[] = "195	5130	2.....	330	340	3	4	f	NULL";
		$rows[] = "189	5130	2.....	60	340	3	4	t	NULL";
		$rows[] = "188	5130	2.....	330	50	NULL	4	t	NULL";
		$rows[] = "187	5130	2.....	330	340	3	9	t	NULL";
		$rows[] = "234	5163	1.....	10	340	1	1	t	NULL";
		$rows[] = "236	5163	2.....	330	340	3	9	f	NULL";
		$rows[] = "237	4165	1.....	330	340	3	1	t	NULL";
		$rows[] = "180	4144	2.....	10	340	3	11	t	NULL";
		$rows[] = "184	4144	2.....	60	340	3	4	t	NULL";
		$rows[] = "238	443	1.....	10	340	15	32	t	NULL";
		$rows[] = "146	4138	1.....	330	340	3	45	t	NULL";
		$rows[] = "239	4138	2.....	330	340	3	13	t	NULL";
		$rows[] = "242	5168	1.....	60	340	3	73	t	NULL";
		$rows[] = "246	5168	1.....	330	340	3	65	f	NULL";
		$rows[] = "185	577	2.....	60	340	3	4	t	NULL";
		$rows[] = "168	577	1.....	10	340	3	3	t	NULL";
		$rows[] = "197	585	14....	330	340	3	13	t	NULL";
		$rows[] = "199	585	15....	330	340	3	13	t	NULL";
		$rows[] = "264	541	2.....	330	340	1	1	f	NULL";
		$rows[] = "201	588	112...	330	340	3	15	t	NULL";
		$rows[] = "202	588	133...	330	340	3	15	t	NULL";
		$rows[] = "200	4109	......	10	340	3	15	t	NULL";
		$rows[] = "30	537	2.....	330	340	3	25	t	NULL";
		$rows[] = "241	443	1.....	330	340	7	15	t	NULL";
		$rows[] = "247	541	1.....	330	340	3	96	t	NULL";
		$rows[] = "265	537	1.....	10	340	3	25	t	NULL";
		$rows[] = "186	584	11....	330	340	3	109	t	NULL";
		$rows[] = "261	537	......	330	340	3	25	f	NULL";
		$rows[] = "248	577	2.....	330	340	31	2	t	NULL";
		$rows[] = "263	537	......	330	340	31	2	f	NULL";
		$rows[] = "82	577	2.....	330	340	3	25	t	NULL";
		$rows[] = "31	537	1.....	330	340	31	2	t	NULL";
		$rows[] = "266	537	2.....	330	340	31	2	t	NULL";
		$rows[] = "254	541	112...	10	340	3	9	t	NULL";
		$rows[] = "176	4143	......	10	340	3	11	t	NULL";
		$rows[] = "211	577	1.....	10	340	NULL	12	t	NULL";
		$rows[] = "56	563	2.....	10	330	31	1	t	NULL";
		$rows[] = "66	572	1.....	10	340	31	2	t	NULL";
		$rows[] = "67	574	......	10	340	31	15	t	NULL";
		$rows[] = "96	454	23....	10	330	31	1	t	NULL";
		$rows[] = "73	468	2.....	10	340	31	15	t	NULL";
		$rows[] = "33	538	11....	10	340	31	10	t	NULL";
		$rows[] = "75	538	11....	10	330	31	11	t	NULL";
		$rows[] = "81	577	2.....	10	10	31	11	t	NULL";
		$rows[] = "77	539	13....	330	340	31	2	t	NULL";
		$rows[] = "114	584	11....	330	340	31	2	t	NULL";
		$rows[] = "120	585	14....	330	340	31	2	t	NULL";
		$rows[] = "121	585	15....	330	340	31	2	t	NULL";
		$rows[] = "143	5125	2.....	10	340	31	2	t	NULL";
		$rows[] = "163	5124	2.....	330	340	31	18	f	NULL";
		$rows[] = "113	584	112...	10	340	31	11	t	NULL";
		$rows[] = "252	497	133...	10	340	3	127	t	NULL";
		$rows[] = "216	5147	25....	10	340	3	27	t	NULL";
		$rows[] = "218	5149	27....	10	340	3	27	t	NULL";
		$rows[] = "220	5149	29....	10	340	3	27	t	NULL";
		$rows[] = "222	5150	222...	10	340	3	25	t	NULL";
		$rows[] = "142	5125	2.....	330	340	3	9	t	NULL";
		$rows[] = "70	575	......	10	340	31	127	t	NULL";
		$rows[] = "258	577	112...	10	340	3	19	t	NULL";
		$rows[] = "172	5140	2.....	10	60	31	11	t	NULL";
		$rows[] = "118	585	14....	10	10	31	11	t	NULL";
		$rows[] = "119	585	15....	10	10	31	11	t	NULL";
		$rows[] = "125	587	17....	10	10	31	11	t	NULL";
		$rows[] = "127	588	112...	10	10	31	1	t	NULL";
		$rows[] = "128	588	133...	10	10	31	1	t	NULL";
		$rows[] = "141	5125	2.....	10	10	31	9	t	NULL";
		$rows[] = "80	539	13....	10	10	31	27	t	NULL";
		$rows[] = "164	5130	2.....	10	340	3	9	t	NULL";
		$rows[] = "167	575	......	330	340	31	15	f	NULL";
		$rows[] = "153	585	15....	330	340	31	11	f	NULL";
		$rows[] = "154	585	14....	330	340	31	11	f	NULL";
		$rows[] = "155	587	17....	330	340	31	11	f	NULL";
		$rows[] = "156	588	112...	330	340	31	1	f	NULL";
		$rows[] = "157	588	133...	330	340	31	1	f	NULL";
		$rows[] = "160	577	2.....	330	340	31	11	f	NULL";
		$rows[] = "151	539	13....	330	340	31	27	f	NULL";
		$rows[] = "152	584	11....	330	340	31	11	f	NULL";
		$rows[] = "212	443	1.....	10	340	3	16	t	NULL";
		$rows[] = "233	5159	25....	330	340	7	25	t	NULL";
		$rows[] = "235	5163	2.....	10	340	3	9	t	NULL";
		$rows[] = "158	581	......	330	340	31	31	f	NULL";
		$rows[] = "227	581	......	10	340	31	29	t	NULL";
		$rows[] = "243	5168	1.....	60	340	3	4	t	NULL";
		$rows[] = "245	5168	2.....	60	340	3	4	t	NULL";
		$rows[] = "161	5123	23....	10	340	3	27	t	NULL";
		$rows[] = "267	5123	23....	60	340	3	4	t	NULL";
		$rows[] = "214	5146	21....	10	340	3	27	t	NULL";
		$rows[] = "268	5146	21....	60	340	3	4	t	NULL";
		$rows[] = "215	5147	24....	10	340	3	27	t	NULL";
		$rows[] = "269	5147	24....	60	340	3	4	t	NULL";
		$rows[] = "270	5147	25....	60	340	3	4	t	NULL";
		$rows[] = "217	5148	22....	10	340	3	27	t	NULL";
		$rows[] = "271	5148	22....	60	340	3	4	t	NULL";
		$rows[] = "219	5149	28....	10	340	3	27	t	NULL";
		$rows[] = "272	5149	27....	60	340	3	4	t	NULL";
		$rows[] = "273	5149	28....	60	340	3	4	t	NULL";
		$rows[] = "274	5149	29....	60	340	3	4	t	NULL";
		$rows[] = "221	5150	212...	10	340	3	25	t	NULL";
		$rows[] = "223	5150	233...	10	340	3	25	t	NULL";
		$rows[] = "275	5150	222...	60	340	3	4	t	NULL";
		$rows[] = "276	5150	212...	60	340	3	4	t	NULL";
		$rows[] = "277	5150	233...	60	340	3	4	t	NULL";
		$rows[] = "278	5125	2.....	60	340	3	4	t	NULL";
		$rows[] = "165	5125	2.....	330	340	31	15	f	NULL";
		$rows[] = "279	5419	1.....	330	340	3	9	t	NULL";
		$rows[] = "280	5419	2.....	330	340	1	1	t	NULL";
		$rows[] = "281	5419	1.....	330	340	3	1	f	NULL";
		$rows[] = "282	5419	2.....	330	340	1	1	f	NULL";
		$rows[] = "126	587	17....	330	340	7	105	t	NULL";
		$rows[] = "283	587	17....	330	340	3	125	t	NULL";
		$rows[] = "289	4423	......	330	340	3	109	f	NULL";

        $oDbl->pgsqlCopyFromArray($nom_tabla, $rows, $delimiter, $null_as, $fields);
        
        // Fix sequences
        $a_sql[0] = "SELECT SETVAL('$id_seq', (SELECT MAX($campo_seq) FROM $nom_tabla) )";
        $this->executeSql($a_sql);
    }

 }