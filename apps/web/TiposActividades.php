<?php

namespace web;

use actividades\model\entity\GestorTipoDeActividad;

/**
 * Clase que implementa la entidad tipos de actividades
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/10/2010
 */
/*
Inicialmete e pensó un tipo de actividad de 6 dígitos con la siguiente estructura:

(1 dígito para sv/sf, 1 dígito para el tipo de asistentes, 1 dígito para actividad, 3 dígitos para nombre)

  1  1  4  0  2  6
  |  |  |  |  |  |
  |  |  |  ----------> nom_tipo
  |  |  |____________  actividad
  |  |_______________  asistentes
  |__________________  sv/sf

Pero en la práctica, habia que distinguir entre actividades (ca-cv) de estudios y no de estudios,
 , actividades(cve-sacd) propias para los sacd y actividades (cv-sr-bach/univ) de sr para bachilleres
 o universitarios. Así que se toman 2 dígitos para actividad y 2 para nom_tipo.
    
  1  1  4  0  2  6
  |  |  |  |  |  |
  |  |  |  |  -------> nom_tipo
  |  |  |  |
  |  |  -------------> actividad
  |  |_______________  asistentes
  |__________________  sv/sf

De todas formas, en la mayoría de los casos se quiere hacer una selección por la actividad teniendo
 en cuenta sólo el primer dígito (sin tener en cuenta si es de estudios, o para sacd etc). En este caso 
 el nom_tipo toma 3 dígitos.
Si es importante a la hora de crear tipos, que se creen dentro de un tipo de actividad de 2 dígitos
 y por tanto creando un tipo de 2 dígitos. 

*/

class TiposActividades
{
    /* ATRIBUTOS ----------------------------------------------------------------- */

    /**
     * aDades de TiposActividades
     *
     * @var array
     */
    private $aDades;

    /**
     * bLoaded
     *
     * @var boolean
     */
    private $bLoaded = FALSE;

    /**
     * Id_tipo_activ de TiposActividades
     *
     * @var integer
     */
    private $iid_tipo_activ;
    /**
     * Regexp_id_tipo_activ de TiposActividades
     *
     * @var string
     */
    private $sregexp_id_tipo_activ;

    /**
     * sfsv de TiposActividades
     *
     * @var string
     */
    private $ssfsv;
    /**
     * asistentes de TiposActividades
     *
     * @var string
     */
    private $sasistentes;
    /**
     * actividad de TiposActividades
     *
     * @var string
     */
    private $sactividad;
    /**
     * nom_tipo de TiposActividades
     *
     * @var string
     */
    private $snom_tipo;
    /**
     * aSfsv de TiposActividades
     *
     * @var array
     */
    private $aSfsv = array(
        "sv" => 1,
        "sf" => 2,
        "reservada" => 3,
        "all" => '.'
    );

    /**
     * aAsistentes de TiposActividades
     *
     * @var array
     */
    private $aAsistentes = array(
        "n" => 1,
        "nax" => 2,
        "agd" => 3,
        "s" => 4,
        "sg" => 5,
        "sss+" => 6,
        "sr" => 7,
        "sr-nax" => 8,
        "sr-agd" => 9,
        "all" => '.'
    );

    /**
     * aActividad1Digito de TiposActividades
     *
     * @var array
     */
    private $aActividad1Digito = array(
        "crt" => '1',
        "ca" => '2',
        "cv" => '3',
        "cve" => '4',
        "cv-crt" => '5',
        "all" => '.'
    );
    /**
     * aActividad2Digitos de TiposActividades
     *
     * @var array
     */
    private $aActividad2Digitos = array(
        "crt" => 10,
        "crt-recientes" => 11,
        "crt-bach" => 15,
        "crt-univ" => 16,
        "ca" => 20,
        "ca-recientes" => 21,
        "ca-est" => 22,
        "semestre-inv" => 23,
        "ca-repaso" => 24,
        "ca-sacd" => 25,
        "cv" => 30,
        "cv-recientes" => 31,
        "cv-est" => 32,
        "cv-repaso" => 34,
        "cv-bach" => 35,
        "cv-univ" => 36,
        "cve" => 40,
        "cve-sacd" => 41,
        "cv-crt" => 50,
        "all" => '..'
    );

