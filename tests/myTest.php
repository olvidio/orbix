<?php

namespace Tests;

use config\model\Config;
use core\ConfigDB;
use core\ConfigGlobal;
use core\DBConnection;
use core\DBView;
use core\GestorErrores;
use permisos\model\PermDl;
use permisos\model\PermisosActividades;
use permisos\model\PermisosActividadesTrue;
use PHPUnit\Framework\TestCase;
use usuarios\model\entity\GestorPermMenu;
use usuarios\model\entity\GestorUsuarioGrupo;

class myTest extends TestCase
{


//use
//include('apps/core/ServerConf.php');
//include('apps/core/ConfigGlobal.php');
//include('apps/core/DBConnection.php');

    public function setUp(): void
    {
        # Turn on error reporting
        error_reporting(E_ALL);

        ConfigGlobal::setTest_mode(TRUE);

        $_SESSION['oGestorErrores'] = new GestorErrores(TRUE);

        $id_usuario = 443;
        $sfsv = 1;
        $id_role = 1;
        $role_pau = 'u';
        $_POST['username'] = 'dani';
        $_POST['password'] = 'massavolssaber';
        $esquema = 'H-dlbv';
        $perms_activ = '';
        $mi_oficina = '';
        $mi_oficina_menu = '';
        $expire = '';
        $mail = '';
        $idioma = '';
        $ordenApellidos = '';
        $id_schema = '';
//si existe, registro la sesión con los permisos
        if (!isset($_SESSION['session_auth'])) {
            $session_auth = ['id_usuario' => $id_usuario,
                'sfsv' => $sfsv,
                'id_role' => $id_role,
                'role_pau' => $role_pau,
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'esquema' => $esquema,
                'perms_activ' => $perms_activ,
                'mi_oficina' => $mi_oficina,
                'mi_oficina_menu' => $mi_oficina_menu,
                'expire' => $expire,
                'mail' => $mail,
                'idioma' => $idioma,
                'ordenApellidos' => $ordenApellidos,
                'mi_id_schema' => $id_schema,];
            $_SESSION['session_auth'] = $session_auth;
        }

//si existe, registro la sesion con la configuración
        if (!isset($_SESSION['config'])) {
            $session_config = array(
                'id_role' => $id_role,
                'role_pau' => $role_pau,
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'perms_activ' => $perms_activ,
                'mi_oficina' => $mi_oficina,
                'mi_oficina_menu' => $mi_oficina_menu,
                'expire' => $expire,
                'mail' => $mail,
                'idioma' => $idioma,
//        'app_installed' => $app_installed,
//        'mod_installed' => $a_mods_installed,
//        'a_apps' => $a_apps,
//        'a_mods' => $a_mods,
            );
            $_SESSION['config'] = $session_config;
        }

// public para todo el mundo
        $oConfigDB = new ConfigDB('comun'); //de la database comun

        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBPC'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('resto');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBRC'] = $oConexion->getPDO();

// public para todo el mundo sólo lectura
        $oConfigDB = new ConfigDB('comun_select'); //de la database comun

        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBPC_Select'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('resto');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBRC_Select'] = $oConexion->getPDO();


        $user_sfsv = $_SESSION['session_auth']['sfsv'];

        $esquemav = $_SESSION['session_auth']['esquema'];
        $esquema = \substr($esquemav, 0, -1);
        $esquemaf = $esquema . 'f';
//común
        $oConfigDB->setDataBase('comun');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBC'] = $oConexion->getPDO();
//común sólo lectura
        $oConfigDB->setDataBase('comun_select');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBC_Select'] = $oConexion->getPDO();

        $oConfigDB->setDataBase('sv');
        $config = $oConfigDB->getEsquema($esquemav);
        $oConexion = new DBConnection($config);
        $GLOBALS['oDB'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBP'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBR'] = $oConexion->getPDO();

        if (ConfigGlobal::is_app_installed('dbextern') && !ConfigGlobal::is_dmz()) {
            // Para sincronizar con listas Madrid (SQLSERVER)
            // No en el caso de cr (H-Hv)
            if ((ConfigGlobal::mi_region() != ConfigGlobal::mi_delef()) && !isset($GLOBALS['oDBListas'])) {
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
                    $oDBListas = 'error';
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

                // Las vistas sólo las actualizo para consulta (interior)
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

                $oMatView = new DBView($schema_vf, $user_sfsv, 'exterior_select');
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

                $oMatView = new DBView($schema, NULL, 'comun_select');
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
                $oGesGrupo = new GestorUsuarioGrupo();
                $cGrupos = $oGesGrupo->getUsuariosGrupos(array('id_usuario' => ConfigGlobal::mi_id_usuario()));
                $iperm_menu = 0;
                foreach ($cGrupos as $UsuarioGrupo) {
                    $id_grupo = $UsuarioGrupo->getId_grupo();
                    $oGesPermMenu = new GestorPermMenu();
                    $cPermMenu = $oGesPermMenu->getPermMenus(array('id_usuario' => $id_grupo));
                    foreach ($cPermMenu as $oPermMenu) {
                        // Or (inclusive or) 	Bits that are set in either $a or $b are set.
                        $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
                    }
                }
                //echo "perms: $iperm_menu<br>";
                $_SESSION['iPermMenus'] = $iperm_menu;
                $_SESSION['oPerm'] = new PermDl();
                $_SESSION['oPerm']->setAccion($iperm_menu);
            }
        }

// Datos de configuración propios de cada dl.
        $oConfig = new Config();
        $_SESSION['oConfig'] = $oConfig;

// func_tablas. Es necesaria para permisos\PermisosActividades->carregar()...
        include_once('apps/core/func_tablas.php');

// para mantener los permisos por actividades en una variable
        if (empty($_SESSION['oPermActividades'])) {
            if (ConfigGlobal::is_app_installed('procesos')) {
                //$_SESSION['oPermActividades'] = new permisos\PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
                $_SESSION['oPermActividades'] = new PermisosActividades(ConfigGlobal::mi_id_usuario());
            } else {
                $_SESSION['oPermActividades'] = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
            }
            //$oPermActividades = $_SESSION['oPermActividades'];
        }

    }
}