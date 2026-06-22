<?php

namespace src\shared\config;

use src\configuracion\domain\value_objects\ConfigSnapshot;
use src\usuarios\domain\entity\Usuario;

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;

class ConfigGlobal extends ServerConf
{

    // la region (sin cr), las dl en formato de DBU
    // Actualmente se usa para sincronizar con la BDU.
    // las dl de la bdu (sin esquema en orbix) se añaden a la región que tiene esquema en Orbix.
    public const REGIONES_CON_DL = [
            'Pla' => ['u', 'par'],
        ];

    /**
     * @return array<string, mixed>
     */
    private static function sessionAuth(): array
    {
        $auth = $_SESSION['session_auth'] ?? null;

        return is_array($auth) ? $auth : [];
    }

    /**
     * @return array<string, mixed>
     */
    private static function sessionConfig(): array
    {
        $config = $_SESSION['config'] ?? null;

        return is_array($config) ? $config : [];
    }

    public static function getWebPort(): string
    {
        $private = $_SESSION['private'] ?? '';
        if (!is_string($private)) {
            $private = '';
        }
        if ($private !== '' && $private === 'sf') {
            return self::$web_port_sf;
        }

        return self::$web_port;
    }

    public static function getWebPath(): string
    {
        $path = self::$web_path;
        // `login.php` pone `$_SESSION['sfsv']` desde UBICACION (sv/sf). En CLI/phpunit puede faltar.
        $sfsv_ubicacion = $_SESSION['sfsv'] ?? '';
        if (!is_string($sfsv_ubicacion)) {
            $sfsv_ubicacion = '';
        }
        if ($sfsv_ubicacion === 'sf') {
            $path .= 'sf';
        }
        $esquema_web = getenv('ESQUEMA');
        if (!empty($esquema_web)) {
            $path .= '/' . $esquema_web;
        }
        return $path;
    }

    public static function getWeb(): string
    {
        return self::$web_server . self::getWebPort() . self::getWebPath();
    }

    public static function getWeb_scripts(): string
    {
        return self::getWeb() . '/scripts';
    }

    public static function getWeb_NodeScripts(): string
    {
        return self::getWeb() . '/node_modules';
    }

    public static function getWeb_public(): string
    {
        return self::getWeb() . '/public';
    }

    public static function getWeb_icons(): string
    {
        return self::getWeb() . '/images';
    }

    public static function getWeb_udm(): string
    {
        return self::getWeb() . '/scripts/udm4-php/udm-resources/';
    }

    public static function is_dmz(): bool
    {
        $dmz = self::$dmz; // heredada de ServerConf (FALSE), TRUE En la instalación exterior
        if ($dmz) {
            $private = $_SESSION['private'] ?? null;
            if (is_string($private) && $private !== '' && $private === 'sf') {
                $dmz = false;
            }
        }
        return $dmz;
    }

    public static function is_debug_mode(): bool
    {
        return self::$debug;
    }

    public static function setTest_mode(bool $test): void
    {
        self::$test = $test;
    }

    public static function is_test_mode(): bool
    {
        return self::$test;
    }

    /**
     * Entorno «pruebas» (prefijos pruebas- en ficheros .inc, suscripciones lógicas, etc.).
     * Usa WEBDIR del entorno si está definido; si no, la constante de ServerConf.
     */
    public static function esEntornoPruebas(): bool
    {
        $webdir = getenv('WEBDIR');
        if (is_string($webdir) && $webdir !== '') {
            return $webdir === 'pruebas';
        }

        $configured = (new \ReflectionClass(self::class))->getConstant('WEBDIR');

        return is_string($configured) && $configured === 'pruebas';
    }

    public static function getDIR_PWD(): string
    {
        if (self::is_test_mode()) {
            // Dentro del contenedor PHP: tests/config apunta a localhost:5444 (host);
            // Postgres está en el servicio `db` vía /var/www/conf/*.conn.inc.
            if (is_readable('/.dockerenv') && is_dir(self::DIR_PWD)) {
                return self::DIR_PWD;
            }

            $desdeDirProyecto = self::DIR . '/tests/config';
            if (is_dir($desdeDirProyecto)) {
                return $desdeDirProyecto;
            }

            return self::DIR_PWD_TEST;
        }

        return self::DIR_PWD;
    }

    /**
     * devuelve true/false si está o no instalado el módulo.
     */
    public static function is_mod_installed(int $id_mod): bool
    {
        $modInstalled = self::sessionConfig()['mod_installed'] ?? null;
        if (!is_array($modInstalled)) {
            return false;
        }

        return array_key_exists($id_mod, $modInstalled);
    }

    /**
     * devuelve true/false si está o no instalada la app.
     */
    public static function is_app_installed(string $nom_app): bool
    {
        $config = self::sessionConfig();
        $aApps = $config['a_apps'] ?? null;
        if (!is_array($aApps) || empty($aApps[$nom_app])) {
            return false;
        }
        $idAppRaw = $aApps[$nom_app];
        if (!is_numeric($idAppRaw)) {
            return false;
        }
        $id_app = (int) $idAppRaw;
        $appInstalled = $config['app_installed'] ?? null;
        if (!is_array($appInstalled)) {
            return false;
        }

        return in_array($id_app, $appInstalled, true);
    }

