<?php

namespace core;

class ConfigGlobal extends ServerConf
{

    public static $auth_method='database';
    // la region (sin cr), las dl en formato de DBU
    // Actualmente se usa para sincronizar con la BDU.
    // las dl de la bdu (sin esquema en orbix) se añaden a la región que tiene esquema en Orbix.
    public const REGIONES_CON_DL = [
            'Pla' => ['u', 'par'],
        ];

    public static function getWebPort()
    {
        $private = getenv('PRIVATE');
        if (!empty($private) && $private === 'sf') {
            return self::$web_port_sf;
        } else {
            return self::$web_port;
        }
    }

    public static function getWebPath()
    {
        $path = self::$web_path;
        if ($_SESSION['sfsv'] === 'sf') {
            $path .= 'sf';
        }
        $esquema_web = getenv('ESQUEMA');
        if (!empty($esquema_web)) {
            $path .= '/' . $esquema_web;
        }
        return $path;
    }

    public static function getWeb()
    {
        return self::$web_server . self::getWebPort() . self::getWebPath();
    }

    public static function getWeb_scripts()
    {
        return self::getWeb() . '/scripts';
    }

    public static function getWeb_NodeScripts()
    {
        return self::getWeb() . '/node_modules';
    }

    public static function getWeb_public()
    {
        return self::getWeb() . '/public';
    }

    public static function getWeb_icons()
    {
        return self::getWeb() . '/images';
    }

    public static function getWeb_udm()
    {
        return self::getWeb() . '/scripts/udm4-php/udm-resources/';
    }

    public static function is_dmz()
    {
        $dmz = self::$dmz; // heredada de ServerConf (FALSE), TRUE En la instalación exterior
        if ($dmz) {
            $private = getenv('PRIVATE');
            if (!empty($private) && $private === 'sf') {
                $dmz = FALSE;
            }
        }
        return $dmz;
    }

    public static function is_debug_mode()
    {
        return self::$debug;
    }

    public static function setTest_mode(bool $test)
    {
        self::$test = $test;
    }

    public static function is_test_mode()
    {
        return self::$test;
    }

    public static function getDIR_PWD()
    {
        if (self::is_test_mode()) {
            return  self::DIR_PWD_TEST;
        }

        return  self::DIR_PWD;
    }

    /**
     * devuelve true/false si está o no instalado el módulo.
     *
     * @param integer $id_mod
     * @return boolean
     */
    public static function is_mod_installed($id_mod)
    {
        return array_key_exists($id_mod, $_SESSION['config']['mod_installed']);
    }

    /**
     * devuelve true/false si está o no instalada la app.
     *
     * @param integer $id_mod
     * @return boolean
     */
    public static function is_app_installed($nom_app)
    {
        if (!empty($_SESSION['config']['a_apps'][$nom_app])) {
            $id_app = $_SESSION['config']['a_apps'][$nom_app];
            return in_array($id_app, $_SESSION['config']['app_installed']);
        } else {
            return false;
        }
    }

    public static function mi_id_usuario()
    {
        return $_SESSION['session_auth']['id_usuario'];
    }

    /**
     *
     * @return integer  1: sv, 2 sf
     */
    public static function mi_sfsv(): int
    {
        return $_SESSION['session_auth']['sfsv'];
    }

    public static function mi_id_role()
    {
        return $_SESSION['session_auth']['id_role'];
    }

    public static function mi_role_pau()
    {
        return $_SESSION['session_auth']['role_pau'];
    }

    public static function mi_usuario()
    {
        return $_SESSION['session_auth']['username'];
    }

    public static function mi_pass()
    {
        return $_SESSION['session_auth']['password'];
    }

    public static function mi_id_schema()
    {
        return $_SESSION['session_auth']['mi_id_schema'];
    }

    public static function mi_region_dl()
    {
        return $_SESSION['session_auth']['esquema'];
    }

    public static function mi_region()
    {
        $a_reg = explode('-', $_SESSION['session_auth']['esquema']);
        return $a_reg[0];
    }

    public static function mi_dele()
    {
        $a_reg = explode('-', $_SESSION['session_auth']['esquema']);
        $dl = substr($a_reg[1], 0, -1); // quito la v o la f.
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
     * @param $isfsv
     * @return string
     */
    public static function mi_delef($isfsv = '')
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
     *
     * @return string 'dl'|'r'|'rstgr'
     */
    public static function mi_ambito()
    {
        return $_SESSION['oConfig']->getAmbito();
    }

    public static function permisos()
    {
        //ja no val return $_SESSION['session_auth']['perms'];
    }

    public static function mi_oficina_menu()
    {
        return $_SESSION['session_auth']['mi_oficina_menu'];
    }

    public static function mi_oficina()
    {
        return $_SESSION['session_auth']['mi_oficina'];
    }

    public static function mi_mail()
    {
        return $_SESSION['session_auth']['mail'];
    }
    // ----------- Idioma -------------------
    //es_ES.UTF-8
    public static function mi_Idioma()
    {
        return $_SESSION['session_auth']['idioma'];
    }

    //es
    public static function mi_Idioma_short()
    {
        return substr($_SESSION['session_auth']['idioma'], 0, 2);
    }

    public static function is_locale_us()
    {
        $idioma = $_SESSION['session_auth']['idioma'];
        # Si no hemos encontrado ningún idioma que nos convenga, mostramos la web en el idioma por defecto
        if (!isset($idioma)) {
            $idioma = $_SESSION['oConfig']->getIdioma_default();
        }
        $a_idioma = explode('.', $idioma);
        $code_lng = $a_idioma[0];
        return $code_lng === 'en_US';
    }

    // ----------- ordenApellidos -------------------
    public static function mi_ordenApellidos()
    {
        return $_SESSION['session_auth']['ordenApellidos'] ?? '';
    }
}