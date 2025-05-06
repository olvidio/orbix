<?php

namespace encargossacd\db;

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
        $this->eliminar_encargos_sacd_select();
        $this->eliminar_encargo_sacd_observ_select();
        $this->eliminar_encargo_sacd_horario_excepcion_select();
        $this->eliminar_encargo_sacd_horario_select();
        $this->eliminar_encargo_horario_excepcion_select();
        $this->eliminar_encargo_horario_select();
        $this->eliminar_encargos_select();
        $this->eliminar_encargo_tipo_select();
        $this->eliminar_encargo_datos_cgi_select();
        $this->eliminar_encargo_textos_select();
    }

    public function createAllSelect()
    {
        $this->create_encargo_tipo_select();
        $this->create_encargos_select();
        $this->create_encargos_sacd_select(); // debe ir antes de los horarios foreign key
        $this->create_encargo_horario_select();
        $this->create_encargo_horario_excepcion_select();
        $this->create_encargo_sacd_horario_select();
        $this->create_encargo_sacd_horario_excepcion_select();
        $this->create_encargo_sacd_observ_select();
        $this->create_encargo_datos_cgi_select();
        $this->create_encargo_textos_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /**
     * En la BD sv-e (esquema).
     */
    public function create_encargo_tipo_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_tipo";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_tipo_enc); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_tipo_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_tipo");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargos");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargo_horario_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_horario";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc,id_item_h); ";
        $a_sql[] = "CREATE INDEX encargo_horario_f_fin_idx ON $nom_tabla USING btree (f_fin); ";
        $a_sql[] = "CREATE INDEX encargo_horario_f_ini_idx ON $nom_tabla USING btree (f_ini); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_horario_id_item_idx ON $nom_tabla USING btree (id_item_h); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_horario_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_horario");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargo_horario_excepcion_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_horario_excepcion";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc,id_item_ex); ";
        $a_sql[] = "CREATE INDEX encargo_horario_ex_f_fin_idx ON $nom_tabla USING btree (f_fin); ";
        $a_sql[] = "CREATE INDEX encargo_horario_ex_f_ini_idx ON $nom_tabla USING btree (f_ini); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_horario_ex_id_item_idx ON $nom_tabla USING btree (id_item_ex); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_horario_excepcion_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_horario_excepcion");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargo_sacd_horario_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_sacd_horario";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc,id_item, id_nom); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_f_fin_idx ON $nom_tabla USING btree (f_fin); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_f_ini_idx ON $nom_tabla USING btree (f_ini); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_id_enc_idx ON $nom_tabla USING btree (id_enc); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_id_nom_idx ON $nom_tabla USING btree (id_nom ); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_sacd_horario_id_item_idx ON $nom_tabla USING btree (id_item); ";


        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_sacd_horario_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_sacd_horario");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargo_sacd_horario_excepcion_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_sacd_horario_excepcion";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_enc,id_item_ex); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_ex_f_fin_idx ON $nom_tabla USING btree (f_fin); ";
        $a_sql[] = "CREATE INDEX encargo_sacd_horario_ex_f_ini_idx ON $nom_tabla USING btree (f_ini); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_sacd_horario_ex_id_item_idx ON $nom_tabla USING btree (id_item_ex); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_sacd_horario_excepcion_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_sacd_horario_excepcion");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargo_sacd_observ_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_sacd_observ";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_sacd_observ_id_nom_idx ON $nom_tabla USING btree (id_nom); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_sacd_observ_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_sacd_observ");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargos_sacd_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargos_sacd";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargos_sacd_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargos_sacd");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_encargo_datos_cgi_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_datos_cgi";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "CREATE UNIQUE INDEX encargo_datos_cgi_id_ubi_idx ON $nom_tabla USING btree (id_ubi); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_datos_cgi_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_datos_cgi");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }


    public function create_encargo_textos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "encargo_textos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY ($campo_seq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_encargo_textos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("encargo_textos");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

}