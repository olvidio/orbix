<?php

namespace src\utils_database\domain\entity;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;

/**
 * Crear las tablas necesaria a nivel de aplicaci?n (global).
 * Cada esquema deber? crear las suyas, heredadas de estas.
 */
abstract class DBAbstract
{

    protected $esquema;
    protected $vf;
    protected $role;
    protected $role_vf;
    protected $oDbl;
    protected $user_orbix;

    public static function hasServerSelect()
    {
        // Si es el mismo servidor (port?til) me lo salto:
        $oConfigDB = new ConfigDB('importar');
        $config = $oConfigDB->getEsquema('public');
        $host_sv = $config['host'];
        $port_sv = $config['port'];
        //coge los valores de public: 1.la database sv-e; 2.nombre superusuario; 3.pasword superusuario;
        $configE = $oConfigDB->getEsquema('public_select');
        $host_sve = $configE['host'];
        $port_sve = $configE['port'];

        return ($host_sv != $host_sve || $port_sv != $port_sve);
    }

    /**
     * Define el objeto PDO de la base de datos
     */
    protected function setConexion($db)
    {
        switch ($db) {
            case 'comun':
                // Conexi?n Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
                // Conexi?n sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
            case 'sfsv-e':
                // Conexi?n sv-e esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDBE'];
                break;
        }
    }

    /**
     *
     * Al ser de la DB comun, puede ser que al intentar crear como sf, las
     * tablas ya se hayan creado como sv (o al rev?s).
     *
     * @param string  nombre de la tabla sin schema
     * @return boolean
     */
    protected function tableExists($nom_tabla)
    {
        $oDbl = $this->oDbl;
        $sql = "SELECT to_regclass('$nom_tabla');";

        if (($oDblSt = $oDbl->query($sql)) === FALSE) {
            $sClauError = 'comprobar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
        if ($aDades['to_regclass'] === $nom_tabla) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Quita el permiso (orbix u orbixv/f) para acceder a global.
     */
    protected function delPermisoGlobal($db)
    {
        if (empty($this->role) && empty($this->role_vf)) {
            return;
        }
        $pdoOrigen = $this->oDbl;
        try {
            $this->delPermisoGlobalConexion($db);
        } finally {
            $this->oDbl = $pdoOrigen;
        }
    }

    private function delPermisoGlobalConexion($db): void
    {
        $role_target = (empty($this->role)) ? $this->role_vf : $this->role;
        $role_target = str_replace('"', '', $role_target);

        switch ($db) {
            case 'sfsv-e_select':
                $role_target = (empty($this->role_vf)) ? $role_target : str_replace('"', '', $this->role_vf);
                // conectar con DB sv-e_select:
                $oConfigDB = new ConfigDB('importar');
                $config = $oConfigDB->getEsquema('publicv-e_select');
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlbv de orbixv/f (para poder acceder a global)
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix' . $vf;
                $a_sql = [];
                $a_sql = [$this->sqlRevokeRoleFromRole($this->user_orbix, $role_target)];

                $this->executeSql($a_sql);
                break;
            case 'comun_select':
                // conectar con DB comun_select:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('public_select'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlb de orbix (para poder acceder a global)
                $this->user_orbix = 'orbix';

                $a_sql = [];
                $a_sql = [$this->sqlRevokeRoleFromRole($this->user_orbix, $role_target)];

                $this->executeSql($a_sql);
                break;
            case 'comun':
                $oConfigDB = new ConfigDB('importar');
                $config = $oConfigDB->getEsquema('public');
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                $this->user_orbix = 'orbix';

                $a_sql = array_merge(
                    [$this->sqlRevokeRoleFromRole($this->user_orbix, $role_target)],
                    $this->sqlRevokesRestoSiRolExiste('resto', $role_target),
                );

                $this->executeSql($a_sql);
                break;
            case 'sfsv':
                $role_target = (empty($this->role_vf)) ? $role_target : str_replace('"', '', $this->role_vf);
                $oConfigDB = new ConfigDB('importar');
                $config = $oConfigDB->getEsquema('publicv');
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                    $resto = 'restov';
                } else {
                    $vf = 'f';
                    $resto = 'restof';
                }
                $this->user_orbix = 'orbix' . $vf;

                $a_sql = array_merge(
                    [$this->sqlRevokeRoleFromRole($this->user_orbix, $role_target)],
                    $this->sqlRevokesRestoSiRolExiste($resto, $role_target),
                );

                $this->executeSql($a_sql);
                break;
            case 'sfsv-e':
                $role_target = (empty($this->role_vf)) ? $role_target : str_replace('"', '', $this->role_vf);
                $oConfigDB = new ConfigDB('importar');
                $config = $oConfigDB->getEsquema('publicv-e');
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                    $resto = 'restov';
                } else {
                    $vf = 'f';
                    $resto = 'restof';
                }
                $this->user_orbix = 'orbix' . $vf;
                $a_sql = array_merge(
                    [$this->sqlRevokeRoleFromRole($this->user_orbix, $role_target)],
                    $this->sqlRevokesRestoSiRolExiste($resto, $role_target),
                );

                $this->executeSql($a_sql);
                break;
        }
    }

