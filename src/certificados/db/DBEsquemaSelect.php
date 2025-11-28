<?php

namespace src\certificados\db;

use core\ConfigGlobal;
use core\DBRefresh;
use src\ubis\domain\contracts\DelegacionRepositoryInterface;

/**
 * crear las tablas necesarias para el esquema select,
 * para permitir la sincronización.
 */
class DBEsquemaSelect extends DBEsquema
{

    public function dropAllSelect()
    {
        $a_reg = explode('-', $this->esquema);
        $dl = $a_reg[1];
        $gesDelegeacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        if ($gesDelegeacion->soy_region_stgr($dl)) {
            $this->eliminar_e_certificados_emitidos_select();
        }
        // caso especial H-Hv
        if ($this->esquema !== 'H-H') {
            $this->eliminar_e_certificados_recibidos_select();
        }
        // caso especial M-Mv
        if ($this->esquema !== 'M-M') {
            $this->eliminar_e_certificados_recibidos();
        }
    }

    public function createAllSelect()
    {
        $a_reg = explode('-', $this->esquema);
        $dl = $a_reg[1];
        $gesDelegeacion = $GLOBALS['container']->get(DelegacionRepositoryInterface::class);
        if ($gesDelegeacion->soy_region_stgr($dl)) {
            $this->create_e_certificados_emitidos_select();
        }
        // caso especial H-Hv
        if ($this->esquema !== 'H-H') {
            $this->create_e_certificados_recibidos_select();
        }
        // caso especial M-Mv
        if ($this->esquema !== 'M-M') {
            $this->eliminar_e_certificados_recibidos();
        }
        // renovar subscripciones
        $DBRefresh = new DBRefresh();
        $DBRefresh->refreshSubscriptionModulo('sv-e');
    }

    /**
     * En la BD sv-e (esquema).
     */
    public function create_e_certificados_emitidos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "e_certificados_emitidos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_tabla_parent = 'public';
        if ($this->vf === 'v') {
            $nom_tabla_parent = 'publicv';
        }
        if ($this->vf === 'f') {
            $nom_tabla_parent = 'publicf';
        }
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */

        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_item)
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS e_certificados_rstgr_key ON $nom_tabla USING btree (certificado);";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function create_e_certificados_recibidos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');

        $tabla = "e_certificados_recibidos";
        $datosTabla = $this->infoTable($tabla);

        $nom_tabla = $datosTabla['nom_tabla'];
        $nom_tabla_parent = 'public';
        if ($this->vf === 'v') {
            $nom_tabla_parent = 'publicv';
        }
        if ($this->vf === 'f') {
            $nom_tabla_parent = 'publicf';
        }
        $nompkey = $tabla . '_pkey';
        /* Los constraint de 'primary key' y 'foreign key' deben estar en la creación de la tabla,
         *  que permite la clausula 'IF EXISTS'.  De otro modo da error cuando se está activando un módulo
         *  que ya había sido instalado y se había desactivado, pero no borrado.
         */
        $a_sql = [];
        $a_sql[] = "CREATE TABLE IF NOT EXISTS $nom_tabla (
                        CONSTRAINT $nompkey PRIMARY KEY (id_item)
                )
            INHERITS ($nom_tabla_parent.$tabla);";

        $a_sql[] = "ALTER TABLE $nom_tabla ALTER id_schema SET DEFAULT public.idschema('$this->esquema'::text)";
        $a_sql[] = "CREATE INDEX IF NOT EXISTS e_certificados_dl_key ON $nom_tabla USING btree (certificado);";
        $a_sql[] = "ALTER TABLE $nom_tabla OWNER TO $this->role; ";

        $this->executeSql($a_sql);
        $this->delPermisoGlobal('sfsv-e_select');
        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

    public function eliminar_e_certificados_emitidos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $this->eliminarDeSVESelect("e_certificados_emitidos");
    }

    public function eliminar_e_certificados_recibidos_select()
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $this->eliminarDeSVESelect("e_certificados_recibidos");
    }

}
