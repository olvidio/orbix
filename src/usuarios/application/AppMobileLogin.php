<?php

namespace src\usuarios\application;

use src\shared\infrastructure\persistence\ConfigDB;
use src\shared\config\ConfigGlobal;
use src\shared\infrastructure\persistence\DBConnection;
use src\shared\infrastructure\persistence\postgresql\DBPropiedades;
use src\usuarios\domain\entity\Usuario;
use src\usuarios\domain\PasswordHasher;
use src\usuarios\domain\Verify2fa;

/**
 * Login para clientes móviles (JSON): misma validación que login_obj sin HTML.
 * Establece $_SESSION['session_auth'] y $_SESSION['config'] en caso de éxito.
 */
final class AppMobileLogin
{
    /**
     * @param array{username?:string,password?:string,esquema?:string,verification_code?:string} $input
     * @return array{ok:bool,code?:string,mensaje?:string,data?:array}
     */
    public static function attempt(array $input): array
    {
        $esquema_web = getenv('ESQUEMA') ?: '';
        $ubicacion = getenv('UBICACION');
        $private = getenv('PRIVATE');

        $_SESSION['sfsv'] = $ubicacion;

        if (!empty($esquema_web)) {
            $oDBPropiedades = new DBPropiedades();
            $a_posibles_esquemas = $oDBPropiedades->array_posibles_esquemas(false, true);
            if (!in_array($esquema_web, $a_posibles_esquemas, true)) {
                return [
                    'ok' => false,
                    'code' => 'invalid_schema_env',
                    'mensaje' => sprintf(_('No existe este equema: %s'), $esquema_web),
                ];
            }
        }

        $username = trim((string)($input['username'] ?? ''));
        $password = (string)($input['password'] ?? '');
        $esquemaIn = trim((string)($input['esquema'] ?? ''));
        $verification_code = trim((string)($input['verification_code'] ?? ''));

        if ($username === '' || $password === '') {
            return [
                'ok' => false,
                'code' => 'missing_credentials',
                'mensaje' => _('Usuario y contraseña obligatorios'),
            ];
        }

        $_SESSION['private'] = $private;

        $esquema = $esquemaIn !== '' ? $esquemaIn : $esquema_web;
        if ($esquema === '') {
            return [
                'ok' => false,
                'code' => 'missing_schema',
                'mensaje' => _('Esquema no indicado'),
            ];
        }

        $sfsv = 0;
        $oDB_Select = self::pdoForEsquema($esquema, $sfsv);
        if ($oDB_Select === null) {
            return [
                'ok' => false,
                'code' => 'invalid_schema',
                'mensaje' => _('Esquema no válido'),
            ];
        }

        $aWhere = ['usuario' => $username];
        $query = 'SELECT * FROM aux_usuarios WHERE usuario = :usuario';
        $oDBSt = $oDB_Select->prepare($query);
        if ($oDBSt === false) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, 'app_login.prepare', __LINE__, __FILE__);

            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Error de servidor')];
        }
        if ($oDBSt->execute($aWhere) === false) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, 'app_login.execute', __LINE__, __FILE__);

            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Error de servidor')];
        }

        $password_db = null;
        $oDBSt->bindColumn('password', $password_db, \PDO::PARAM_STR);
        $row = $oDBSt->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return [
                'ok' => false,
                'code' => 'invalid_credentials',
                'mensaje' => _('Usuario o contraseña incorrectos'),
            ];
        }
        $row['password'] = $password_db;
        $MiUsuario = (new Usuario())->setAllAttributes($row);

        $oCrypt = new PasswordHasher();
        if ($oCrypt->encode($password, $password_db) !== $password_db) {
            return [
                'ok' => false,
                'code' => 'invalid_credentials',
                'mensaje' => _('Usuario o contraseña incorrectos'),
            ];
        }

        $expire = '';
        if ($MiUsuario->isCambio_password() || $password === '1ªVegada') {
            $expire = 1;
        }

        if ($MiUsuario->isHas_2fa() && empty($row['secret_2fa'])) {
            $url_base = ConfigGlobal::getWeb() . '/';
            $a_params = [
                'username' => $username,
                'ubicacion' => $ubicacion,
                'esquema' => $esquema,
                'url_base' => $url_base,
            ];
            $ayuda_url = $url_base . 'frontend/usuarios/controller/ayuda_2fa_reset.php?' . http_build_query($a_params);

            return [
                'ok' => false,
                'code' => 'need_2fa_setup',
                'mensaje' => _('Debe completar la configuración 2FA'),
                'data' => ['ayuda_url' => $ayuda_url],
            ];
        }

        if ($MiUsuario->isHas_2fa() && !empty($row['secret_2fa'])) {
            if ($verification_code === '') {
                return [
                    'ok' => false,
                    'code' => 'need_2fa',
                    'mensaje' => _('Código 2FA requerido'),
                ];
            }
            if (!Verify2fa::verify_2fa_code($verification_code, $row['secret_2fa'])) {
                return [
                    'ok' => false,
                    'code' => 'invalid_2fa',
                    'mensaje' => _('Código 2FA inválido'),
                ];
            }
        }

        $id_usuario = (int)$row['id_usuario'];
        $id_role = (int)$row['id_role'];

        $oConfigDB = new ConfigDB('comun_select');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBCP_Select = $oConexion->getPDO();
        $queryr = 'SELECT * FROM aux_roles WHERE id_role = ' . $id_role;
        $oDBPSt = $oDBCP_Select->query($queryr);
        if ($oDBPSt === false) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDBCP_Select, 'app_login.role', __LINE__, __FILE__);

            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Error de servidor')];
        }
        $row2 = $oDBPSt->fetch(\PDO::FETCH_ASSOC);
        if ($row2 === false) {
            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Rol no encontrado')];
        }
        $role_pau = $row2['pau'];

        if (ConfigGlobal::is_dmz()) {
            $role_dmz = $row2['dmz'] ?? null;
            if (empty($role_dmz)) {
                return [
                    'ok' => false,
                    'code' => 'dmz_denied',
                    'mensaje' => _('Acceso no permitido'),
                ];
            }
        }

        $mail = (string)($row['email'] ?? '');

        $a_mods = self::getModsPosibles();
        $a_apps = self::getAppsPosibles();
        $a_mods_installed = self::getModsInstalados($oDB_Select);
        $app = [];
        foreach ($a_mods_installed as $id_mod => $param) {
            $app[] = self::getAppsMods($id_mod);
            $app[] = self::getApps($id_mod);
        }
        $app_installed = $app !== [] ? array_merge(...array_values($app)) : [];
        $app_installed = array_unique($app_installed);

        $query_idioma = sprintf(
            "select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ",
            $id_usuario,
            'idioma'
        );
        $oDBStI = $oDB_Select->query($query_idioma);
        $rowI = $oDBStI ? $oDBStI->fetch(\PDO::FETCH_ASSOC) : false;
        $idioma = ($rowI === false) ? '' : (string)($rowI['preferencia'] ?? '');

        $query_ordenApellidos = sprintf(
            "select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ",
            $id_usuario,
            'ordenApellidos'
        );
        $oDBStoA = $oDB_Select->query($query_ordenApellidos);
        $rowO = $oDBStoA ? $oDBStoA->fetch(\PDO::FETCH_ASSOC) : false;
        $ordenApellidos = ($rowO === false) ? '' : (string)($rowO['preferencia'] ?? '');

        $oDBPropiedades = new DBPropiedades();
        $id_schema = $oDBPropiedades->getIdSchema($esquema);

        $session_auth = [
            'id_usuario' => $id_usuario,
            'MiUsuario' => $MiUsuario,
            'sfsv' => $sfsv,
            'id_role' => $id_role,
            'role_pau' => $role_pau,
            'username' => $username,
            'password' => $password,
            'esquema' => $esquema,
            'perms_activ' => '',
            'mi_oficina' => '',
            'mi_oficina_menu' => '',
            'expire' => $expire,
            'mail' => $mail,
            'idioma' => $idioma,
            'ordenApellidos' => $ordenApellidos,
            'mi_id_schema' => $id_schema,
        ];
        $_SESSION['session_auth'] = $session_auth;

        $session_config = [
            'id_role' => $id_role,
            'role_pau' => $role_pau,
            'username' => $username,
            'password' => $password,
            'perms_activ' => '',
            'mi_oficina' => '',
            'mi_oficina_menu' => '',
            'expire' => $expire,
            'mail' => $mail,
            'idioma' => $idioma,
            'app_installed' => $app_installed,
            'mod_installed' => $a_mods_installed,
            'a_apps' => $a_apps,
            'a_mods' => $a_mods,
        ];
        $_SESSION['config'] = $session_config;

        $time_expire_cookie = time() + (86400 * 30);
        $arr_cookie_options = [
            'expires' => $time_expire_cookie,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Strict',
        ];
        setcookie('esquema', $esquema, $arr_cookie_options);
        setcookie('idioma', $idioma, $arr_cookie_options);

        return [
            'ok' => true,
            'data' => [
                'id_usuario' => $id_usuario,
                'username' => $username,
                'esquema' => $esquema,
                'require_password_change' => $expire === 1,
            ],
        ];
    }

    private static function pdoForEsquema(string $esquema, ?int &$sfsv): ?\PDO
    {
        $sfsv = null;
        if (str_ends_with($esquema, 'v')) {
            $sfsv = 1;
            $oConfigDB = new ConfigDB('sv-e_select');
            $config = $oConfigDB->getEsquema($esquema);
            $oConexion = new DBConnection($config);

            return $oConexion->getPDO();
        }
        if (str_ends_with($esquema, 'f')) {
            $sfsv = 2;
            $oConfigDB = new ConfigDB('sf-e');
            $config = $oConfigDB->getEsquema($esquema);
            $oConexion = new DBConnection($config);

            return $oConexion->getPDO();
        }

        return null;
    }

    /** @return array<string, int> */
    private static function getAppsPosibles(): array
    {
        $oConfigDB = new ConfigDB('comun_select');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP_Select = $oConexion->getPDO();
        $a_apps = [];
        foreach ($oDBP_Select->query('SELECT * FROM m0_apps') as $aDades) {
            $a_apps[$aDades['nom']] = $aDades['id_app'];
        }

        return $a_apps;
    }

    /** @return array<int, array{nom: string, mods_req: mixed, apps_req: mixed}> */
    private static function getModsPosibles(): array
    {
        $oConfigDB = new ConfigDB('comun_select');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP_Select = $oConexion->getPDO();
        $a_mods = [];
        foreach ($oDBP_Select->query('SELECT * FROM m0_modulos') as $aDades) {
            $id_mod = $aDades['id_mod'];
            $a_mods[$id_mod] = [
                'nom' => $aDades['nom'],
                'mods_req' => $aDades['mods_req'],
                'apps_req' => $aDades['apps_req'],
            ];
        }

        return $a_mods;
    }

    /** @return array<int, string> */
    private static function getModsInstalados(\PDO $oDB_Select): array
    {
        $a_mods = self::getModsPosibles();
        $a_mods_installed = [];
        foreach ($oDB_Select->query('SELECT * FROM m0_mods_installed_dl WHERE active = \'t\'') as $aDades) {
            $id_mod = $aDades['id_mod'];
            $nom_mod = $a_mods[$id_mod]['nom'];
            $a_mods_installed[$id_mod] = $nom_mod;
        }

        return $a_mods_installed;
    }

    /** @return list<string> */
    private static function getAppsMods(int|string $id_mod): array
    {
        $apps = [];
        $a_mods = self::getModsPosibles();
        $ajson = $a_mods[$id_mod]['mods_req'];
        if (preg_match('/^{(.*)}$/', (string)$ajson, $matches)) {
            if (!empty($matches[1])) {
                $apps_installed = [];
                $mod_in = str_getcsv($matches[1]);
                foreach ($mod_in as $mod) {
                    $apps_installed[] = self::getApps($mod);
                }
                $apps = array_merge(...array_values($apps_installed));
            }
        }

        return $apps;
    }

    /** @return list<string> */
    private static function getApps(int|string $id_mod): array
    {
        $apps = [];
        $a_mods = self::getModsPosibles();
        $ajson = $a_mods[$id_mod]['apps_req'];
        if (preg_match('/^{(.*)}$/', (string)$ajson, $matches)) {
            $app_in = str_getcsv($matches[1]);
            foreach ($app_in as $app) {
                $apps[] = $app;
            }
        }

        return $apps;
    }
}
