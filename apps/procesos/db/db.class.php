<?php
namespace procesos\db;
/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * Cada esquema deberá crear las suyas, heredaas de estas.
 */


class DB {
    private $esquema;
    private $role;
    private $oDbl;

    public function __construct(){
        $this->esquema = 'global';
        $this->role = 'orbix';
        $this->oDbl = $GLOBALS['oDBC'];
    }
    
    public function dropAll() {
        $this->eliminar_a_actividad_proceso();
        $this->eliminar_a_tipos_proceso();
        $this->eliminar_a_procesos();
        $this->eliminar_a_fases();
        $this->eliminar_a_tareas();
    }
    
    public function createAll() {
        $this->create_a_actividad_proceso();
        $this->create_a_tipos_procesos();
        $this->create_a_tareas();
        $this->create_a_fases();
        $this->create_a_procesos();
    }
    
    private function executeSql($a_sql) {
        $oDbl = $this->oDbl;
        
        $oDbl->beginTransaction();
        foreach ($a_sql as $sql) {
            if ($oDbl->exec($sql) === false) {
                $sClauError = 'Procesos.DB.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                $oDbl->rollback();
                return FALSE;
            }
        }
        $oDbl->commit();
    }
    private function eliminar($tabla) {
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        
        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;" ;
        
        return $this->executeSql($a_sql);
    }

    /**
     * En la BD Comun.
     * Tiene un foreing key con el id_activ. Entiendo que no hay problemas con sf, ya 
     * los procesoso podrian ser distintos, pero no interfieren los ids.
     */
    public function create_a_actividad_proceso() {
        $tabla = "a_actividad_proceso";
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                id_item integer NOT NULL,
                id_tipo_proceso integer NOT NULL,
                id_activ integer NOT NULL,
                id_fase integer,
                id_tarea integer,
                n_orden smallint,
                completado boolean,
                observ text
                ); ";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER completado SET DEFAULT false;";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_actividad_proceso() {
        $tabla = "a_actividad_proceso";
        return $this->eliminar($tabla);
    }
    

    public function create_a_tipos_procesos() {
        $tabla = "a_tipos_proceso";
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        $a_sql = [];

        
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
            id_tipo_proceso integer NOT NULL,
            nom_proceso text
            );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_tipos_proceso() {
        $tabla = "a_tipos_proceso";
        return $this->eliminar($tabla);
    }
    
    public function create_a_tareas() {
        $tabla = "a_tareas";
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        $a_sql = [];

        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_fase integer NOT NULL,
                    id_tarea integer NOT NULL,
                    desc_tarea character varying(70)
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_tareas() {
        $tabla = "a_tareas";
        return $this->eliminar($tabla);
    }
    
    public function create_a_fases() {
        $tabla = "a_fases";
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_fase integer NOT NULL,
                    desc_fase text,
                    sf boolean NOT NULL,
                    sv boolean NOT NULL
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_fases() {
        $tabla = "a_fases";
        return $this->eliminar($tabla);
    }
    
    public function create_a_procesos() {
        $tabla = "a_procesos";
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                    id_item integer NOT NULL,
                    id_tipo_proceso integer NOT NULL,
                    n_orden smallint,
                    id_fase integer NOT NULL,
                    id_tarea integer NOT NULL,
                    status smallint NOT NULL,
                    of_responsable character varying(7),
                    id_fase_previa integer,
                    id_tarea_previa integer,
                    mensaje_requisito text
                    );";
        
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";
        
        return $this->executeSql($a_sql);
    }
    public function eliminar_a_procesos() {
        $tabla = "a_procesos";
        return $this->eliminar($tabla);
    }
}