    /**
     * A?ade el permiso (orbix u orbixv/f) para acceder a global.
     *
     * @param  $db 'comun'|'sfsv'
     */
    protected function addPermisoGlobal(string $db)
    {
        if (empty($this->role) && empty($this->role_vf)) {
            return;
        }
        $pdoOrigen = $this->oDbl;
        try {
            $this->addPermisoGlobalConexion($db);
        } finally {
            $this->oDbl = $pdoOrigen;
        }
    }

    private function addPermisoGlobalConexion(string $db): void
    {
        $role_target = (empty($this->role)) ? $this->role_vf : $this->role;
        $role_target = str_replace('"', '', $role_target);

        switch ($db) {
            case 'sfsv-e_select':
                $role_target = (empty($this->role_vf)) ? $role_target : str_replace('"', '', $this->role_vf);
                // conectar con DB sv-e_select:
                $oConfigDB = new ConfigDB('importar');
                $config = $oConfigDB->getEsquema('publicv-e_select');
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlbv de orbixv/f (para poder acceder a global)
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                    $resto = 'restov';
                } else {
                    $vf = 'f';
                    $resto = 'restof';
                }
                $this->user_orbix = 'orbix' . $vf;
                $a_sql = array_merge(
                    [$this->sqlGrantRoleToRole($this->user_orbix, $role_target)],
                    $this->sqlGrantsRestoSiRolExiste($resto, $role_target),
                );

                $this->executeSql($a_sql);
                break;
            case 'comun_select':
                // conectar con DB comun_select:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('public_select'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlb de orbix (para poder acceder a global)
                $this->user_orbix = 'orbix';

                $a_sql = array_merge(
                    [$this->sqlGrantRoleToRole($this->user_orbix, $role_target)],
                    $this->sqlGrantsRestoSiRolExiste('resto', $role_target),
                );

                $this->executeSql($a_sql);
                break;
            case 'comun':
                // conectar con DB comun:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('public'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlb de orbix (para poder acceder a global)
                $this->user_orbix = 'orbix';

                $a_sql = array_merge(
                    [$this->sqlGrantRoleToRole($this->user_orbix, $role_target)],
                    $this->sqlGrantsRestoSiRolExiste('resto', $role_target),
                );

                $this->executeSql($a_sql);
                break;
            case 'sfsv':
                $role_target = (empty($this->role_vf)) ? $role_target : str_replace('"', '', $this->role_vf);
                // conectar con DB sv-e:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('publicv'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlbv de orbixv/f (para poder acceder a global)
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                    $resto = 'restov';
                } else {
                    $vf = 'f';
                    $resto = 'restof';
                }
                $this->user_orbix = 'orbix' . $vf;


                $a_sql = array_merge(
                    [$this->sqlGrantRoleToRole($this->user_orbix, $role_target)],
                    $this->sqlGrantsRestoSiRolExiste($resto, $role_target),
                );

