<?php
namespace core;

Class ConfigGlobal extends ServerConf {
    
    

	public static function getWebPort() {
        $private = getenv('PRIVATE');
	    if (!empty($private) && $private == 'sf') {
	        return self::$web_port_sf;
	    } else {
	        return self::$web_port;
	    }
	}
	
	public static function getWebPath() {
	    $path = self::$web_path;
        if ($_SESSION['sfsv'] == 'sf') {
            $path .= 'sf';
        }
        $esquema_web = getenv('ESQUEMA');
        if (!empty($esquema_web)) {
            $path .= '/'.$esquema_web;
        }
        return $path;
	}
	public static function getWeb() {
		return self::$web_server.self::getWebPort().self::getWebPath();
	}
	public static function getWeb_scripts() {
	    return self::getWeb().'/scripts';
	}
	public static function getWeb_NodeScripts() {
	    return self::getWeb().'/node_modules';
	}
	public static function getWeb_public() {
	    return self::getWeb().'/public';
	}
	public static function getWeb_icons() {
	    return self::getWeb().'/images';
	}
	public static function getWeb_udm() {
	    return self::getWeb().'/scripts/udm4-php/udm-resources/';
	}
	
	public static function is_dmz() {
	    $dmz = self::$dmz; // heredada de ServerConf (FALSE), TRUE En la instalacion exterior
	    if ($dmz) {
            $private = getenv('PRIVATE');
            if (!empty($private) && $private == 'sf') {
                $dmz = FALSE;
            }
	    }
        return $dmz;
	}
	
	public static function is_debug_mode() {
        return self::$debug;
	}

	public static function is_mod_installed($id_mod) {
		if (array_key_exists($id_mod,$_SESSION['config']['mod_installed'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function is_app_installed($nom_app) {
		if (!empty($_SESSION['config']['a_apps'][$nom_app])) {
			$id_app = $_SESSION['config']['a_apps'][$nom_app];
			if (in_array($id_app,$_SESSION['config']['app_installed'])) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function mi_id_usuario() {
		return $_SESSION['session_auth']['id_usuario'];
	}
	/**
	 * 
	 * @return integer  1: sv, 2 sf
	 */
	public static function mi_sfsv() {
		return $_SESSION['session_auth']['sfsv'];
	}
	public static function mi_id_role() {
		return $_SESSION['session_auth']['id_role'];
	}
	public static function mi_role_pau() {
		return $_SESSION['session_auth']['role_pau'];
	}
	public static function mi_usuario() {
		return $_SESSION['session_auth']['username'];
	}
	public static function mi_pass() {
		return $_SESSION['session_auth']['password'];
	}
	public static function mi_id_schema() {
		return $_SESSION['session_auth']['mi_id_schema'];
	}
	public static function mi_region_dl() {
		return $_SESSION['session_auth']['esquema'];
	}
	public static function mi_region() {
		$a_reg = explode('-',$_SESSION['session_auth']['esquema']);
		$reg = $a_reg[0]; 
		return $reg;
	}
	public static function mi_dele() {
		$a_reg = explode('-',$_SESSION['session_auth']['esquema']);
		$dl = substr($a_reg[1],0,-1); // quito la v o la f.
        if ($dl == 'cr') {
		    $dl .= self::mi_region();
		}
		return $dl;
	}
	/**
	 * Añado la f en caso de sf.
	 * Quizá se debería hacer en la función de mi_dele(),
	 * pero de momento vamos a ir cambiando poco a poco
	 * (de momento he cambiado todo lo que  tiene que ver con dl_org de actividades)
	 * Añado el parametro: isfsv, para el caso de des, poder acceder a sf.
	 * 
	 * @param $isfsv
	 * @return string
	 */
	public static function mi_delef($isfsv='') {
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
	 * @return boolean
	 */
	public static function soy_region() {
	    $soy_region = FALSE;
        if ( self::mi_region() === self::mi_delef() ) {
            $soy_region = TRUE;
        }
        return $soy_region;
	}
	
	public static function permisos() {
		//ja no val return $_SESSION['session_auth']['perms'];
	}
	public static function mi_oficina_menu() {
		return $_SESSION['session_auth']['mi_oficina_menu'];
	}
	public static function mi_oficina() {
		return $_SESSION['session_auth']['mi_oficina'];
	}
	public static function mi_mail() {
		return $_SESSION['session_auth']['mail'];
	}
	// ----------- Idioma -------------------
	//es_ES.UTF-8
	public static function mi_Idioma() {
		return $_SESSION['session_auth']['idioma'];
	}
	//es
	public static function mi_Idioma_short() {
		return substr($_SESSION['session_auth']['idioma'],0,2);
	}
	// ----------- ordenApellidos -------------------
	public static function mi_ordenApellidos() {
		return $_SESSION['session_auth']['ordenApellidos'];
	}
}