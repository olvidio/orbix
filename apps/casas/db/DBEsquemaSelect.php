<?php

namespace casas\db;

use core\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $this->eliminar_da_ingresos_dl_select();
        $this->eliminar_du_gastos_dl_select();
        $this->eliminar_du_grupos_dl_select();
    }

    public function createAllSelect()
    {
        $this->create_da_ingresos_dl_select();
        $this->create_du_gastos_dl_select();
        $this->create_du_grupos_dl_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('comun');
    }

    /** **************************************************
     * En la BD Comun (esquema).
     */
    public function create_da_ingresos_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla_padre = "da_ingresos";
        $tabla = "da_ingresos_dl";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_activ); ";
        $a_sql[] = "CREATE INDEX {$tabla}_id_activ ON $nom_tabla USING btree (id_activ); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');
    }

    public function create_du_gastos_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla_padre = "du_gastos";
        $tabla = "du_gastos_dl";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        $a_sql[] = "CREATE INDEX {$tabla}_id_ubi ON $nom_tabla USING btree (id_ubi); ";
        $a_sql[] = "CREATE INDEX {$tabla}_f_gasto ON $nom_tabla USING btree (f_gasto); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');
    }

    public function create_du_grupos_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla_padre = "du_grupos";
        $tabla = "du_grupos_dl";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        $a_sql[] = "CREATE INDEX {$tabla}_id_ubi_padre ON $nom_tabla USING btree (id_ubi_padre); ";
        $a_sql[] = "CREATE INDEX {$tabla}_id_ubi_hijo ON $nom_tabla USING btree (id_ubi_hijo); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_da_ingresos_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("da_ingresos_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_du_gastos_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("du_gastos_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_du_grupos_dl_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("du_grupos_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun_select');
    }


}