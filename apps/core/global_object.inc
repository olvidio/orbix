<?php

namespace core;

use config\model\Config;
use web;
use menus\model\entity\GestorGrupMenuRole;
use permisos\model as permisos;
use usuarios\model\entity as usuarios;

if (session_id() == "") {
    session_start(); // if no active session we start a new one
}

require_once('apps/permisos/controller/login_obj.php');

$_SESSION['oGestorErrores'] = new GestorErrores();

/*
 * Definir Conexiones
 *
 * No conviene que las conexiones sean persistentes, pues en caso de algún bloqueo
 *  o error afectan a toda la aplicación.
 * De esta manera (no persistente) se cierra la conexión cada vez que acaba el script.
 * 
 */

/*
$oConfigDB = new ConfigDB('tramity'); //de la database comun 
$config = $oConfigDB->getEsquema('public'); 
$oConexion = new dbConnection($config);
$oDBT = $oConexion->getPDO();
*/

// public para todo el mundo
$oConfigDB = new ConfigDB('comun'); //de la database comun 

$config = $oConfigDB->getEsquema('public');
$oConexion = new DBConnection($config);
$oDBPC = $oConexion->getPDO();

$config = $oConfigDB->getEsquema('resto');
$oConexion = new DBConnection($config);
$oDBRC = $oConexion->getPDO();

$user_sfsv = $_SESSION['session_auth']['sfsv'];
switch ($user_sfsv) {
    case 1: //sv
        $esquemav = $_SESSION['session_auth']['esquema'];
        $esquema = \substr($esquemav, 0, -1);
        $esquemaf = $esquema . 'f';
        //comun
        $oConfigDB->setDataBase('comun');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBC = $oConexion->getPDO();
        //sv
        if (ConfigGlobal::is_dmz()) {
            /*
            $oConfigDB->setDataBase('comun'); 
            $config = $oConfigDB->getEsquema($esquemav); 
            $oConexion = new dbConnection($config);
            $oDB = $oConexion->getPDO();
            
            $config = $oConfigDB->getEsquema('publicv'); 
            $oConexion = new dbConnection($config);
            $oDBP = $oConexion->getPDO();

            $config = $oConfigDB->getEsquema('restov'); 
            $oConexion = new dbConnection($config);
            $oDBR = $oConexion->getPDO();
            */
        } else {
            $oConfigDB->setDataBase('sv');
            $config = $oConfigDB->getEsquema($esquemav);
            $oConexion = new DBConnection($config);
            $oDB = $oConexion->getPDO();

            $config = $oConfigDB->getEsquema('publicv');
            $oConexion = new DBConnection($config);
            $oDBP = $oConexion->getPDO();

            $config = $oConfigDB->getEsquema('restov');
            $oConexion = new DBConnection($config);
            $oDBR = $oConexion->getPDO();
        }
        //sv exterior
        $oConfigDB->setDataBase('sv-e');
        $config = $oConfigDB->getEsquema($esquemav);
        $oConexion = new DBConnection($config);
        $oDBE = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $oDBEP = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov');
        $oConexion = new DBConnection($config);
        $oDBER = $oConexion->getPDO();
        break;
    case 2: //sf
        $esquemaf = $_SESSION['session_auth']['esquema'];
        $esquema = \substr($esquemaf, 0, -1);
        $esquemav = $esquema . 'v';
        //comun
        $oConfigDB->setDataBase('comun');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBC = $oConexion->getPDO();

        //sf
        $oConfigDB->setDataBase('sf');
        $config = $oConfigDB->getEsquema($esquemaf);
        $oConexion = new DBConnection($config);
        $oDB = $oConexion->getPDO();
        $oDBE = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicf');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $oDBEP = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restof');
        $oConexion = new DBConnection($config);
        $oDBR = $oConexion->getPDO();

        //sf exterior
        // De momento todo junto. No distingo tunel/dmz
        /*
        $oConfigDB->setDataBase('sf-e');
        $config = $oConfigDB->getEsquema($esquemaf);
        $oConexion = new dbConnection($config);
        $oDBE = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicf');
        $oConexion = new dbConnection($config);
        $oDBEP = $oConexion->getPDO();
        */
        break;
}

if (ConfigGlobal::is_app_installed('dbextern') && !ConfigGlobal::is_dmz()) {
    // Para sincronizar con listas Madrid (SQLSERVER)
    // No en el caso de cr (H-Hv)
    if ((ConfigGlobal::mi_region() != ConfigGlobal::mi_delef()) && !isset($_SESSION['oDBListas'])) {
        try {
            $oConfigDB = new ConfigDB('listas');
            $config = $oConfigDB->getEsquema('public');
            $oConexion = new DBConnection($config);
            $oDBListas = $oConexion->getPDOListas();
        } catch (\PDOException $e) {
            //Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
            echo "/*";
            echo _("No puedo conectar con la base de datos de listas") . ':<br>';
            echo $e->getMessage();
            echo "*/";
            $_SESSION['oDBListas'] = 'error';
        }
    }
}