    //transpongo los vectores para buscar por números y no por el texto
    private $afSfsv = array();
    private $afAsistentes = array();
    private $afActividad1Digito = array();
    private $afActividad2Digitos = array();

    private $extendida = FALSE;

    /* CONSTRUCTOR -------------------------------------------------------------- */

    /**
     * Constructor de la classe.
     *
     * @param integer|string sid_tipo_proceso
     */
    function __construct($id = '', $extendida = FALSE)
    {
        $this->setExtendida($extendida);
        if (isset($id) && $id !== '') {
            if (is_numeric($id)) $this->iid_tipo_activ = $id;
            $this->separarId($id);
        }
    }

    /* MÉTODOS PRIVADOS ----------------------------------------------------------*/

    private function getFlipSfsv()
    {
        if (empty($this->afSfsv)) $this->afSfsv = array_flip($this->aSfsv);
        return $this->afSfsv;
    }

    private function getFlipAsistentes()
    {
        if (empty($this->afAsistentes)) $this->afAsistentes = array_flip($this->aAsistentes);
        return $this->afAsistentes;
    }

    private function getFlipActividad1Digito()
    {
        if (empty($this->afActividad1Digito)) $this->afActividad1Digito = array_flip($this->aActividad1Digito);
        return $this->afActividad1Digito;
    }

    private function getFlipActividad2Digitos()
    {
        if (empty($this->afActividad2Digitos)) $this->afActividad2Digitos = array_flip($this->aActividad2Digitos);
        return $this->afActividad2Digitos;
    }

    private function separarId($sregexp_id_tipo_activ)
    {
        if (!empty($sregexp_id_tipo_activ)) {
            $inc = 0;
            if (($ini = strpos($sregexp_id_tipo_activ, '[')) !== false) {
                $fin = strpos($sregexp_id_tipo_activ, ']');
                $inc = $fin - $ini;
            }
            $long = empty($inc) ? 6 : 6 + $inc;
            for ($i = strlen($sregexp_id_tipo_activ); $i < $long; $i++) {
                $sregexp_id_tipo_activ .= '.';
            }
            $matches = [];
            if ($this->extendida) {
                preg_match('/(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\[\d+\]|\d{2}|\d\.|\.\.)(\d{2}|\.*)/', $sregexp_id_tipo_activ, $matches);
            } else {
                preg_match('/(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\d{3}|\.*)/', $sregexp_id_tipo_activ, $matches);
            }
            if (!empty($matches)) {
                $this->sregexp_id_tipo_activ = $matches[0];
                $this->ssfsv = $matches[1];
                $this->sasistentes = $matches[2];
                $this->sactividad = $matches[3];
                $this->snom_tipo = $matches[4];
            }
        } else {
            return false;
        }
    }

    /* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

    public function setPosiblesAll($bAll)
    {
        if ($bAll === FALSE) {
            unset ($this->aSfsv['all']);
        }
    }

    /**
     * Separa un id_tipo_activ con posibles asistentes en
     * un array de id_tipo_activ separados.
     *
     * De momento parece que sólo es necesario para los asistentes.
     *
     * return array
     */
    public function getArrayAsistentesIndividual()
    {
        $a_tipos = [];
        $aAsistentes = $this->getAsistentesPosibles();
        foreach ($aAsistentes as $iasistentes => $sasistentes) {
            $txt_id = $this->getSfsvText() . ' ' . $sasistentes;
            $a_tipos[$txt_id] = $this->getSfsvId() . $iasistentes;
        }
        return $a_tipos;
    }


    /**
     * Recupera el atributo id_tipo_activ en format de regexp
     *
     * @return string
     */
    public function getId_tipo_activ()
    {
        $txt = $this->ssfsv;
        $txt .= $this->sasistentes;
        $txt .= $this->sactividad;
        $txt .= $this->snom_tipo;
        return $txt;
    }

