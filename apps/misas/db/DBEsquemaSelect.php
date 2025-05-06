<?php

namespace misas\db;

use core\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $this->eliminar_cuadricula_select();
        $this->eliminar_iniciales_select();
        $this->eliminar_rel_encargo_ctr_select();
    }

    public function createAllSelect()
    {
        $this->create_cuadricula_select();
        $this->create_iniciales_select();
        $this->create_rel_encargo_ctr_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('comun');
    }

    /**
     * En la BD Comun (esquema).
     */
    public function create_iniciales_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla = "misa_iniciales";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_nom); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_iniciales_select()
    {
        $datosTabla = $this->infoTable("misa_iniciales");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }

    public function create_cuadricula_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla = "misa_cuadricula";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (uuid_item); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_cuadricula_select()
    {
        $datosTabla = $this->infoTable("misa_cuadricula");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }

    public function create_rel_encargo_ctr_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla = "misa_rel_encargo_ctr";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (uuid_item); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_rel_encargo_ctr_select()
    {
        $datosTabla = $this->infoTable("misa_rel_encargo_ctr");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }

}