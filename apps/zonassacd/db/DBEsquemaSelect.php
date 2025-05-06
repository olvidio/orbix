<?php

namespace zonassacd\db;


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
        $this->eliminar_zonas_select();
        $this->eliminar_zonas_grupos_select();
        $this->eliminar_zonas_sacd_select();
    }

    public function createAllSelect()
    {
        $this->create_zonas_select();
        $this->create_zonas_grupos_select();
        $this->create_zonas_sacd_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /**
     * En la BD sv-e (esquema).
     */
    public function create_zonas_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "zonas";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_zona); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_zonas_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("zonas");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_zonas_grupos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "zonas_grupos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_grupo); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_zonas_grupos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("zonas_grupos");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

    public function create_zonas_sacd_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "zonas_sacd";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_zonas_sacd_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("zonas_sacd");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

}