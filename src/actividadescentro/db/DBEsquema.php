<?php

namespace src\actividadescentro\db;

use src\shared\config\ConfigGlobal;
use src\shared\config\ServerConf;
use src\utils_database\domain\entity\DBAbstract;

/**
 * crear las tablas necesarias para el esquema.
 * Heredadas de global
 */
class DBEsquema extends DBAbstract
{
    private string $dir_base = ServerConf::DIR . "/src/actividadescentro/db";

    public function __construct(?string $esquema_sfsv = null)
    {
        if (empty($esquema_sfsv)) {
            $esquema_sfsv = ConfigGlobal::mi_region_dl();
        }
        $this->esquema = substr($esquema_sfsv, 0, -1);
        $this->role = '"' . $this->esquema . '"';
        $this->role_vf = '"' . $esquema_sfsv . '"';
    }

    public function dropAll(): void
    {
        $this->eliminar_da_ctr_encargados();
        if (DBAbstract::hasServerSelect()) {
            $oDBEsquemaSelect = new DBEsquemaSelect();
            $oDBEsquemaSelect->dropAllSelect();
        }
    }

    public function createAll(): void
    {
        $this->create_da_ctr_encargados();
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
        $datosTabla['tabla'] = $tabla;
        switch ($tabla) {
            case "da_ctr_encargados":
                $nom_tabla = $this->getNomTabla($tabla);
                $campo_seq = '';
                $id_seq = '';
                break;
            default:
                throw new \InvalidArgumentException('Tabla desconocida: ' . $tabla);
        }
        $datosTabla['nom_tabla'] = $nom_tabla;
        $datosTabla['campo_seq'] = $campo_seq;
        $datosTabla['id_seq'] = $id_seq;
        $datosTabla['filename'] = $this->dir_base . "/$tabla.csv";
        return $datosTabla;
    }

    public function create_da_ctr_encargados(): void
    {
        $this->addPermisoGlobal('comun');

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

        $this->delPermisoGlobal('comun');
    }

    public function eliminar_da_ctr_encargados(): void
    {
        $this->addPermisoGlobal('comun');

        $datosTabla = $this->infoTable("da_ctr_encargados");

        $nom_tabla = $datosTabla['nom_tabla'];
        $id_seq = $datosTabla['id_seq'];

        $a_sql = [];
        $a_sql[0] = "DROP SEQUENCE IF EXISTS $id_seq CASCADE;";
        $this->executeSql($a_sql);

        $this->eliminar($nom_tabla);

        $this->delPermisoGlobal('comun');
    }
}
