<?php

namespace src\configuracion\domain\entity;

use core\ConfigGlobal;
use src\configuracion\application\repositories\ConfigSchemaRepository;

/**
 * Classe
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 7/5/2019
 */
class Config
{

    // conversion
    public static $replace = array(
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
    private ConfigSchemaRepository $repository;


    public function __construct()
    {
        $this->msg = _("Debe configurar el esquema en Menu: Sistema > Configuración > config esquema");
        // No se puede guardar el repository, porque guardamos la clase en la $_SESSION, y la
        // conexión PDO no se puede serializar.
        //$this->repository = new ConfigSchemaRepository();
    }


    public function getGestionCalendario()
    {
        $parametro = 'gesCalendario';
        $oConfigSchema = $this->repository->findById($parametro);
        return $oConfigSchema?->getValorVo()?->value();
    }

    /**
     * Devuelve TRUR O FALSE si es o no jefe del calendario.
     * Si no se le pasa ningun valor, compara con el usuario actual
     *
     * @param string $username
     * @return boolean
     */
    public function is_jefeCalendario(string $username = '')
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'jefe_calendario';
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();

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
        if (in_array($username, $a_jefes_calendario)) {
            return true;
        } else {
            return false;
        }
    }

    public function getCe_lugar()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'ce_lugar';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("lugar ce");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            $this->msg .= "<br>". _("se puede poner una lista separada por comas");
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getCe()
    {
        $this->repository = new ConfigSchemaRepository();
        $valor = $this->getCe_lugar();
        // pasar el valor de nombres separados por coma a array:
        $a_ce = explode(',', $valor);
        return $a_ce;
    }

    public function get_region_latin()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'region_latin';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("nombre región en latín");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getVstgr()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'vstgr';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("vstgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getLugarFirma()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'lugar_firma';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("lugar firma");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getDirStgr()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'dir_stgr';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("direccion stgr");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getAmbito()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'ambito';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("ámbito");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getNotaCorte()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'nota_corte';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("nota corte");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getNotaMax()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'nota_max';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("nota máxima");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function getCaducaCursada()
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'caduca_cursada';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();
        if (empty($valor)) {
            $nom_param = _("caduca cursada");
            $this->msg .= "<br><br>";
            $this->msg .= sprintf(_("falta el parámetro: %s"), $nom_param);
            exit ($this->msg);
        } else {
            return $valor;
        }
    }

    public function setCursoStgr(): void
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'curso_stgr';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();

        // valor es un json representa un array:
        // ini_dia, ini_mes, fin_dia, fin_mes
        $aCursoStgr = json_decode($valor, TRUE);
        $this->aCursoStgr = $aCursoStgr;
    }

    public function setCursoCrt(): void
    {
        $this->repository = new ConfigSchemaRepository();
        $parametro = 'curso_crt';
        
        $oConfigSchema = $this->repository->findById($parametro);
        $valor = $oConfigSchema?->getValorVo()?->value();

        // valor es un json representa un array:
        // ini_dia, ini_mes, fin_dia, fin_mes
        $aCursoCrt = json_decode($valor, TRUE);
        $this->aCursoCrt = $aCursoCrt;
    }

    public function getCursoStgr(): array
    {
        if (!isset($this->aCursoStgr)) {
            $this->setCursoStgr();
        }
        return $this->aCursoStgr;
    }

    public function getCursoCrt(): array
    {
        if (!isset($this->aCursoCrt)) {
            $this->setCursoCrt();
        }
        return $this->aCursoCrt;
    }

        public function getMesFinStgr()
    {
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


}