                $this->executeSql($a_sql);
                break;
            case 'sfsv-e':
                $role_target = (empty($this->role_vf)) ? $role_target : str_replace('"', '', $this->role_vf);
                // conectar con DB sv-e:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('publicv-e'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlbv de orbixv/f (para poder acceder a global)
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                    $resto = 'restov';
                } else {
                    $vf = 'f';
                    $resto = 'restof';
                }
                $this->user_orbix = 'orbix' . $vf;
                $a_sql = array_merge(
                    [$this->sqlGrantRoleToRole($this->user_orbix, $role_target)],
                    $this->sqlGrantsRestoSiRolExiste($resto, $role_target),
                );

                $this->executeSql($a_sql);
                break;
        }
    }

    /**
     * Rol que recibe GRANT/REVOKE en addPermisoRole (en sfsv usa role_vf, p. ej. Pl-crPlv).
     */
    private function roleTargetParaPermisoRole(string $db): string
    {
        $role_target = (empty($this->role)) ? $this->role_vf : $this->role;
        $role_target = str_replace('"', '', $role_target);
        if ($db === 'sfsv' && !empty($this->role_vf)) {
            $role_target = str_replace('"', '', $this->role_vf);
        }

        return $role_target;
    }

    protected function addPermisoRole(string $db, string $role_to_grant)
    {
        if (empty($this->role) && empty($this->role_vf)) {
            return;
        }
        $role_to_grant = str_replace('"', '', $role_to_grant);
        $role_target = $this->roleTargetParaPermisoRole($db);
        // Mismo rol (p. ej. ctr dl2resto: GRANT "Pl-crPlv" TO "Pl-crPlv").
        if ($role_to_grant === $role_target) {
            return;
        }

        $pdoOrigen = $this->oDbl;
        try {
            switch ($db) {
                case 'comun':
                    $oConfigDB = new ConfigDB('importar');
                    $config = $oConfigDB->getEsquema('public');
                    $oConexion = new DBConnection($config);
                    $this->oDbl = $oConexion->getPDO();

                    $a_sql = [$this->sqlGrantRoleToRole($role_to_grant, $role_target)];

                    $this->executeSql($a_sql);
                    break;
                case 'sfsv':
                    $oConfigDB = new ConfigDB('importar');
                    $config = $oConfigDB->getEsquema('publicv');
                    $oConexion = new DBConnection($config);
                    $this->oDbl = $oConexion->getPDO();

                    $a_sql = [$this->sqlGrantRoleToRole($role_to_grant, $role_target)];

                    $this->executeSql($a_sql);
                    break;
            }
        } finally {
            $this->oDbl = $pdoOrigen;
        }
    }

    protected function delPermisoRole(string $db, string $role_to_grant)
    {
        if (empty($this->role) && empty($this->role_vf)) {
            return;
        }
        $role_to_grant = str_replace('"', '', $role_to_grant);
        $role_target = $this->roleTargetParaPermisoRole($db);
        if ($role_to_grant === $role_target) {
            return;
        }

        $pdoOrigen = $this->oDbl;
        try {
            switch ($db) {
                case 'comun':
                    $oConfigDB = new ConfigDB('importar');
                    $config = $oConfigDB->getEsquema('public');
                    $oConexion = new DBConnection($config);
                    $this->oDbl = $oConexion->getPDO();

                    $a_sql = [$this->sqlRevokeRoleFromRole($role_to_grant, $role_target)];

                    $this->executeSql($a_sql);
                    break;
                case 'sfsv':
                    $oConfigDB = new ConfigDB('importar');
                    $config = $oConfigDB->getEsquema('publicv');
                    $oConexion = new DBConnection($config);
                    $this->oDbl = $oConexion->getPDO();

                    $a_sql = [$this->sqlRevokeRoleFromRole($role_to_grant, $role_target)];

                    $this->executeSql($a_sql);
                    break;
            }
        } finally {
            $this->oDbl = $pdoOrigen;
        }
    }

    private function escSqlIdent(string $name): string
    {
        return str_replace('"', '', $name);
    }

    private function sqlGrantRoleToRole(string $roleMember, string $roleGrantee): string
    {
        $member = $this->escSqlIdent($roleMember);
        $grantee = $this->escSqlIdent($roleGrantee);

        return "DO $$ BEGIN IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = '$member') AND EXISTS (SELECT 1 FROM pg_roles WHERE rolname = '$grantee') THEN BEGIN EXECUTE 'GRANT \"$member\" TO \"$grantee\"'; EXCEPTION WHEN insufficient_privilege OR undefined_object THEN NULL; END; END IF; END $$;";
    }

    private function sqlRevokeRoleFromRole(string $roleMember, string $roleGrantee): string
    {
        $member = $this->escSqlIdent($roleMember);
        $grantee = $this->escSqlIdent($roleGrantee);

        return "DO $$ BEGIN IF EXISTS (SELECT 1 FROM pg_roles WHERE rolname = '$member') AND EXISTS (SELECT 1 FROM pg_roles WHERE rolname = '$grantee') THEN EXECUTE 'REVOKE \"$member\" FROM \"$grantee\"'; END IF; END $$;";
    }

    /**
     * @return list<string>
     */
    private function sqlGrantsRestoSiRolExiste(string $resto, string $rol): array
    {
        $resto = $this->escSqlIdent($resto);
        $rol = $this->escSqlIdent($rol);
        $cond = "EXISTS (SELECT 1 FROM pg_roles WHERE rolname = '$rol') AND EXISTS (SELECT 1 FROM pg_namespace WHERE nspname = '$resto')";

        return [
            "DO $$ BEGIN IF $cond THEN EXECUTE 'GRANT ALL PRIVILEGES ON SCHEMA \"$resto\" TO \"$rol\"'; END IF; END $$;",
            "DO $$ BEGIN IF $cond THEN EXECUTE 'GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA \"$resto\" TO \"$rol\"'; END IF; END $$;",
            "DO $$ BEGIN IF $cond THEN EXECUTE 'GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA \"$resto\" TO \"$rol\"'; END IF; END $$;",
        ];
    }

    /**
     * Revoca los GRANT de {@see sqlGrantsRestoSiRolExiste} (necesario antes de DROP ROLE).
     *
     * @return list<string>
     */
    private function sqlRevokesRestoSiRolExiste(string $resto, string $rol): array
    {
        $resto = $this->escSqlIdent($resto);
        $rol = $this->escSqlIdent($rol);
        $cond = "EXISTS (SELECT 1 FROM pg_roles WHERE rolname = '$rol') AND EXISTS (SELECT 1 FROM pg_namespace WHERE nspname = '$resto')";

        return [
            "DO $$ BEGIN IF $cond THEN EXECUTE 'REVOKE ALL PRIVILEGES ON ALL TABLES IN SCHEMA \"$resto\" FROM \"$rol\"'; END IF; END $$;",
            "DO $$ BEGIN IF $cond THEN EXECUTE 'REVOKE ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA \"$resto\" FROM \"$rol\"'; END IF; END $$;",
            "DO $$ BEGIN IF $cond THEN EXECUTE 'REVOKE ALL PRIVILEGES ON SCHEMA \"$resto\" FROM \"$rol\"'; END IF; END $$;",
        ];
    }

    protected function getNomTabla($tabla)
    {
        if ($this->esquema === 'public') {
            $public_vf = $this->esquema . $this->vf;
            $nom_tabla = '"' . $public_vf . '".' . $tabla;
        } else {
            $nom_tabla = '"' . $this->esquema . '".' . $tabla;
        }
        return $nom_tabla;
    }

    protected function executeSql($a_sql)
    {
        $oDbl = $this->oDbl;

        $oDbl->beginTransaction();
        foreach ($a_sql as $sql) {
            if ($oDbl->exec($sql) === false) {
                $sClauError = 'Procesos.DBEsquema.query';
                $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
                $oDbl->rollback();
                return FALSE;
            }
        }
        $oDbl->commit();
        return TRUE;
    }

    protected function eliminar($nom_tabla)
    {
        $a_sql = [];
        // solo borrar todo si estoy en pruebas
        if (ConfigGlobal::is_debug_mode()) {
            $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;";
        } else {
            $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla RESTRICT;";
        }

        return $this->executeSql($a_sql);
    }

    protected function eliminarDeComunSelect($nom_tabla)
    {
        // (debe estar despu?s de fijar el role)
        $this->addPermisoGlobal('comun_select');
        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('comun_select');
    }

    protected function eliminarDeSVESelect($tabla_sin_esquema)
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';

        $datosTabla = $this->infoTable($tabla_sin_esquema);
        $nom_tabla = $datosTabla['nom_tabla'];

        $this->addPermisoGlobal('sfsv-e_select');
        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('sfsv-e_select');

        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

}