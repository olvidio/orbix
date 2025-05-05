<?php

namespace actividadescentro\db;

use devel\model\DBAbstract;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $this->eliminar_da_ctr_encargados_select();
    }

    public function createAllSelect()
    {
        $this->create_da_ctr_encargados_select();
        // renovar subscripciones
        DBAbstract::refreshSubscription($this);
    }

    /**
     * En la BD Comun (esquema).
     */
    public function create_da_ctr_encargados_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $tabla = "da_ctr_encargados";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_seq = $datosTabla['campo_seq'];

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD CONSTRAINT da_ctr_encargados_id_activ_id_ubi_key
                    UNIQUE (id_activ, id_ubi); ";
        $a_sql[] = "ALTER TABLE $nom_tabla ADD PRIMARY KEY (id_activ, id_ubi); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";


        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_da_ctr_encargados_select()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');

        $datosTabla = $this->infoTable("da_ctr_encargados");

        $nom_tabla = $datosTabla['nom_tabla'];

        $a_sql = [];
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun_select');
    }

}