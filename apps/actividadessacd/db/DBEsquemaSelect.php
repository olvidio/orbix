<?php

namespace actividadessacd\db;

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
        $this->eliminar_atn_sacd_textos_select();
    }

    public function createAllSelect()
    {
        $this->create_atn_sacd_textos_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /**
     * En la BD sv-e (esquema).
     */
    public function create_atn_sacd_textos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "a_sacd_textos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_item); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT a_sacd_textos_ukey
                    UNIQUE (idioma,clave); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_atn_sacd_textos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $datosTabla = $this->infoTable("a_sacd_textos");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
        $this->delPermisoGlobal('comun_select');
    }

}