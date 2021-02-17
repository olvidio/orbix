<?php
namespace config\model;
use config\model\entity\ConfigSchema;
use core\ConfigGlobal;

/**
 * Classe 
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 7/5/2019
 */
class Config {
   
    // conversion
    public static $replace  = array(
            'AE' => '&#0198;',
            'Ae' => '&#0198;',
            'ae' => '&#0230;',
            'aE' => '&#0230;',
            'OE' => '&#0338;',
            'Oe' => '&#0338;',
            'oe' => '&#0339;',
            'oE' => '&#0339;'
        );
        
    /**
     * 
     * @var array
     */
    private $aCursoStgr;
    
    /**
     * 
     * @var array
     */
    private $aCursoCrt;
    
    /**
     * 
     * @var string
     */
    private $msg;
    
    
    public function __construct() {
        $this->msg = _("Debe configurar el esquema en Menu: Sistema > Configuración > config esquema");
    }
    
    
    public function getGestionCalendario() {
        $parametro = 'gesCalendario';
        $oConfigSchema = new ConfigSchema($parametro);
        return $oConfigSchema->getValor();
    }
    
    /**
     * Devuelve TRUR O FALSE si es o no jefe del calendario.
     * Si no se le pasa ningun valor, compara con el usuario actual
     * 
     * @param string $username
     * @return boolean
     */
    public function is_jefeCalendario($username = '') {
        $parametro = 'jefe_calendario';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();

        if (empty($valor)) {
            $nom_param = _("jefe calendario");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        }
        
        // pasar el valor de nombres separados por coma a array:
        $a_jefes_calendario = explode(',', $valor);
        if (empty($username)) {
            $username = ConfigGlobal::mi_usuario();
        }
        if (in_array($username,$a_jefes_calendario)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getIdioma_default() {
        $parametro = 'idioma_default';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("idioma por defecto");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }
    
    public function getNota_corte() {
        $parametro = 'nota_corte';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("nota de corte");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }
    
    public function getNota_max() {
        $parametro = 'nota_max';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("nota máxima");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }
    
    public function getCaduca_cursada() {
        $parametro = 'caduca_cursada';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("años que se conserva si se ha cursado una asignatura");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }
    
    public function getNomRegionLatin() {
        $parametro = 'region_latin';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("nombre de la región");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            $nombre_region_latin = strtr($valor, self::$replace);
            return $nombre_region_latin;
        }
    }
    
    public function getNomVstgr() {
        $parametro = 'vstgr';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("nombre del secretario del stgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            $vstgr = strtr($valor, self::$replace);
            return $vstgr;
        }
    }
    
    public function getLugarFirma() {
        $parametro = 'lugar_firma';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("lugar firma");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            $lugar_firma = strtr($valor, self::$replace);
            return $lugar_firma;
        }
    }
    
    public function getDirStgr() {
        $parametro = 'dir_stgr';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
        if (empty($valor)) {
            $nom_param = _("dirección sede");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            $dir_stgr = strtr($valor, self::$replace);
            return $dir_stgr;
        }
    }
    
    public function getAmbito() {
        $parametro = 'ambito';
        $oConfigSchema = new ConfigSchema($parametro);
        return $oConfigSchema->getValor();
    }
    
    public function mes_actual() {
        return date("m");
    }
    
    public function any_final_curs($que='est') {
        switch ($que) {
            case 'est':
                if ($this->mes_actual() > 9) {
                    return date("Y")+1;
                } else {
                    return date("Y");
                }
                break;
            case 'crt':
                if ($this->mes_actual() > 8) {
                    return date("Y")+1;
                } else {
                    return date("Y");
                }
                break;
        }
    }
    
    private function getCursoStgr() {
        if (!isset($this->aCursoStgr)) {
            $parametro = 'curso_stgr';
            $oConfigSchema = new ConfigSchema($parametro);
            $valor = $oConfigSchema->getValor();
            $this->aCursoStgr = json_decode($valor, TRUE);
        }
        return $this->aCursoStgr;
    }
    
    public function getDiaIniStgr() {
        $aCursoStgr = $this->getCursoStgr();
        if (empty($aCursoStgr['ini_dia'])) {
            $nom_param = _("día inicio stgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoStgr['ini_dia'];
        }
    }
    public function getMesIniStgr() {
        $aCursoStgr = $this->getCursoStgr();
        if (empty($aCursoStgr['ini_mes'])) {
            $nom_param = _("mes inicio stgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoStgr['ini_mes'];
        }
    }
    public function getDiaFinStgr() {
        $aCursoStgr = $this->getCursoStgr();
        if (empty($aCursoStgr['fin_dia'])) {
            $nom_param = _("día fin stgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoStgr['fin_dia'];
        }
    }
    public function getMesFinStgr() {
        $aCursoStgr = $this->getCursoStgr();
        if (empty($aCursoStgr['fin_mes'])) {
            $nom_param = _("mes de fin stgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoStgr['fin_mes'];
        }
    }
    
    private function getCursoCrt() {
        if (!isset($this->aCursoCrt)) {
            $parametro = 'curso_crt';
            $oConfigSchema = new ConfigSchema($parametro);
            $valor = $oConfigSchema->getValor();
            $this->aCursoCrt = json_decode($valor, TRUE);
        }
        return $this->aCursoCrt;
    }
    
    public function getDiaIniCrt() {
        $aCursoCrt = $this->getCursoCrt();
        if (empty($aCursoCrt['ini_dia'])) {
            $nom_param = _("día incio curso crt");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoCrt['ini_dia'];
        }
    }
    public function getMesIniCrt() {
        $aCursoCrt = $this->getCursoCrt();
        if (empty($aCursoCrt['ini_mes'])) {
            $nom_param = _("mes inicio curso crt");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoCrt['ini_mes'];
        }
    }
    public function getDiaFinCrt() {
        $aCursoCrt = $this->getCursoCrt();
        if (empty($aCursoCrt['fin_dia'])) {
            $nom_param = _("día fin curso crt");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoCrt['fin_dia'];
        }
    }
    public function getMesFinCrt() {
        $aCursoCrt = $this->getCursoCrt();
        if (empty($aCursoCrt['fin_mes'])) {
            $nom_param = _("mes fin curso crt");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $aCursoCrt['fin_mes'];
        }
    }
    
    
    
}