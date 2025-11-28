<?php

namespace core;

use DI\ContainerBuilder;
use permisos\model\PermDl;
use permisos\model\PermisosActividades;
use permisos\model\PermisosActividadesTrue;
use src\configuracion\domain\entity\Config;
use src\menus\domain\PermisoMenu;
use src\usuarios\domain\contracts\GrupoRepositoryInterface;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\RoleRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioRepositoryInterface;
use web\Hash;
use web\Posicion;
use zonassacd\model\entity\GestorZona;

if (session_status() !== PHP_SESSION_ACTIVE) {
    // Para detectar el error: "Headers already sent"
    //$file = $line = null; headers_sent($file, $line); die("$file:$line");

    if (!empty($_COOKIE["PHPSESSID"])) {
        session_id($_COOKIE['PHPSESSID']);
        session_start();
    } else {
        // Configure timeout to 30 minutes
        $timeout = 1800;
        $maxlifetime = time() + $timeout;

        // Set the maxlifetime of session
        ini_set("session.gc_maxlifetime", $timeout);

        // Also set the session cookie timeout
        ini_set("session.cookie_lifetime", $timeout);

        //$domain = ConfigGlobal::getDomain();
        // Now start the session
        session_set_cookie_params([
            'lifetime' => $maxlifetime,
            'Secure' => false,
            'HttpOnly' => true,
            'SameSite' => 'Strict',
            //'Domain' => $domain
        ]);
        session_start();
    }
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

// public para todo el mundo sólo lectura
$oConfigDB = new ConfigDB('comun_select'); //de la database comun

$config = $oConfigDB->getEsquema('public');
$oConexion = new DBConnection($config);
$oDBPC_Select = $oConexion->getPDO();

$config = $oConfigDB->getEsquema('resto');
$oConexion = new DBConnection($config);
$oDBRC_Select = $oConexion->getPDO();


$user_sfsv = $_SESSION['session_auth']['sfsv'];
switch ($user_sfsv) {
    case 1: //sv
        $esquemav = $_SESSION['session_auth']['esquema'];
        $esquema = substr($esquemav, 0, -1);
        $esquemaf = $esquema . 'f';
        //común
        $oConfigDB->setDataBase('comun');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBC = $oConexion->getPDO();
        //común sólo lectura
        $oConfigDB->setDataBase('comun_select');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBC_Select = $oConexion->getPDO();
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

        //sv exterior sólo lectura
        $oConfigDB->setDataBase('sv-e_select');
        $config = $oConfigDB->getEsquema($esquemav);
        $oConexion = new DBConnection($config);
        $oDBE_Select = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $oDBEP_Select = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov');
        $oConexion = new DBConnection($config);
        $oDBER_Select = $oConexion->getPDO();
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
        //común sólo lectura
        $oConfigDB->setDataBase('comun_select');
        $config = $oConfigDB->getEsquema($esquema);
        $oConexion = new DBConnection($config);
        $oDBC_Select = $oConexion->getPDO();

        //sf
        $oConfigDB->setDataBase('sf');
        $config = $oConfigDB->getEsquema($esquemaf);
        $oConexion = new DBConnection($config);
        $oDB = $oConexion->getPDO();
        $oDBE = $oConexion->getPDO();
        $oDBE_Select = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicf');
        $oConexion = new DBConnection($config);
        $oDBP = $oConexion->getPDO();
        $oDBEP = $oConexion->getPDO();
        $oDBEP_Select = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restof');
        $oConexion = new DBConnection($config);
        $oDBR = $oConexion->getPDO();
        $oDBER = $oConexion->getPDO();
        $oDBER_Select = $oConexion->getPDO();

        //sf lectura
        break;
}

if (ConfigGlobal::is_app_installed('dbextern') && !ConfigGlobal::is_dmz()) {
    // Para sincronizar con listas Madrid (SQLSERVER)
    // No en el caso de cr (H-Hv)
    if ((ConfigGlobal::mi_region() != ConfigGlobal::mi_delef())) {
        try {
            $oConfigDB = new ConfigDB('listas');
            $config = $oConfigDB->getEsquema('public');
            $oConexion = new DBConnection($config);
            $oDBListas = $oConexion->getPDOListas();
        } catch (\InvalidArgumentException $e) {
            // Esto capturará el error si la configuración está incompleta
            //Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
            echo "/*";
            echo $e->getMessage() . "<br>";
            echo "*/";
            $oDBListas = 'error';
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

// 2) Contenedor DI (si no existe ya) y exponerlo globalmente
if (!isset($GLOBALS['container'])) {
    $builder = new ContainerBuilder();

    // Cargar rutas por módulo: cada módulo define sus rutas en src/<modulo>/config/routes.php
    $dependenciesFiles = glob(__DIR__ . '/../../src/*/config/dependencies.php');
    if (is_array($dependenciesFiles)) {
        foreach ($dependenciesFiles as $dependenciesFile) {
            $builder->addDefinitions($dependenciesFile);
        }
    }

    // Cache de compilación en producción
    if (class_exists(ConfigGlobal::class) && !ConfigGlobal::is_debug_mode()) {
        $cacheDir = __DIR__ . '/../var/cache/php-di';
        if (!is_dir($cacheDir)) {
            if (!mkdir($cacheDir, 0775, true) && !is_dir($cacheDir)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $cacheDir));
            }
        }
        $builder->enableCompilation($cacheDir);
        $builder->writeProxiesToFile(true, $cacheDir . '/proxies');
    }

    $GLOBALS['container'] = $builder->build();
}
$container = $GLOBALS['container'];


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
        $UsuarioGrupoRepository = $GLOBALS['container']->get(UsuarioGrupoRepositoryInterface::class);
        $GrupoRepository = $GLOBALS['container']->get(GrupoRepositoryInterface::class);
        $UsuarioRepository = $GLOBALS['container']->get(UsuarioRepositoryInterface::class);
        $RoleRepository = $GLOBALS['container']->get(RoleRepositoryInterface::class);
        $aRoles = $RoleRepository->getArrayRoles();

        $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => ConfigGlobal::mi_id_usuario()));
        $iperm_menu = 0;
        $PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
        foreach ($cGrupos as $UsuarioGrupo) {
            $id_grupo = $UsuarioGrupo->getId_grupo();
            $cPermMenu = $PermMenuRepository->getPermMenus(array('id_usuario' => $id_grupo));
            foreach ($cPermMenu as $oPermMenu) {
                // Or (inclusive or) 	Bits that are set in either $a or $b are set.
                $iperm_menu = $iperm_menu | $oPermMenu->getMenu_perm();
            }
        }
        // añadir el permiso de 'jefeZona'
        $isJefeZona = FALSE;
        $id_usuario = ConfigGlobal::mi_id_usuario();
        $oMiUsuario = $UsuarioRepository->findById($id_usuario);
        $id_role = $oMiUsuario->getId_role();
        if (!empty($aRoles[$id_role]) && ($aRoles[$id_role] === 'p_sacd')) {
            $id_nom = $oMiUsuario->getId_pauAsString();
            if (!empty($id_nom)) {
                $oGesZonas = new GestorZona();
                $isJefeZona = $oGesZonas->isJefeZona($id_nom);
            }
        }
        if ($isJefeZona) {
            $oPermisoMenu = new PermisoMenu();
            $permissions = $oPermisoMenu->omplir();
            $perm_jefe_zona = $permissions['jefeZona'];
            $iperm_menu = $iperm_menu | $perm_jefe_zona;
        }
        //echo "perms: $iperm_menu<br>";
        $_SESSION['iPermMenus'] = $iperm_menu;
        $_SESSION['oPerm'] = new PermDl();
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
        $_SESSION['oPermActividades'] = new PermisosActividades(ConfigGlobal::mi_id_usuario());
    } else {
        $_SESSION['oPermActividades'] = new PermisosActividadesTrue(ConfigGlobal::mi_id_usuario());
    }
    //$oPermActividades = $_SESSION['oPermActividades'];
}


session_write_close();
// Para validar los parámetros enviados via POST
$oValidator = new Hash();
echo $oValidator->validatePost($_POST);

$oPosicion = new Posicion($_SERVER['PHP_SELF'], $_POST);

