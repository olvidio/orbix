<?php
namespace devel\model;

use core\ConfigGlobal;

/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * Cada esquema deberá crear las suyas, heredaas de estas.
 */


abstract class DBAbstract {
    
    protected $esquema;
    protected $vf;
    protected $role;
    protected $role_vf;
    protected $oDbl;
    protected $user_orbix;
    
    /**
     * Define el objeto PDO de la base de datos
     */
    protected function setConexion($db) {
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
     * @param   string  nombre de la tabla sin schema
     * @return boolean
     */
    protected function tableExists($nom_tabla) {
        $oDbl = $this->oDbl;
        $sql = "SELECT to_regclass('$nom_tabla');";
        
        if (($oDblSt = $oDbl->query($sql)) === FALSE) {
            $sClauError = 'comprobar';
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
            return FALSE;
        }
        $aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
        if ($aDades['to_regclass'] == $nom_tabla) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
     * Quita el permiso (orbix u orbixv/f) para acceder a global.
     */
    protected function delPermisoGlobal($db) {
        switch ($db) {
            case 'comun':
                // Conexión Comun public, para entrar como usuario orbix.
                $this->oDbl = $GLOBALS['oDBPC'];
                // Dar permisos al role H-dlb de orbix (para poder aceder a global)
                $this->user_orbix = 'orbix';
                $a_sql = [];
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBP'];
                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if ( ConfigGlobal::mi_sfsv() === 1 ) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix'.$vf;

                $a_sql = [];
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role_vf;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
            case 'sfsv-e':
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBEP'];
                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if ( ConfigGlobal::mi_sfsv() === 1 ) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix'.$vf;

                $a_sql = [];
                $a_sql[0] = "REVOKE $this->user_orbix FROM $this->role_vf;" ;
                
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
     * @param  $db  'comun'|'sfsv'
     */
    protected function addPermisoGlobal($db) {
        switch ($db) {
            case 'comun':
                // Conexión Comun public, para entrar como usuario orbix.
                $this->oDbl = $GLOBALS['oDBPC'];
                // Dar permisos al role H-dlb de orbix (para poder aceder a global)
                $this->user_orbix = 'orbix';
                $a_sql = [];
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBP'];
                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if ( ConfigGlobal::mi_sfsv() === 1 ) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix'.$vf;

                $a_sql = [];
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role_vf;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
            case 'sfsv-e':
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBEP'];
                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if ( ConfigGlobal::mi_sfsv() === 1 ) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $this->user_orbix = 'orbix'.$vf;

                $a_sql = [];
                $a_sql[0] = "GRANT $this->user_orbix TO $this->role_vf;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDBE'];
                break;
        }
    }
    protected function getNomTabla($tabla) {
        if ($this->esquema == 'public') {
            $public_vf = $this->esquema.$this->vf;
            $nom_tabla = '"'.$public_vf.'".'.$tabla;
        } else {
            $nom_tabla = '"'.$this->esquema.'".'.$tabla;
        }
        return $nom_tabla;
    }
    protected function executeSql($a_sql) {
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
    }
    protected function eliminar($nom_tabla) {
        $a_sql = [];
        // solo borrar todo si estoy en pruebas
        if (ConfigGlobal::is_debug_mode()) {
            $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;" ;
        } else {
            $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla RESTRICT;" ;
        }
        
        return $this->executeSql($a_sql);
    }
    
}