    /**
     * Recupera el nombre de actividad para pasarela
     *
     * @return string
     */
    public function getNomPasarela()
    {
        $txt_svsf = $this->getSfsvText();
        // asistentes
        $txt_asistentes = ''; // valor por defecto
        if ($this->getAsistentesText() === 'n') {
            if ($txt_svsf === 'sv') {
                $txt_asistentes = _("numerarios");
            } else {
                $txt_asistentes = _("numerarias");
            }
        }
        if ($this->getAsistentesText() === 'nax') {
            $txt_asistentes = _("numerarias auxiliares");
        }
        if ($this->getAsistentesText() === 'agd') {
            if ($txt_svsf === 'sv') {
                $txt_asistentes = _("agregados");
            } else {
                $txt_asistentes = _("agregadas");
            }
        }
        if ($this->getAsistentesText() === 'sg') {
            if ($txt_svsf === 'sv') {
                $txt_asistentes = _("coperadores");
            } else {
                $txt_asistentes = _("coperadoras");
            }
        }
        // actividad
        $txt_actividad = 'Actividad'; // valor por defecto
        if ($this->getActividadText() === 'crt') {
            $txt_actividad = _("curso de retiro");
        }
        if ($this->getActividadText() === 'ca') {
            $txt_actividad = _("curso anual");
        }
        if ($this->getActividadText() === 'cv' || $this->getActividadText() === 'cve') {
            $txt_actividad = _("convivencia");
        }
        return $txt_actividad . ' ' . $txt_asistentes;
    }

    /**
     * Recupera el atributo nom en format de text
     * sin el (sin especificar)
     *
     * @return string
     */
    public function getNomGral()
    {
        $txt = $this->getSfsvText();
        if ($this->getAsistentesText() <> 'all') $txt .= ' ' . $this->getAsistentesText();
        if ($this->getActividadText() <> 'all') $txt .= ' ' . $this->getActividadText();
        if ($this->getNom_tipoId() <> 0 && $this->getNom_tipoText() <> 'all') $txt .= ' ' . $this->getNom_tipoText();
        return $txt;
    }

    /**
     * Recupera el atributo nom en format de text
     *
     * @return string
     */
    public function getNom()
    {
        $txt = $this->getSfsvText();
        if ($this->getAsistentesText() <> 'all') $txt .= ' ' . $this->getAsistentesText();
        if ($this->getActividadText() <> 'all') $txt .= ' ' . $this->getActividadText();
        if ($this->getNom_tipoText() <> 'all') $txt .= ' ' . $this->getNom_tipoText();
        return $txt;
    }

    /**
     * Recupera el atributo sfsv en format de text
     *
     * @return string
     */
    public function getSfsvText()
    {
        $aText = $this->getFlipSfsv();
        if (is_numeric($this->ssfsv)) {
            return $aText[$this->ssfsv];
        } else {
            return 'all';
        }
    }

    /**
     * Estableix l'atribut sfsv en format de text
     *
     * @return string
     */
    public function setSfsvText($sSfsv)
    {
        if (is_string($sSfsv)) {
            if (empty($sSfsv)) {
                $sSfsv = 'all';
            }
            $this->ssfsv = $this->aSfsv[$sSfsv];
        } else {
            return false;
        }
    }

    /**
     * Recupera el atributo sfsv en format de integer
     *
     */
    public function getSfsvId()
    {
        return $this->ssfsv;
    }

    /**
     * Estableix l'atribut sfsv en format de integer
     *
     * @return integer
     */
    public function setSfsvId($isfsv)
    {
        $this->ssfsv = $isfsv;
    }

    /**
     * Recupera el atributo sfsv en format de regexp
     *
     * @return integer
     */
    public function getSfsvRegexp()
    {
        return '^' . $this->ssfsv;

    }

