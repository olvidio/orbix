<?php

namespace cartaspresentacion\db;

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
        $this->eliminar_presentacion_select();
    }

    public function createAllSelect()
    {
        $this->create_presentacion_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /**
     * En la BD sv-e (esquema).
     */
    public function create_presentacion_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "du_presentacion";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_tabla_parent = 'public';
        if ($this->vf === 'v') {
            $nom_tabla_parent = 'publicv';
        }
        if ($this->vf === 'f') {
            $nom_tabla_parent = 'publicf';
        }

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_ubi, id_direccion); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_presentacion_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $datosTabla = $this->infoTable("du_presentacion");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeSVESelect($nom_tabla);
    }

}