<?php

namespace src\zonassacd\db;


use src\shared\infrastructure\persistence\postgresql\DBRefresh;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 *
 * Réplica de las tablas de zonas en comun_select (es lo que lee la DMZ).
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect(): void
    {
        $this->eliminar_zonas_ctr_select();
        $this->eliminar_zonas_select();
        $this->eliminar_zonas_grupos_select();
        $this->eliminar_zonas_sacd_select();
    }

    public function createAllSelect(): void
    {
        $this->create_zonas_select();
        $this->create_zonas_grupos_select();
        $this->create_zonas_sacd_select();
        $this->create_zonas_ctr_select();
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('comun');
    }

    public function create_zonas_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "zonas";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_zona)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_zonas_select(): void
    {
        $datosTabla = $this->infoTable("zonas");
        $this->eliminarDeComunSelect($datosTabla['nom_tabla']);
    }

    public function create_zonas_grupos_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "zonas_grupos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_grupo)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_zonas_grupos_select(): void
    {
        $datosTabla = $this->infoTable("zonas_grupos");
        $this->eliminarDeComunSelect($datosTabla['nom_tabla']);
    }

    public function create_zonas_sacd_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "zonas_sacd";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_item)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_zonas_sacd_select(): void
    {
        $datosTabla = $this->infoTable("zonas_sacd");
        $this->eliminarDeComunSelect($datosTabla['nom_tabla']);
    }

    /**
     * Sin foreign key hacia zonas: en el suscriptor las filas llegan por replicación
     * y el orden de aplicación no está garantizado.
     */
    public function create_zonas_ctr_select(): void
    {
        $this->addPermisoGlobal('comun_select');

        $tabla = "zonas_ctr";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_ubi)
                )
            INHERITS (global.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS zonas_ctr_id_zona_idx ON $nom_tabla (id_zona);";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun_select');
    }

    public function eliminar_zonas_ctr_select(): void
    {
        $datosTabla = $this->infoTable("zonas_ctr");
        $this->eliminarDeComunSelect($datosTabla['nom_tabla']);
    }
}
