<?php

namespace src\misas\db;

use src\shared\infrastructure\persistence\postgresql\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{
    public function dropAllSelect(): void
    {
        $this->eliminar_cuadricula_select();
        $this->eliminar_iniciales_select();
        $this->eliminar_rel_encargo_ctr_select();
    }

    public function createAllSelect(): void
    {
        $this->create_cuadricula_select();
        $this->create_iniciales_select();
        $this->create_rel_encargo_ctr_select();
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('comun');
    }

    public function create_iniciales_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "misa_iniciales";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_nom)
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_iniciales_select(): void
    {
        $datosTabla = $this->infoTable("misa_iniciales");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }

    public function create_cuadricula_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "misa_cuadricula";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (uuid_item)
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_cuadricula_select(): void
    {
        $datosTabla = $this->infoTable("misa_cuadricula");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }

    public function create_rel_encargo_ctr_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "misa_rel_encargo_ctr";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (uuid_item)
                ) 
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_rel_encargo_ctr_select(): void
    {
        $datosTabla = $this->infoTable("misa_rel_encargo_ctr");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminarDeComunSelect($nom_tabla);
    }
}
