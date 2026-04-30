<?php

namespace Tests;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBView;
use src\shared\infrastructure\logging\GestorErrores;
use DI\ContainerBuilder;
use PDOException;
use src\permisos\domain\PermDl;
use src\permisos\domain\PermisosActividades;
use src\permisos\domain\PermisosActividadesTrue;
use PHPUnit\Framework\TestCase;
use src\configuracion\application\ObtenerConfigSnapshot;
use src\usuarios\domain\contracts\PermMenuRepositoryInterface;
use src\usuarios\domain\contracts\UsuarioGrupoRepositoryInterface;
use src\usuarios\domain\entity\Usuario;

class myTest extends TestCase
{
    /**
     * Cada setUp() llegaba a abrir ~16 PDO; si no se liberan antes del siguiente test
     * se agota max_connections en Postgres. Reutilizamos un solo juego por proceso PHPUnit.
     */
    private static bool $pdoTestBootstrapHecho = false;

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

        if (!isset($_SESSION['session_auth']['MiUsuario'])) {
            $uid = (int)($_SESSION['session_auth']['id_usuario'] ?? $id_usuario);
            $rid = (int)($_SESSION['session_auth']['id_role'] ?? $id_role);
            $oMiUsuario = new Usuario();
            $oMiUsuario->setId_usuario($uid);
            $oMiUsuario->setId_role($rid);
            $_SESSION['session_auth']['MiUsuario'] = $oMiUsuario;
        }

        if (!isset($_SESSION['oPerm'])) {
            $_SESSION['oPerm'] = new class {
                public function have_perm_oficina(string $p): bool
                {
                    return false;
                }
            };
        }

