<?php

namespace src\actividadescentro\db;

use src\shared\infrastructure\persistence\postgresql\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{
    public function dropAllSelect(): void
    {
        $this->eliminar_da_ctr_encargados_select();
    }

    public function createAllSelect(): void
    {
        $this->create_da_ctr_encargados_select();
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('comun');
    }

    public function create_da_ctr_encargados_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "da_ctr_encargados";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_activ, id_ubi)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_da_ctr_encargados_select(): void
    {
        $datosTabla = $this->infoTable("da_ctr_encargados");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }
}
