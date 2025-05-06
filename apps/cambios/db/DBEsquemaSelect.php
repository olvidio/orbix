<?php

namespace cambios\db;

use core\ConfigGlobal;
use core\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $this->eliminar_av_cambios_usuario_propiedades_pref_select();
        $this->eliminar_av_cambios_usuario_objeto_pref_select();
        $this->eliminar_av_cambios_usuario_select();
        $this->eliminar_av_cambios_anotados_select();
        $this->eliminar_av_cambios_anotados_sf_select();
        $this->eliminar_av_cambios_dl_select();
    }

    public function createAllSelect()
    {
        $this->create_av_cambios_dl_select();
        $this->create_av_cambios_anotados_select();
        $this->create_av_cambios_anotados_sf_select();
        $this->create_av_cambios_usuario_select();

        $this->create_av_cambios_usuario_objeto_pref_select();
        $this->create_av_cambios_usuario_propiedades_pref_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /** *****************************************************************************
     * En la BD comun (esquema).
     */
    public function create_av_cambios_dl_select()
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "av_cambios_dl";
        $tabla_padre = "av_cambios";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (public.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";

        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');
    }

    public function create_av_cambios_usuario_select()
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "av_cambios_usuario";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (public.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS {$tabla}_udx ON $nom_tabla USING btree (id_schema_cambio,id_item_cambio,id_usuario,sfsv,aviso_tipo); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');

    }

    public function create_av_cambios_anotados_sf_select()
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "av_cambios_anotados_dl_sf";
        $tabla_padre = "av_cambios_anotados";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (public.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS {$tabla}_udx ON $nom_tabla USING btree (server,id_schema_cambio,id_item_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_schema_cambio_idx ON $nom_tabla USING btree (id_schema_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_item_cambio_idx ON $nom_tabla USING btree (id_item_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_anotado_idx ON $nom_tabla USING btree (anotado); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_server_idx ON $nom_tabla USING btree (server); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');

    }

    public function create_av_cambios_anotados_select()
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "av_cambios_anotados_dl";
        $tabla_padre = "av_cambios_anotados";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (public.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";

        $a_sql[] = "CREATE UNIQUE INDEX IF NOT EXISTS {$tabla}_udx ON $nom_tabla USING btree (server,id_schema_cambio,id_item_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_schema_cambio_idx ON $nom_tabla USING btree (id_schema_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_id_item_cambio_idx ON $nom_tabla USING btree (id_item_cambio); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_anotado_idx ON $nom_tabla USING btree (anotado); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_server_idx ON $nom_tabla USING btree (server); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('comun_select');

    }

    public function eliminar_av_cambios_dl_select()
    {
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("av_cambios_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_av_cambios_anotados_sf_select()
    {
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("av_cambios_anotados_dl_sf");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('comun_select');

    }

    public function eliminar_av_cambios_anotados_select()
    {
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("av_cambios_anotados_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('comun_select');

    }

    public function eliminar_av_cambios_usuario_select()
    {
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("av_cambios_usuario");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('comun_select');

    }

    /** *****************************************************************
     * En la BD sv-e (esquema).
     */
    public function create_av_cambios_usuario_objeto_pref_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "av_cambios_usuario_objeto_pref";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";

        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function create_av_cambios_usuario_propiedades_pref_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "av_cambios_usuario_propiedades_pref";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_seq}_idx ON $nom_tabla USING btree ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_av_cambios_usuario_objeto_pref_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $datosTabla = $this->infoTable("av_cambios_usuario_objeto_pref");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
        $this->delPermisoGlobal('sfsv-e_select');
    }

    public function eliminar_av_cambios_usuario_propiedades_pref_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $datosTabla = $this->infoTable("av_cambios_usuario_propiedades_pref");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
        $this->delPermisoGlobal('sfsv-e_select');

    }

}