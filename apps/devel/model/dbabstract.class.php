<?php
namespace devel\model;

use core\ConfigGlobal;

/**
 * Crear las tablas necesaria a nivel de aplicación (global).
 * Cada esquema deberá crear las suyas, heredaas de estas.
 */


abstract class DBAbstract {
    
    protected $esquema;
    protected $role;
    protected $oDbl;
    
    /**
     * Define el objeto PDO de la base de datos
     */
    protected function setConexion($db) {
        switch ($db) {
            case 'comun':
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'svsf':
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
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
                $a_sql = [];
                $a_sql[0] = "REVOKE orbix FROM $this->role;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
            case 'svsf':
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBP'];
                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if ( ConfigGlobal::mi_sfsv() === 1 ) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $user_orbix = 'orbix'.$vf;

                $a_sql = [];
                $a_sql[0] = "REVOKE $user_orbix FROM $this->role;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
        }
    }
    
    /**
     * Añade el permiso (orbix u orbixv/f) para acceder a global.
     * 
     * @param  $db  'comun'|'svsf'
     */
    protected function addPermisoGlobal($db) {
        switch ($db) {
            case 'comun':
                // Conexión Comun public, para entrar como usuario orbix.
                $this->oDbl = $GLOBALS['oDBPC'];
                // Dar permisos al role H-dlb de orbix (para poder aceder a global)
                $a_sql = [];
                $a_sql[0] = "GRANT orbix TO $this->role;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión Comun esquema, para entrar como usuario H-dlb.
                $this->oDbl = $GLOBALS['oDBC'];
                break;
            case 'sfsv':
            case 'svsf':
                // Conexión sv public, para entrar como usuario orbixv/f.
                $this->oDbl = $GLOBALS['oDBP'];
                // Dar permisos al role H-dlbv de orbixv/f (para poder aceder a global)
                if ( ConfigGlobal::mi_sfsv() === 1 ) {
                    $vf = 'v';
                } else {
                    $vf = 'f';
                }
                $user_orbix = 'orbix'.$vf;

                $a_sql = [];
                $a_sql[0] = "GRANT $user_orbix TO $this->role;" ;
                
                $this->executeSql($a_sql);
                // Devuelve la conexión a origen.
                // Conexión sv esquema, para entrar como usuario H-dlbv.
                $this->oDbl = $GLOBALS['oDB'];
                break;
        }
    }
    protected function getNomTabla($tabla) {
        $nom_tabla = '"'.$this->esquema.'".'.$tabla;
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
        $a_sql[0] = "DROP TABLE IF EXISTS $nom_tabla CASCADE;" ;
        
        return $this->executeSql($a_sql);
    }
    
}