/********* En el caso cr-stgr, refrescar las vistas **********************/
if ((ConfigGlobal::mi_region() === ConfigGlobal::mi_delef()) && !isset($_SESSION['Refresh'])) {
    try {
        // para el esquema sv
        $views = [
            'd_profesor_latin',
            'd_profesor_ampliacion',
            'd_profesor_director',
            'd_profesor_juramento',
            'd_profesor_stgr',
            'd_publicaciones',
            'd_congresos',
            'd_docencia_stgr',
            'd_titulo_est',
            'p_agregados',
            'p_numerarios',
            'personas_dl',
            'd_teleco_personas_dl',
            'u_centros_dl',
        ];

        if ($user_sfsv == 1) {
            $schema_vf = $esquemav;
        } elseif ($user_sfsv == 2) {
            $schema_vf = $esquemaf;
        }

        $oMatView = new DBView($schema_vf, $user_sfsv, 'interior');
        foreach ($views as $view) {
            $oMatView->setView($view);
            if ($oMatView->ExisteYEsIgual()) {
                //true
                $oMatView->Refresh();
            } else {
                $oMatView->create();
            }
        }

        // para el esquema sv-e
        $views = [
            'd_asistentes_out',
            'd_asistentes_dl',
            'd_cargos_activ_dl',
        ];

        if ($user_sfsv == 1) {
            $schema_vf = $esquemav;
        } elseif ($user_sfsv == 2) {
            $schema_vf = $esquemaf;
        }

        $oMatView = new DBView($schema_vf, $user_sfsv, 'exterior');
        foreach ($views as $view) {
            $oMatView->setView($view);
            if ($oMatView->ExisteYEsIgual()) {
                //true
                $oMatView->Refresh();
            } else {
                $oMatView->create();
            }
        }

        // para el esquema comun
        $views = [
            'av_actividades',
            'xa_tipo_tarifa',
        ];

        $schema = $esquema;

        $oMatView = new DBView($schema, NULL, 'comun');
        foreach ($views as $view) {
            $oMatView->setView($view);
            if ($oMatView->ExisteYEsIgual(TRUE)) {
                //true
                $oMatView->Refresh();
            } else {
                $oMatView->create(TRUE);
            }
        }
        $_SESSION['Refresh'] = 'ok';
    } catch (\PDOException $e) {
        //Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
        echo "/*";
        echo _("No puedo refrescar las vistas") . ':<br>';
        echo $e->getMessage();
        echo "*/";
        $_SESSION['Refresh'] = 'error';
    }
}

if (ConfigGlobal::is_app_installed('menus')) {
    if (empty($_SESSION['iPermMenus'])) { // con hacerlo una vez basta.
        // Grupos
        $oGesGrupo = new usuarios\GestorUsuarioGrupo();
        $cGrupos = $oGesGrupo->getUsuariosGrupos(array('id_usuario' => ConfigGlobal::mi_id_usuario()));
        $iperm_menu = 0;
        foreach ($cGrupos as $UsuarioGrupo) {
            $id_grupo = $UsuarioGrupo->getId_grupo();
            $oGesPermMenu = new usuarios\GestorPermMenu();
            $cPermMenu = $oGesPermMenu->getPermMenus(array('id_usuario' => $id_grupo));
            foreach ($cPermMenu as $oPermMenu) {
                // Or (inclusive or) 	Bits that are set in either $a or $b are set.
                $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
            }
        }
        //echo "perms: $iperm_menu<br>";
        $_SESSION['iPermMenus'] = $iperm_menu;
        $_SESSION['oPerm'] = new permisos\PermDl();
        $_SESSION['oPerm']->setAccion($iperm_menu);
    }
}

// Datos de configuración propios de cada dl.
$_SESSION['oConfig'] = new Config();

// func_tablas. Es necesaria para permisos\PermisosActividades->carregar()...
include_once('func_tablas.php');

// para mantener los permisos por actividades en una variable
if (empty($_SESSION['oPermActividades'])) {
    if (ConfigGlobal::is_app_installed('procesos')) {
        //$_SESSION['oPermActividades'] = new permisos\PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
        $_SESSION['oPermActividades'] = new permisos\PermisosActividades(ConfigGlobal::mi_id_usuario());
    } else {
        $_SESSION['oPermActividades'] = new permisos\PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
    }
    //$oPermActividades = $_SESSION['oPermActividades'];
}

// Para validar los parametros enviados via POST
$oValidator = new web\Hash();
echo $oValidator->validatePost($_POST);

$oPosicion = new web\Posicion($_SERVER['PHP_SELF'], $_POST);