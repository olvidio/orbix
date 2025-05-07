<?php

namespace devel\model;

use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;

/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * Cada esquema deberá crear las suyas, heredadas de estas.
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
        // Si es el mismo servidor (portátil) me lo salto:
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
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
            case 'sfsv-e':
                // Conexión sv-e esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDBE'];
                break;
        }
    }

    /**
     *
     * Al ser de la DB comun, puede ser que al intentar crear como sf, las
     * tablas ya se hayan creado como sv (o al revés).
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
        switch ($db) {
            case 'sfsv-e_select':
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
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role_vf GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema.
                $this->oDbl = $GLOBALS['oDBE_Select'];
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
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema.
                $this->oDbl = $GLOBALS['oDBC_Select'];
                break;
            case 'comun':
                /*
                // Conexión Comun public, para entrar como usuario orbix.
                $this->oDbl = $GLOBALS['oDBPC'];
                */
                // conectar con DB comun:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('public'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlb de orbix (para poder aceder a global)
                $this->user_orbix = 'orbix';
                $a_sql = [];
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
                /*
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBP'];
                */
                // conectar con DB comun:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('publicv'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix' . $vf;

                $a_sql = [];
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role_vf GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
            case 'sfsv-e':
                /*
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBEP'];
                */
                // conectar con DB comun:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('publicv-e'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if (ConfigGlobal::mi_sfsv() === 1) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix' . $vf;

                $a_sql = [];
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role_vf GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv-e esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDBE'];
                break;
        }
    }

    /**
     * Añade el permiso (orbix u orbixv/f) para acceder a global.
     *
     * @param  $db 'comun'|'sfsv'
     */
    protected function addPermisoGlobal(string $db)
    {
        switch ($db) {
            case 'sfsv-e_select':
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
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role_vf GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema.
                $this->oDbl = $GLOBALS['oDBE_Select'];
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
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema.
                $this->oDbl = $GLOBALS['oDBC_Select'];
                break;
            case 'comun':
                /*
                // Conexión Comun public, para entrar como usuario orbix.
                $this->oDbl = $GLOBALS['oDBPC'];
                */

                // conectar con DB comun:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('public'); //de la database comun
                $oConexion = new DBConnection($config);
                $this->oDbl = $oConexion->getPDO();

                // Dar permisos al role H-dlb de orbix (para poder acceder a global)
                $this->user_orbix = 'orbix';

                $a_sql = [];
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
                /*
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBP'];
                */
                // conectar con DB sv-e:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('publicv'); //de la database comun
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
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role_vf GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
            case 'sfsv-e':
                /*
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBEP'];
                */
                // conectar con DB sv-e:
                $oConfigDB = new ConfigDB('importar'); //de la database comun
                $config = $oConfigDB->getEsquema('publicv-e'); //de la database comun
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
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role_vf GRANTED BY orbix_admindb;";

                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDBE'];
                break;
        }
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
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('comun_select');
        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('comun_select');
    }

    protected function eliminarDeSVESelect($nom_tabla)
    {
        // OJO Corresponde al esquema sf-e/sv-e, no al comun.
        $esquema_org = $this->esquema;
        $role_org = $this->role;
        $this->esquema = ConfigGlobal::mi_region_dl();
        $this->role = '"' . $this->esquema . '"';
        // (debe estar después de fijar el role)
        $this->addPermisoGlobal('sfsv-e_select');
        $this->eliminar($nom_tabla);
        $this->delPermisoGlobal('sfsv-e_select');

        // Devolver los valores al estado original
        $this->esquema = $esquema_org;
        $this->role = $role_org;
    }

}