        //si existe, registro la sesión con la configuración
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
                // 'app_installed' => $app_installed,
                // 'mod_installed' => $a_mods_installed,
                // 'a_apps' => $a_apps,
                // 'a_mods' => $a_mods,
            );
            $_SESSION['config'] = $session_config;
        }

        $user_sfsv = (int)($_SESSION['session_auth']['sfsv'] ?? 1);
        $esquemav = (string)($_SESSION['session_auth']['esquema'] ?? 'H-dlbv');
        $esquema = substr($esquemav, 0, -1);
        $esquemaf = $esquema . 'f';

        $this->bootstrapConexionesBdTestUnaVezPorProceso($esquemav, $esquema, $esquemaf);

        // 2) Contenedor DI (si no existe ya) y exponerlo globalmente
        if (!isset($GLOBALS['container'])) {
            $builder = new ContainerBuilder();

            // Cargar rutas por módulo: cada módulo define sus rutas en src/<modulo>/config/routes.php
            $dependenciesFiles = glob(__DIR__ . '/../src/*/config/dependencies.php');
            if (is_array($dependenciesFiles)) {
                foreach ($dependenciesFiles as $dependenciesFile) {
                    $builder->addDefinitions($dependenciesFile);
                }
            }

            // Cache de compilación en producción
            if (class_exists(ConfigGlobal::class) && !ConfigGlobal::is_debug_mode()) {
                $cacheDir = __DIR__ . '/../../var/cache/php-di';
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
        if (!isset($_SESSION['Refresh']) && (ConfigGlobal::mi_region() === ConfigGlobal::mi_delef())) {
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

                if ($user_sfsv === 1) {
                    $schema_vf = $esquemav;
                } elseif ($user_sfsv === 2) {
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

                if ($user_sfsv === 1) {
                    $schema_vf = $esquemav;
                } elseif ($user_sfsv === 2) {
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
            } catch (PDOException $e) {
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
                $cGrupos = $UsuarioGrupoRepository->getUsuariosGrupos(array('id_usuario' => ConfigGlobal::mi_id_usuario()));
                $iperm_menu = 0;
                foreach ($cGrupos as $UsuarioGrupo) {
                    $id_grupo = $UsuarioGrupo->getId_grupo();
                    $PermMenuRepository = $GLOBALS['container']->get(PermMenuRepositoryInterface::class);
                    $cPermMenu = $PermMenuRepository->getPermMenus(array('id_usuario' => $id_grupo));
                    foreach ($cPermMenu as $oPermMenu) {
                        // Or (inclusive or) 	Bits that are set in either $a or $b are set.
                        $iperm_menu |= $oPermMenu->getMenu_perm();
                    }
                }
                //echo "perms: $iperm_menu<br>";
                $_SESSION['iPermMenus'] = $iperm_menu;
                $_SESSION['oPerm'] = new PermDl();
                $_SESSION['oPerm']->setAccion($iperm_menu);
            }
        }

        // Datos de configuración propios de cada dl.
        $_SESSION['oConfig'] = $GLOBALS['container']->get(ObtenerConfigSnapshot::class)->execute();

        // func_tablas. Es necesaria para permisos\PermisosActividades->carregar()...
        // Usamos una ruta absoluta para asegurar que se encuentre el archivo sin importar desde dónde se ejecute el test
        include_once(__DIR__ . '/../src/shared/domain/helpers/func_tablas.php');

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

    /**
     * Abre todas las PDO de integración una sola vez por proceso PHPUnit.
     */
    private function bootstrapConexionesBdTestUnaVezPorProceso(string $esquemav, string $esquema, string $esquemaf): void
    {
        if (self::$pdoTestBootstrapHecho) {
            return;
        }

        // public para todo el mundo
        $oConfigDB = new ConfigDB('comun'); //de la database comun

        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBPC'] = $oConexion->getPDO();

        // Varias tablas (p. ej. publicv.d_asistentes_de_paso) requieren id_schema NOT NULL coherente con la DL.
        $esqSession = (string) ($_SESSION['session_auth']['esquema'] ?? '');
        $mis = $_SESSION['session_auth']['mi_id_schema'] ?? null;
        if ($esqSession !== '' && ($mis === '' || $mis === null)) {
            try {
                $st = $GLOBALS['oDBPC']->prepare('SELECT id FROM public.db_idschema WHERE schema = :schema LIMIT 1');
                $st->execute(['schema' => $esqSession]);
                $idSchemaRow = $st->fetchColumn();
                if ($idSchemaRow !== false) {
                    $_SESSION['session_auth']['mi_id_schema'] = (int) $idSchemaRow;
                }
            } catch (\Throwable) {
            }
        }

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

        //sv
        $oConfigDB->setDataBase('sv');
        $config = $oConfigDB->getEsquema($esquemav);
        $oConexion = new DBConnection($config);
        $GLOBALS['oDB'] = $oConexion->getPDO();

        $oConfigSf = new ConfigDB('sf');
        $configSf = $oConfigSf->getEsquema($esquemaf);
        $oConexionSf = new DBConnection($configSf);
        $GLOBALS['oDBF'] = $oConexionSf->getPDO();

        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBP'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBR'] = $oConexion->getPDO();

        //sv-e
        $oConfigDB->setDataBase('sv-e');
        $config = $oConfigDB->getEsquema($esquemav);
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBE'] = $oConexion->getPDO();
        $GLOBALS['oDBE_Select'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBEP'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBER'] = $oConexion->getPDO();

        //sv exterior sólo lectura
        $oConfigDB->setDataBase('sv-e_select');
        $config = $oConfigDB->getEsquema($esquemav);
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBE_Select'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('publicv');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBEP_Select'] = $oConexion->getPDO();

        $config = $oConfigDB->getEsquema('restov');
        $oConexion = new DBConnection($config);
        $GLOBALS['oDBER_Select'] = $oConexion->getPDO();

        if (ConfigGlobal::is_app_installed('dbextern') && !ConfigGlobal::is_dmz()) {
            // Para sincronizar con listas Madrid (SQLSERVER)
            // No en el caso de cr (H-Hv)
            if ((ConfigGlobal::mi_region() !== ConfigGlobal::mi_delef()) && !isset($GLOBALS['oDBListas'])) {
                try {
                    $oConfigDB = new ConfigDB('listas');
                    $config = $oConfigDB->getEsquema('public');
                    $oConexion = new DBConnection($config);
                    $oDBListas = $oConexion->getPDOListas();
                } catch (PDOException $e) {
                    //Hay que poner el mensaje entre /* ... */ para que el script que carga a continuación lo interprete como un comentario.
                    echo "/*";
                    echo _("No puedo conectar con la base de datos de listas") . ':<br>';
                    echo $e->getMessage();
                    echo "*/";
                    $oDBListas = 'error';
                }
            }
        }

        self::$pdoTestBootstrapHecho = true;
    }
}