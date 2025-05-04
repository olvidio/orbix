<?php

namespace procesos\db;

use core\ConfigGlobal;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $this->eliminar_a_actividad_proceso_selected('sv');
        $this->eliminar_a_actividad_proceso_selected('sf');
        $this->eliminar_a_tipos_proceso_selected();
        $this->eliminar_a_tareas_proceso_selected();
        $this->eliminar_a_fases_selected();
        $this->eliminar_a_tareas_selected();
        $this->eliminar_aux_usuarios_perm_selected();
    }

    public function createAllSelect()
    {
        $this->create_a_tareas_selected(); // antes que 'actividad_proceso' por el FOREIGN KEY
        $this->create_a_fases_selected(); // antes que 'actividad_proceso' por el FOREIGN KEY
        $this->create_a_actividad_proceso_selected('sv');
        $this->create_a_actividad_proceso_selected('sf');
        $this->create_a_tipos_procesos_selected();
        $this->create_a_tareas_proceso_selected();
        $this->create_aux_usuarios_perm_selected();
    }


    /**
     * En la BD Comun (esquema).
     */
    public function create_a_actividad_proceso_selected($seccion)
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla_padre = "a_actividad_proceso";
        if ($seccion === 'sv') {
            $tabla = "a_actividad_proceso_sv";
        }
        if ($seccion === 'sf') {
            $tabla = "a_actividad_proceso_sf";
        }
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun');
    }

    public function eliminar_a_actividad_proceso_selected($seccion)
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        if ($seccion === 'sv') {
            $tabla = "a_actividad_proceso_sv";
        }
        if ($seccion === 'sf') {
            $tabla = "a_actividad_proceso_sf";
        }
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_tipos_procesos_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tipos_proceso";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_a_tipos_proceso_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tipos_proceso");

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_tareas_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS {$tabla}_id_tarea_key ON $nom_tabla USING btree (id_tarea); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_a_tareas_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tareas");

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_fases_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_fases";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_a_fases_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_fases");

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_a_tareas_proceso_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "a_tareas_proceso";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS {$tabla}_idx ON $nom_tabla USING btree (id_tipo_proceso, id_fase, id_tarea); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_a_tareas_proceso_selected()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("a_tareas_proceso");

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * En la BD sf-e/sv-e [exterior] (esquema).
     */
    public function create_aux_usuarios_perm_selected()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $tabla = "aux_usuarios_perm";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_usuario ON $nom_tabla USING btree (id_usuario); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_tipo_activ ON $nom_tabla USING btree (id_tipo_activ_txt); ";

        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_aux_usuarios_perm_selected()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e');

        $datosTabla = $this->infoTable("aux_usuarios_perm");

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('sfsv-e');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }


}