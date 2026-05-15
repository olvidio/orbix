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
 * Caso de uso del login web.
 *
 * Logica pura: valida credenciales, 2FA y permisos DMZ, y construye los
 * arrays de sesion. NO toca `$_SESSION`, NO hace `setcookie`, NO hace
 * `header()` ni `echo` — eso es responsabilidad del controlador frontend
 * (`frontend/usuarios/controller/login.php`), que es quien sabe como
 * presentar el resultado (renderizar form con error, redirigir a la ayuda
 * de 2FA, o rellenar sesion y continuar).
 *
 * Codigos de error usados (coinciden con el mapeo de mensajes del formulario):
 *   - 1: usuario o password no validos.
 *   - 2: usuario sin permiso DMZ.
 *   - 3: se requiere codigo 2FA.
 *   - 4: codigo 2FA invalido.
 *
 * Casos especiales:
 *   - `2fa_setup_pendiente`: el usuario tiene `has_2fa=true` pero no hay
 *     `secret_2fa` en BD. El controlador debe redirigir a la pagina de
 *     ayuda para que complete la configuracion.
 */
final class LoginProcesar
{
    /**
     * @param array{username?:string, password?:string, esquema?:string, verification_code?:string} $input
     * @param string $esquemaWeb Esquema forzado por la variable de entorno ESQUEMA ('' si no hay).
     * @param string $ubicacion Valor de getenv('UBICACION') (p. ej. «sf» solo en entrada sf; si no, esquema «…f» se trata como comun sin sufijo).
     *
     * @return array{
     *     ok: bool,
     *     error?: int,
     *     redirect_ayuda_2fa?: array{username:string, ubicacion:string, esquema:string},
     *     session_auth?: array<string,mixed>,
     *     session_config?: array<string,mixed>,
     *     esquema?: string,
     *     idioma?: string,
     *     sfsv?: int
     * }
     */
    public function execute(array $input, string $esquemaWeb, string $ubicacion): array
    {
        $username = (string)($input['username'] ?? '');
        $password = (string)($input['password'] ?? '');
        $esquemaIn = (string)($input['esquema'] ?? '');
        $verification_code = (string)($input['verification_code'] ?? '');

        $esquema = empty($esquemaIn) ? $esquemaWeb : $esquemaIn;

        // PDO según sufijo v/sv-e o f/sf, o comun (nombre base). La «f» solo implica BD sf si la entrada es sf (UBICACION/PRIVATE); si no, se usa comun (p. ej. Docker sin rol …f).
        $sfsv = 0;
        $oDB_Select = $this->pdoForEsquema($esquema, $sfsv, $ubicacion);
        if ($oDB_Select === null) {
            return ['ok' => false, 'error' => 1];
        }

        $aWhere = ['usuario' => $username];
        $query = 'SELECT * FROM aux_usuarios WHERE usuario = :usuario';
        if (($oDBSt = $oDB_Select->prepare($query)) === false) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, 'login.prepare', __LINE__, __FILE__);
            return ['ok' => false, 'error' => 1];
        }
        if ($oDBSt->execute($aWhere) === false) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDB_Select, 'login.execute', __LINE__, __FILE__);
            return ['ok' => false, 'error' => 1];
        }

        $password_db = null;
        $oDBSt->bindColumn('password', $password_db, \PDO::PARAM_STR);
        $row = $oDBSt->fetch(\PDO::FETCH_ASSOC);
        if ($row === false) {
            return ['ok' => false, 'error' => 1];
        }
        $row['password'] = $password_db;
        $MiUsuario = (new Usuario())->setAllAttributes($row);

        $oCrypt = new PasswordHasher();
        if ($oCrypt->encode($password, $password_db) !== $password_db) {
            return ['ok' => false, 'error' => 1];
        }

        $expire = '';
        if ($MiUsuario->isCambio_password() || $password === '1ªVegada') {
            $expire = 1;
        }

        // 2FA
        $has_2fa = $row['has_2fa'] ?? false;
        $user_secret = $row['secret_2fa'] ?? '';

        if ($has_2fa && empty($user_secret)) {
            return [
                'ok' => false,
                'redirect_ayuda_2fa' => [
                    'username' => $username,
                    'ubicacion' => $ubicacion,
                    'esquema' => $esquema,
                ],
            ];
        }

        if ($has_2fa && !empty($user_secret)) {
            if (empty($verification_code)) {
                return ['ok' => false, 'error' => 3];
            }
            if (!Verify2fa::verify_2fa_code($verification_code, $user_secret)) {
                return ['ok' => false, 'error' => 4];
            }
        }

        // Role / DMZ
        $id_usuario = (int)$row['id_usuario'];
        $id_role = (int)$row['id_role'];

        $oConfigDB = new ConfigDB('comun_select');
        $config = $oConfigDB->getEsquema('public');
        $oConexion = new DBConnection($config);
        $oDBCP_Select = $oConexion->getPDO();
        $queryr = 'SELECT * FROM aux_roles WHERE id_role = ' . $id_role;
        if (($oDBPSt = $oDBCP_Select->query($queryr)) === false) {
            $_SESSION['oGestorErrores']->addErrorAppLastError($oDBCP_Select, 'login.role', __LINE__, __FILE__);
            return ['ok' => false, 'error' => 1];
        }
        $row2 = $oDBPSt->fetch(\PDO::FETCH_ASSOC);
        $role_pau = $row2['pau'] ?? '';

        if (ConfigGlobal::is_dmz()) {
            $role_dmz = $row2['dmz'] ?? null;
            if (empty($role_dmz)) {
                return ['ok' => false, 'error' => 2];
            }
        }

        $mail = (string)($row['email'] ?? '');

        $a_mods = $this->getModsPosibles();
        $a_apps = $this->getAppsPosibles();
        $a_mods_installed = $this->getModsInstalados($oDB_Select);

        $app = [];
        foreach ($a_mods_installed as $id_mod => $param) {
            $app[] = $this->getAppsMods($id_mod);
            $app[] = $this->getApps($id_mod);
        }
        $app_installed = $app !== [] ? array_merge(...array_values($app)) : [];
        $app_installed = array_unique($app_installed);

        $perms_activ = '';
        $mi_oficina = '';
        $mi_oficina_menu = '';

        // Preferencias: idioma y ordenApellidos.
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
            'perms_activ' => $perms_activ,
            'mi_oficina' => $mi_oficina,
            'mi_oficina_menu' => $mi_oficina_menu,
            'expire' => $expire,
            'mail' => $mail,
            'idioma' => $idioma,
            'ordenApellidos' => $ordenApellidos,
            'mi_id_schema' => $id_schema,
        ];

        $session_config = [
            'id_role' => $id_role,
            'role_pau' => $role_pau,
            'username' => $username,
            'password' => $password,
            'perms_activ' => $perms_activ,
            'mi_oficina' => $mi_oficina,
            'mi_oficina_menu' => $mi_oficina_menu,
            'expire' => $expire,
            'mail' => $mail,
            'idioma' => $idioma,
            'app_installed' => $app_installed,
            'mod_installed' => $a_mods_installed,
            'a_apps' => $a_apps,
            'a_mods' => $a_mods,
        ];

        return [
            'ok' => true,
            'session_auth' => $session_auth,
            'session_config' => $session_config,
            'esquema' => $esquema,
            'idioma' => $idioma,
            'sfsv' => $sfsv,
        ];
    }

    private function pdoForEsquema(string &$esquema, ?int &$sfsv, string $ubicacion): ?\PDO
    {
        $sfsv = 0;
        $private = (string) getenv('PRIVATE');
        $useSfDb = ($ubicacion === 'sf' || $private === 'sf');

        if (substr($esquema, -1) === 'v') {
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

        if (substr($esquema, -1) === 'f') {
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
    private function getAppsPosibles(): array
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

    /** @return array<int, array{nom:string, mods_req:mixed, apps_req:mixed}> */
    private function getModsPosibles(): array
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

    /** @return array<int,string> */
    private function getModsInstalados(\PDO $oDB_Select): array
    {
        $a_mods = $this->getModsPosibles();
        $a_mods_installed = [];
        foreach ($oDB_Select->query("SELECT * FROM m0_mods_installed_dl WHERE active = 't'") as $aDades) {
            $id_mod = $aDades['id_mod'];
            $a_mods_installed[$id_mod] = $a_mods[$id_mod]['nom'];
        }

        return $a_mods_installed;
    }

    /** @return list<string> */
    private function getAppsMods(int|string $id_mod): array
    {
        $apps = [];
        $a_mods = $this->getModsPosibles();
        $ajson = $a_mods[$id_mod]['mods_req'];
        if (preg_match('/^{(.*)}$/', (string)$ajson, $matches)) {
            if (!empty($matches[1])) {
                $apps_installed = [];
                $mod_in = str_getcsv($matches[1]);
                foreach ($mod_in as $mod) {
                    $apps_installed[] = $this->getApps($mod);
                }
                $apps = array_merge(...array_values($apps_installed));
            }
        }

        return $apps;
    }

    /** @return list<string> */
    private function getApps(int|string $id_mod): array
    {
        $apps = [];
        $a_mods = $this->getModsPosibles();
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