    /**
     * Recupera el atributo sfsv posibles en format de array
     *
     * @return array
     */
    public function getSfsvPosibles()
    {
        $aText = $this->getFlipSfsv();
        $GesTipoDeActividades = new GestorTipoDeActividad();
        return $GesTipoDeActividades->getSfsvPosibles($aText);
    }

    /**
     * Recupera el atributo asistentes en format de text
     *
     * @return string
     */
    public function getAsistentesText()
    {
        $aText = $this->getFlipAsistentes();
        if (is_numeric($this->sasistentes)) {
            return $aText[$this->sasistentes];
        } else {
            return 'all';
        }
    }

    /**
     * Estableix l'atribut asistentes en format de text
     *
     * @return false si falla
     */
    public function setAsistentesText($sAsistentes)
    {
        if (is_string($sAsistentes)) {
            if (empty($sAsistentes)) {
                $sAsistentes = 'all';
            }
            // puede ser un string separado por comas (s,sg)
            $a_asistentes_multiple = explode(',', $sAsistentes);
            if (count($a_asistentes_multiple) > 1) {
                $asistentes_txt = "[";
                foreach ($a_asistentes_multiple as $asis) {
                    $asistentes_txt .= $this->aAsistentes[$asis];
                }
                $asistentes_txt .= "]";
            } else {
                $asis = $a_asistentes_multiple[0];
                $asistentes_txt = $this->aAsistentes[$asis];
            }

            $this->sasistentes = $asistentes_txt;
        } else {
            return false;
        }
    }

    /**
     * Recupera el atributo asistentes en format de integer
     *
     * @return integer
     */
    public function getAsistentesId()
    {
        return $this->sasistentes;

    }

    /**
     * Estableix l'atribut asistentes en format de integer
     *
     * @return 'false' si falla
     */
    public function setAsistentesId($id)
    {
        $this->sasistentes = $id;
    }

    /**
     * Recupera el atributo asistentes en format de regexp
     *
     * @return integer
     */
    public function getAsistentesRegexp()
    {
        return $this->getSfsvRegexp() . $this->sasistentes;

    }

    /**
     * Recupera el atributo asistentes posibles en format de array
     *
     * @return array
     */
    public function getAsistentesPosibles()
    {
        $aText = $this->getFlipAsistentes();
        if (!empty($this->sasistentes)) {
            $regexp = $this->getAsistentesRegexp();
        } else {
            $regexp = $this->getSfsvRegexp();
        }
        $GesTipoDeActividades = new GestorTipoDeActividad();
        return $GesTipoDeActividades->getAsistentesPosibles($aText, $regexp);
    }

    /**
     * Recupera el atributo acividades en format de text
     *
     * @return string
     */
    public function getActividadText()
    {
        $aText = $this->getFlipActividad1Digito();
        if (is_numeric($this->sactividad)) {
            return $aText[$this->sactividad];
        } else {
            return 'all';
        }
    }

    /**
     * Estableix l'atribut actividades en format de text
     *
     * @return false si falla
     */
    public function setActividadText($sActividad)
    {
        if (is_string($sActividad)) {
            if (empty($sActividad)) {
                $sActividad = 'all';
            }
            $this->sactividad = $this->aActividad1Digito[$sActividad];
        } else {
            return false;
        }
    }

    /**
     * Recupera el atributo acividades (2 digits) en format de text
     *
     * @return string
     */
    public function getActividad2DigitosText()
    {
        $aText = $this->getFlipActividad2Digitos();
        if (is_numeric($this->sactividad)) {
            return $aText[$this->sactividad];
        } else {
            return 'all';
        }
    }

    /**
     * Estableix l'atribut actividades (2 digits) en format de text
     *
     * @return false si falla
     */
    public function setActividad2DigitosText($sActividad)
    {
        if (is_string($sActividad)) {
            if (empty($sActividad)) {
                $sActividad = 'all';
            }
            $this->sactividad = $this->aActividad2Digitos[$sActividad];
        } else {
            return false;
        }
    }

