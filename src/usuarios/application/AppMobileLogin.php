<?php

namespace src\usuarios\application;

use src\shared\infrastructure\logging\GestorErrores;
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
     * @return array{ok: bool, code?: string, mensaje?: string, data?: array<string, mixed>}
     */
    public function execute(array $input): array
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
        $ubicacionStr = (string) $ubicacion;
        $oDB_Select = self::pdoForEsquema($esquema, $sfsv, $ubicacionStr);
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
            self::logPdoError($oDB_Select, 'app_login.prepare');

            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Error de servidor')];
        }
        if ($oDBSt->execute($aWhere) === false) {
            self::logPdoError($oDB_Select, 'app_login.execute');

            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Error de servidor')];
        }

        $password_db = null;
        $oDBSt->bindColumn('password', $password_db, \PDO::PARAM_STR);
        $row = $oDBSt->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($row)) {
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

        $secret2fa = is_scalar($row['secret_2fa'] ?? null) ? (string) $row['secret_2fa'] : '';
        if ($MiUsuario->isHas_2fa() && $secret2fa === '') {
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

        if ($MiUsuario->isHas_2fa()) {
            if ($verification_code === '') {
                return [
                    'ok' => false,
                    'code' => 'need_2fa',
                    'mensaje' => _('Código 2FA requerido'),
                ];
            }
            if (!Verify2fa::verify_2fa_code($verification_code, $secret2fa)) {
                return [
                    'ok' => false,
                    'code' => 'invalid_2fa',
                    'mensaje' => _('Código 2FA inválido'),
                ];
            }
        }

        $id_usuario = isset($row['id_usuario']) && is_numeric($row['id_usuario']) ? (int) $row['id_usuario'] : 0;
        $id_role = isset($row['id_role']) && is_numeric($row['id_role']) ? (int) $row['id_role'] : 0;

        $oConfigDB = new ConfigDB('comun_select');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBCP_Select = $oConexion->getPDO();
        $queryr = 'SELECT * FROM aux_roles WHERE id_role = ' . $id_role;
        $oDBPSt = $oDBCP_Select->query($queryr);
        if ($oDBPSt === false) {
            self::logPdoError($oDBCP_Select, 'app_login.role');

            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Error de servidor')];
        }
        $row2 = $oDBPSt->fetch(\PDO::FETCH_ASSOC);
        if (!is_array($row2)) {
            return ['ok' => false, 'code' => 'server_error', 'mensaje' => _('Rol no encontrado')];
        }
        $role_pau = is_scalar($row2['pau'] ?? null) ? (string) $row2['pau'] : '';

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

        $mail = is_scalar($row['email'] ?? null) ? (string) $row['email'] : '';

        $a_mods = self::getModsPosibles();
        $a_apps = self::getAppsPosibles();
        $a_mods_installed = self::getModsInstalados($oDB_Select);
        $app_installed = [];
        foreach ($a_mods_installed as $id_mod => $param) {
            foreach (self::getAppsMods($id_mod) as $appName) {
                $app_installed[] = $appName;
            }
            foreach (self::getApps($id_mod) as $appName) {
                $app_installed[] = $appName;
            }
        }
        $app_installed = array_values(array_unique($app_installed));

        $query_idioma = sprintf(
            "select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ",
            $id_usuario,
            'idioma'
        );
        $oDBStI = $oDB_Select->query($query_idioma);
        $rowI = ($oDBStI !== false) ? $oDBStI->fetch(\PDO::FETCH_ASSOC) : false;
        $idioma = is_array($rowI) && is_scalar($rowI['preferencia'] ?? null)
            ? (string) $rowI['preferencia']
            : '';

        $query_ordenApellidos = sprintf(
            "select * from web_preferencias where id_usuario = '%s' and tipo = '%s' ",
            $id_usuario,
            'ordenApellidos'
        );
        $oDBStoA = $oDB_Select->query($query_ordenApellidos);
        $rowO = ($oDBStoA !== false) ? $oDBStoA->fetch(\PDO::FETCH_ASSOC) : false;
        $ordenApellidos = is_array($rowO) && is_scalar($rowO['preferencia'] ?? null)
            ? (string) $rowO['preferencia']
            : '';

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
            'samesite' => 'Lax',
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

    private static function pdoForEsquema(string &$esquema, int &$sfsv, string $ubicacion): ?\PDO
    {
        $sfsv = 0;
        $private = (string) getenv('PRIVATE');
        $useSfDb = ($ubicacion === 'sf' || $private === 'sf');

        if (str_ends_with($esquema, 'v')) {
            $sfsv = 1;
            try {
                $oConfigDB = new ConfigDB('sv-e_select');
                $config = $oConfigDB->getEsquema($esquema);
                $oConexion = new DBConnection($config);

                return $oConexion->getPDO();
            } catch (\PDOException) {
                return null;
            }
        }

        if (str_ends_with($esquema, 'f')) {
            if ($useSfDb) {
                try {
                    $sfsv = 2;
                    $oConfigDB = new ConfigDB('sf-e');
                    $config = $oConfigDB->getEsquema($esquema);
                    $oConexion = new DBConnection($config);

                    return $oConexion->getPDO();
                } catch (\PDOException) {
                    $esquema = substr($esquema, 0, -1);
                    $sfsv = 0;
                }
            } else {
                $esquema = substr($esquema, 0, -1);
            }
        }

        if ($esquema === '') {
            return null;
        }

        try {
            $oConfigDB = new ConfigDB('comun_select');
            $config = $oConfigDB->getEsquema($esquema);
            $oConexion = new DBConnection($config);

            return $oConexion->getPDO();
        } catch (\PDOException) {
            return null;
        }
    }

    /** @return array<string, int> */
    private static function getAppsPosibles(): array
    {
        $oConfigDB = new ConfigDB('comun_select');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBP_Select = $oConexion->getPDO();
        $a_apps = [];
        $stmt = $oDBP_Select->query('SELECT * FROM m0_apps');
        if ($stmt === false) {
            return [];
        }
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['nom'], $aDades['id_app'])) {
                continue;
            }
            $a_apps[(string) $aDades['nom']] = (int) $aDades['id_app'];
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
        $stmt = $oDBP_Select->query('SELECT * FROM m0_modulos');
        if ($stmt === false) {
            return [];
        }
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_mod'], $aDades['nom'])) {
                continue;
            }
            $id_mod = $aDades['id_mod'];
            $a_mods[$id_mod] = [
                'nom' => (string) $aDades['nom'],
                'mods_req' => $aDades['mods_req'] ?? null,
                'apps_req' => $aDades['apps_req'] ?? null,
            ];
        }

        return $a_mods;
    }

    /** @return array<int, string> */
    private static function getModsInstalados(\PDO $oDB_Select): array
    {
        $a_mods = self::getModsPosibles();
        $a_mods_installed = [];
        $stmt = $oDB_Select->query('SELECT * FROM m0_mods_installed_dl WHERE active = \'t\'');
        if ($stmt === false) {
            return [];
        }
        foreach ($stmt as $aDades) {
            if (!is_array($aDades) || !isset($aDades['id_mod'])) {
                continue;
            }
            $id_mod = is_numeric($aDades['id_mod']) ? (int) $aDades['id_mod'] : 0;
            if ($id_mod === 0 || !isset($a_mods[$id_mod])) {
                continue;
            }
            $a_mods_installed[$id_mod] = $a_mods[$id_mod]['nom'];
        }

        return $a_mods_installed;
    }

    /** @return list<string> */
    private static function getAppsMods(int|string $id_mod): array
    {
        $apps = [];
        $a_mods = self::getModsPosibles();
        if (!isset($a_mods[$id_mod])) {
            return [];
        }
        $ajson = $a_mods[$id_mod]['mods_req'];
        $ajsonStr = is_scalar($ajson) ? (string) $ajson : '';
        if (preg_match('/^{(.*)}$/', $ajsonStr, $matches)) {
            if ($matches[1] !== '') {
                $mod_in = str_getcsv($matches[1]);
                foreach ($mod_in as $mod) {
                    if (!is_string($mod) || $mod === '') {
                        continue;
                    }
                    foreach (self::getApps($mod) as $appName) {
                        $apps[] = $appName;
                    }
                }
            }
        }

        return $apps;
    }

    /** @return list<string> */
    private static function getApps(int|string $id_mod): array
    {
        $apps = [];
        $a_mods = self::getModsPosibles();
        if (!isset($a_mods[$id_mod])) {
            return [];
        }
        $ajson = $a_mods[$id_mod]['apps_req'];
        $ajsonStr = is_scalar($ajson) ? (string) $ajson : '';
        if (preg_match('/^{(.*)}$/', $ajsonStr, $matches)) {
            $app_in = str_getcsv($matches[1]);
            foreach ($app_in as $app) {
                if (is_string($app) && $app !== '') {
                    $apps[] = $app;
                }
            }
        }

        return $apps;
    }

    private static function logPdoError(\PDO $db, string $key): void
    {
        if (isset($_SESSION['oGestorErrores']) && $_SESSION['oGestorErrores'] instanceof GestorErrores) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($db, $key, (string) __LINE__, __FILE__);
        }
    }
}