    public static function MiUsuario(): ?Usuario
    {
        $user = self::sessionAuth()['MiUsuario'] ?? null;

        return $user instanceof Usuario ? $user : null;
    }

    public static function mi_id_usuario(): int
    {
        return input_int(self::sessionAuth(), 'id_usuario');
    }

    /**
     * @return int 1: sv, 2 sf
     */
    public static function mi_sfsv(): int
    {
        return input_int(self::sessionAuth(), 'sfsv');
    }

    public static function mi_id_role(): int
    {
        return input_int(self::sessionAuth(), 'id_role');
    }

    public static function mi_role_pau(): string
    {
        return input_string(self::sessionAuth(), 'role_pau');
    }

    public static function mi_usuario(): string
    {
        return input_string(self::sessionAuth(), 'username');
    }

    public static function mi_pass(): string
    {
        return input_string(self::sessionAuth(), 'password');
    }

    public static function mi_id_schema(): int
    {
        return input_int(self::sessionAuth(), 'mi_id_schema');
    }

    public static function mi_region_dl(): string
    {
        $auth = self::sessionAuth();
        $esquema = $auth['esquema'] ?? null;
        if (empty($esquema)) {
            $envEsquema = getenv('ESQUEMA');

            return is_string($envEsquema) ? $envEsquema : '';
        }

        return input_string($auth, 'esquema');
    }

    public static function mi_region(): string
    {
        $esq = input_string(self::sessionAuth(), 'esquema');
        if ($esq === '') {
            return '';
        }
        // «Cong-crCongv» tiene un solo guión: explode('-', $s) ya da [Cong, crCongv].
        // El límite 2 equivale a eso y deja fijada la regla «partir solo en el primer guión»
        // por si el nombre completo llegara a tener más guiones.
        $a_reg = explode('-', $esq, 2);

        return $a_reg[0];
    }

    public static function mi_dele(): string
    {
        $esq = input_string(self::sessionAuth(), 'esquema');
        if ($esq === '') {
            return '';
        }
        $a_reg = explode('-', $esq, 2);
        $seg = $a_reg[1] ?? '';
        if ($seg === '') {
            return '';
        }
        $last = substr($seg, -1);
        // Solo quitar sufijo sv/sf en el tramo delegación; esquemas base sin v/f no se truncan.
        $dl = ($last === 'v' || $last === 'f') ? substr($seg, 0, -1) : $seg;
        if ($dl === 'cr') {
            $dl .= self::mi_region();
        }

        return $dl;
    }

    /**
     * Añado la f en caso de sf.
     * Quizá se debería hacer en la función de mi_dele(),
     * pero de momento vamos a ir cambiando poco a poco
     * (de momento he cambiado todo lo que  tiene que ver con dl_org de actividades)
     * Añado el parámetro: isfsv, para el caso de des, poder acceder a sf.
     *
     * @param string $isfsv
     * @return string
     */
    public static function mi_delef(string $isfsv = ''): string
    {
        $dl = self::mi_dele();
        if (!empty($isfsv)) {
            if ($isfsv == 2) {
                $dl .= 'f';
            }
        } else {
            if (self::mi_sfsv() == 2) {
                $dl .= 'f';
            }
        }
        return $dl;
    }

    /**
     * Para los esquemas tipo 'H-H' o 'H-Hf', se tiene permiso
     * para consultar a todas las dl.
     */
    public static function mi_ambito(): string
    {
        $oConfig = $_SESSION['oConfig'] ?? null;

        return $oConfig instanceof ConfigSnapshot ? $oConfig->getAmbito() : '';
    }

    public static function permisos(): void
    {
        //ja no val return $_SESSION['session_auth']['perms'];
    }

    public static function mi_oficina_menu(): string
    {
        return input_string(self::sessionAuth(), 'mi_oficina_menu');
    }

    public static function mi_oficina(): string
    {
        return input_string(self::sessionAuth(), 'mi_oficina');
    }

    public static function mi_mail(): string
    {
        return input_string(self::sessionAuth(), 'mail');
    }
    // ----------- Idioma -------------------
    //es_ES.UTF-8
    public static function mi_Idioma(): string
    {
        return input_string(self::sessionAuth(), 'idioma');
    }

    //es
    public static function mi_Idioma_short(): string
    {
        return substr(input_string(self::sessionAuth(), 'idioma'), 0, 2);
    }

    public static function is_locale_us(): bool
    {
        $auth = self::sessionAuth();
        $idioma = $auth['idioma'] ?? null;
        # Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
        if (!is_string($idioma) || $idioma === '') {
            $oConfig = $_SESSION['oConfig'] ?? null;
            $idioma = $oConfig instanceof ConfigSnapshot ? $oConfig->getIdioma_default() : '';
        }
        $a_idioma = explode('.', $idioma);
        $code_lng = $a_idioma[0];
        return $code_lng === 'en_US';
    }

    // ----------- ordenApellidos -------------------
    public static function mi_ordenApellidos(): string
    {
        return input_string(self::sessionAuth(), 'ordenApellidos');
    }
}