    /**
     * Recupera el atributo acividades en format de integer
     *
     * @return integer
     */
    public function getActividadId()
    {
        return $this->sactividad;

    }

    /**
     * Estableix l'atribut asistentes en format de integer
     *
     * @return 'false' si falla
     */
    public function setActividadId($id)
    {
        $this->sactividad = $id;
    }

    /**
     * Recupera el atributo actividad en format de regexp
     *
     * @return integer
     */
    public function getActividadRegexp()
    {
        return $this->getAsistentesRegexp() . $this->sactividad;

    }

    /**
     * Recupera el atributo acividades posibles en format de array
     *
     * @return array
     */
    public function getActividadesPosibles1Digito()
    {
        $aText = $this->getFlipActividad1Digito();
        $GesTipoDeActividades = new GestorTipoDeActividad();
        return $GesTipoDeActividades->getActividadesPosibles(1, $aText, $this->getAsistentesRegexp());
    }

    public function getActividadesPosibles2Digitos()
    {
        $aText = $this->getFlipActividad2Digitos();
        $GesTipoDeActividades = new GestorTipoDeActividad();
        return $GesTipoDeActividades->getActividadesPosibles(2, $aText, $this->getAsistentesRegexp());
    }

    /**
     * Recupera el atributo nom_tipo en format de text
     *
     * @return string
     */
    public function getNom_tipoText()
    {
        if (is_numeric($this->snom_tipo)) {
            if (isset($this->afNom_tipo)) {
                return $this->afNom_tipo[$this->snom_tipo];
            } else {
                $this->getNom_tipoPosibles3Digitos();
                return $this->afNom_tipo[$this->snom_tipo];
            }
        } else {
            return 'all';
        }
    }

    /**
     * Estableix l'atribut nom_tipo en format de text
     *
     * @return false si falla
     */
    public function setNom_tipoText($sNom_tipo)
    {
        if (is_string($sNom_tipo)) {
            $this->snom_tipo = $this->aNom_tipo[$sNom_tipo];
        } else {
            return false;
        }
    }

    /**
     * Recupera el atributo nom_tipo en format de integer
     *
     * @return integer
     */
    public function getNom_tipoId()
    {
        return $this->snom_tipo;

    }

    /**
     * Recupera el atributo actividad en format de regexp
     *
     * @return integer
     */
    public function getNom_tipoRegexp()
    {
        return $this->getActividadRegexp() . $this->snom_tipo;

    }

    /**
     * Recupera el atributo nom_tipo posibles en formato de array
     *
     * @return array
     */
    public function getNom_tipoPosibles3Digitos()
    {
        $GesTipoDeActividades = new GestorTipoDeActividad();
        $rta = $GesTipoDeActividades->getNom_tipoPosibles(3, $this->getActividadRegexp());
        $this->afNom_tipo = $rta['tipo_nom'];
        $this->aNom_tipo = $rta['nom_tipo'];
        return $rta['tipo_nom'];
    }

    public function getNom_tipoPosibles2Digitos()
    {
        $GesTipoDeActividades = new GestorTipoDeActividad();
        $rta = $GesTipoDeActividades->getNom_tipoPosibles(2, $this->getActividadRegexp());
        $this->afNom_tipo = $rta['tipo_nom'];
        $this->aNom_tipo = $rta['nom_tipo'];
        return $rta['tipo_nom'];
    }

    /**
     * Retorna els posibles id_tipo en format de array
     *
     * @param regexp expresió regular per tornar el id (substring('bla' from regexp) del postgresql).
     * @return array
     */
    public function getId_tipoPosibles($regexp = '.*')
    {
        $GesTipoDeActividades = new GestorTipoDeActividad();
        return $GesTipoDeActividades->getId_tipoPosibles($regexp, $this->getActividadRegexp());
    }

    /**
     * @return boolean
     */
    public function getExtendida()
    {
        return $this->extendida;
    }

    /**
     * @param boolean $extendida
     */
    public function setExtendida($extendida)
    {
        $this->extendida = $extendida;
    }


}
