<?php

namespace src\misas\db;

use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\utils_database\domain\entity\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de [global] En este caso public
 */
class DBEsquema extends DBAbstract
{
    private string $dir_base = ServerConf::DIR . "/src/misas/db";

    public function __construct(?string $esquema_sfsv = null)
    {
        if ($esquema_sfsv === null || $esquema_sfsv === '') {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1);
        $this->vf = substr($esquema_sfsv, -1);
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll(): void
    {
        $this->eliminar_cuadricula();
        $this->eliminar_iniciales();
        $this->eliminar_rel_encargo_ctr();
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->dropAllSelect();
        }
    }

    public function createAll(): void
    {
        $this->create_cuadricula();
        $this->create_iniciales();
        $this->create_rel_encargo_ctr();
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->createAllSelect();
        }
    }

    public function llenarAll(): void
    {
    }

    /**
     * @return array{tabla: string, nom_tabla: string, campo_seq: string, id_seq: string, filename: string}
     */
    protected function infoTable(string $tabla): array
    {
        $datosTabla = [];
        $nom_tabla = '';
        $campo_seq = '';
        $id_seq = '';
        switch ($tabla) {
            case "misa_iniciales":
                $datosTabla['tabla'] = "misa_iniciales_dl";
                $nom_tabla = $this->getNomTabla("misa_iniciales_dl");
                break;
            case "misa_cuadricula":
                $datosTabla['tabla'] = "misa_cuadricula_dl";
                $nom_tabla = $this->getNomTabla("misa_cuadricula_dl");
                break;
            case "misa_rel_encargo_ctr":
                $datosTabla['tabla'] = "misa_rel_encargo_ctr_dl";
                $nom_tabla = $this->getNomTabla("misa_rel_encargo_ctr");
                break;
            default:
                $datosTabla['tabla'] = $tabla;
                $nom_tabla = $this->getNomTabla($tabla);
                break;
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";

        return $datosTabla;
    }

    public function create_iniciales(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "misa_iniciales";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_tabla_parent = 'global';
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_nom)
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_iniciales(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("misa_iniciales");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_cuadricula(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "misa_cuadricula";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_tabla_parent = 'global';
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (uuid_item)
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_cuadricula(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("misa_cuadricula");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }

    public function create_rel_encargo_ctr(): void
    {
        $this->addPermisoGlobal('comun');

        $tabla = "misa_rel_encargo_ctr";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_tabla_parent = 'global';
        $nompkey = $tabla . '_pkey';

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (uuid_item)
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_rel_encargo_ctr(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("misa_rel_encargo_ctr");

        $nom_tabla = $datosTabla['nom_tabla'];

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
}
