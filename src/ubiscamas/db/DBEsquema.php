<?php

namespace src\ubiscamas\db;

use core\ConfigGlobal;
use core\ServerConf;
use src\cambios\db\DBEsquemaSelect;
use src\utils_database\domain\entity\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de public
 */
class DBEsquema extends DBAbstract
{

    private $dir_base = ServerConf::DIR . "/apps/cambios/db";

    public function __construct($esquema_sfsv = NULL)
    {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1); // quito la v o la f.
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll()
    {
        $this->eliminar_du_camas_dl();
        $this->eliminar_du_habitaciones_dl();
        // eliminar las tablas en la DBSelect para la sincronización.
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->dropAllSelect();
        }
    }

    public function createAll()
    {
        $this->create_du_habitaciones_dl();
        $this->create_du_camas_dl();
        // crear las tablas en la DBSelect para la sincronización.
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->createAllSelect();
        }
    }

    protected function infoTable($tabla)
    {
        $datosTabla = [];
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "du_habitaciones_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_habitacion';
                break;
            case "du_camas_dl":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = 'id_cama';
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";
        return $datosTabla;
    }

    /**
     * En la BD Comun (esquema).
     */
    public function create_du_habitaciones_dl()
    {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "du_habitaciones_dl";
        $tabla_padre = "du_habitaciones";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_uniq = $datosTabla['campo_seq'];

        // Al ser de la DB comun, puede ser que al intentar crear como sf, las
        // tablas ya se hayan creado como sv (o al revés).
        if ($this->tableExists($tabla)) {
            return TRUE;
        }

        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY ($campo_uniq)
                ) 
            INHERITS (public.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER COLUMN $campo_uniq SET DEFAULT gen_random_uuid(); ";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS {$tabla}_{$campo_uniq}_idx ON $nom_tabla USING btree ($campo_uniq); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
        return TRUE;
    }

    public function eliminar_du_habitaciones_dl()
    {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("du_habitaciones_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    /**
     * En la BD Comun (esquema).
     */
    public function create_du_camas_dl()
    {
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $tabla = "du_camas_dl";
        $tabla_padre = "du_camas";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $campo_uniq = $datosTabla['campo_seq'];
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        // Al ser de la DB comun, puede ser que al intentar crear como sf, las
        // tablas ya se hayan creado como sv (o al revés).
        if ($this->tableExists($tabla)) {
            return TRUE;
        }

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY ($campo_uniq)
                ) 
            INHERITS (public.$tabla_padre);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER COLUMN $campo_uniq SET DEFAULT gen_random_uuid(); ";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
        return true;
    }


    public function eliminar_du_camas_dl()
    {
        // OJO, está en public
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("du_camas_dl");
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
}