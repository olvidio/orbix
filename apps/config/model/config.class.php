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
    
    public function getGestionCalendario() {
        $parametro = 'gesCalendario';
        $oConfigSchema = new ConfigSchema($parametro);
        return $oConfigSchema->getValor();
    }
    
    public function is_jefeCalendario($username = '') {
        $parametro = 'jefe_calendario';
        $oConfigSchema = new ConfigSchema($parametro);
        $valor = $oConfigSchema->getValor();
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
        return $oConfigSchema->getValor();
    }
    
    public function getNota_max() {
        $parametro = 'nota_max';
        $oConfigSchema = new ConfigSchema($parametro);
        return $oConfigSchema->getValor();
    }
    
    public function getNomRegionLatin() {
        $parametro = 'region_latin';
        $oConfigSchema = new ConfigSchema($parametro);
        $nombre_region_latin = strtr($oConfigSchema->getValor(), self::$replace);
        return $nombre_region_latin;
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
        return $aCursoStgr['ini_dia'];
    }
    public function getMesIniStgr() {
        $aCursoStgr = $this->getCursoStgr();
        return $aCursoStgr['ini_mes'];
    }
    public function getDiaFinStgr() {
        $aCursoStgr = $this->getCursoStgr();
        return $aCursoStgr['fin_dia'];
    }
    public function getMesFinStgr() {
        $aCursoStgr = $this->getCursoStgr();
        return $aCursoStgr['fin_mes'];
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
        return $aCursoCrt['ini_dia'];
    }
    public function getMesIniCrt() {
        $aCursoCrt = $this->getCursoCrt();
        return $aCursoCrt['ini_mes'];
    }
    public function getDiaFinCrt() {
        $aCursoCrt = $this->getCursoCrt();
        return $aCursoCrt['fin_dia'];
    }
    public function getMesFinCrt() {
        $aCursoCrt = $this->getCursoCrt();
        return $aCursoCrt['fin_mes'];
    }
    
    
    
}