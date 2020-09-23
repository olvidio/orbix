<?php
namespace web;
use actividades\model\entity\GestorTipoDeActividad;

/**
 * Classe que implementa l'entitat tipos de actividades
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 18/10/2010
 */

class TiposActividades {
	/* ATRIBUTS ----------------------------------------------------------------- */

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
	private $aSfsv=array(
				"sv"=>1,
				"sf"=>2,
				"reservada"=>3,
				"all"=>'.'
			);

	/**
	 * aAsistentes de TiposActividades
	 *
	 * @var array
	 */
	private	$aAsistentes=array(
				"n"=>1,
				"nax"=>2,
				"agd"=>3,
				"s"=>4,
				"sg"=>5,
				"sss+" =>6,
				"sr"=>7,
				"sr-nax"=>8,
				"sr-agd"=>9,
				"all"=>'.'
			);

	/**
	 * aActividad de TiposActividades
	 *
	 * @var array
	 */
	private $aActividad = array (
				"crt"=>1,
				"ca"=>2,
				"cv"=>3,
				"cve"=>4,
				"cv-crt"=>5,
				"all"=>'.'
			);

	//transpongo los vectores para buscar por números y no por el texto
	private $afSfsv = array();
	private $afAsistentes = array();
	private $afActividad = array();

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 *
	 * @param integer|string sid_tipo_proceso
	 */
	function __construct($id='') {
		if (isset($id) && $id !== '') {
			if (is_numeric($id)) $this->iid_tipo_activ = $id;
			$this->separarId($id);
		}
	}

	/* METODES PRIVATS ----------------------------------------------------------*/
	
	private function getFlipSfsv(){
		if (empty($this->afSfsv)) $this->afSfsv = array_flip($this->aSfsv);
		return $this->afSfsv;
	}
	private function getFlipAsistentes(){
		if (empty($this->afAsistentes)) $this->afAsistentes = array_flip($this->aAsistentes);
		return $this->afAsistentes;
	}
	private function getFlipActividad(){
		if (empty($this->afActividad)) $this->afActividad = array_flip($this->aActividad);
		return $this->afActividad;
	}

	private function separarId($sregexp_id_tipo_activ) {
		if(!empty($sregexp_id_tipo_activ)) {
			$inc = 0;
			if (($ini = strpos($sregexp_id_tipo_activ, '[')) !== false) {
				$fin = strpos($sregexp_id_tipo_activ, ']');
				$inc = $fin - $ini;
			}
			$long = empty($inc)? 6 : 6+$inc;
			for ($i=strlen($sregexp_id_tipo_activ);$i<$long;$i++) {
				$sregexp_id_tipo_activ.='.';
			}
			$matches = [];
			preg_match('/(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\d{3}|\.*)/', $sregexp_id_tipo_activ,$matches);
			if (!empty($matches)) {
				$this->sregexp_id_tipo_activ=$matches[0];
				$this->ssfsv=$matches[1];
				$this->sasistentes=$matches[2];
				$this->sactividad=$matches[3];
				$this->snom_tipo=$matches[4];
			}
		} else {
			return false;
		}
	}
	
	/* METODES PUBLICS ----------------------------------------------------------*/
	
	public function setPosiblesAll($bAll) {
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
	public function getArrayAsistentesIndividual(){
	    $a_tipos = [];
	    $aAsistentes = $this->getAsistentesPosibles();
	    foreach ($aAsistentes as $iasistentes => $sasistentes) {
	        $txt_id = $this->getSfsvText().' '.$sasistentes;
	        $a_tipos[$txt_id] = $this->getSfsvId().$iasistentes;
	    }
	    return $a_tipos; 
	}
	
	
	/**
	 * Recupera l'atribut id_tipo_activ en format de regexp
	 *
	 * @return string
	 */
	public function getId_tipo_activ() {
		$txt= $this->ssfsv;
		$txt.=$this->sasistentes;
		$txt.=$this->sactividad;
		$txt.=$this->snom_tipo;
		return $txt;
	}
	/**
	 * Recupera l'atribut nom en format de text
	 * sense el (sin especificar)
	 *
	 * @return string
	 */
	public function getNomGral() {
		$txt= $this->getSfsvText();
		if ($this->getAsistentesText() <> 'all') $txt.= ' '.$this->getAsistentesText();
		if ($this->getActividadText() <> 'all') $txt.= ' '.$this->getActividadText();
		if ($this->getNom_tipoId() <> 0 && $this->getNom_tipoText() <> 'all') $txt.= ' '.$this->getNom_tipoText();
		return $txt;
	}
	/**
	 * Recupera l'atribut nom en format de text
	 *
	 * @return string
	 */
	public function getNom() {
		$txt= $this->getSfsvText();
		if ($this->getAsistentesText() <> 'all') $txt.= ' '.$this->getAsistentesText();
		if ($this->getActividadText() <> 'all') $txt.= ' '.$this->getActividadText();
		if ($this->getNom_tipoText() <> 'all') $txt.= ' '.$this->getNom_tipoText();
		return $txt;
	}
	/**
	 * Recupera l'atribut sfsv en format de text
	 *
	 * @return string
	 */
	public function getSfsvText() {
		$aText=$this->getFlipSfsv();
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
	public function setSfsvText($sSfsv) {
		if (is_string($sSfsv)) {
		    if (empty($sSfsv)) { $sSfsv = 'all'; }
			$this->ssfsv=$this->aSfsv[$sSfsv];
		} else {
			return false;
		}
	}
	/**
	 * Recupera l'atribut sfsv en format de integer
	 *
	 */
	public function getSfsvId() {
		return $this->ssfsv;
	}
	/**
	 * Estableix l'atribut sfsv en format de integer
	 *
	 * @return integer
	 */
	public function setSfsvId($isfsv) {
		$this->ssfsv = $isfsv;
	}
	/**
	 * Recupera l'atribut sfsv en format de regexp
	 *
	 * @return integer
	 */
	public function getSfsvRegexp() {
		return '^'.$this->ssfsv;

	}
	/**
	 * Recupera l'atribut sfsv posibles en format de array
	 *
	 * @return array
	 */
	public function getSfsvPosibles() {
	    $aText=$this->getFlipSfsv();
		$GesTipoDeActividades = new GestorTipoDeActividad();
		return $GesTipoDeActividades->getSfsvPosibles($aText);
	}
	/**
	 * Recupera l'atribut asistentes en format de text
	 *
	 * @return string
	 */
	public function getAsistentesText() {
		$aText=$this->getFlipAsistentes();
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
	public function setAsistentesText($sAsistentes) {
		if (is_string($sAsistentes)) {
		    if (empty($sAsistentes)) { $sAsistentes = 'all'; }
			$this->sasistentes=$this->aAsistentes[$sAsistentes];
		} else {
			return false;
		}
	}
	/**
	 * Recupera l'atribut asistentes en format de integer
	 *
	 * @return integer
	 */
	public function getAsistentesId() {
		return $this->sasistentes;

	}
	/**
	 * Estableix l'atribut asistentes en format de integer
	 *
	 * @return 'false' si falla
	 */
	public function setAsistentesId($id) {
		$this->sasistentes=$id;
	}
	/**
	 * Recupera l'atribut asistentes en format de regexp
	 *
	 * @return integer
	 */
	public function getAsistentesRegexp() {
		return $this->getSfsvRegexp().$this->sasistentes;

	}
	/**
	 * Recupera l'atribut asistentes posibles en format de array
	 *
	 * @return array
	 */
	public function getAsistentesPosibles() {
		$aText=$this->getFlipAsistentes();
		if (!empty($this->sasistentes)) {
			$regexp=$this->getAsistentesRegexp();
		} else {
			$regexp=$this->getSfsvRegexp();
		}
		$GesTipoDeActividades = new GestorTipoDeActividad();
		return $GesTipoDeActividades->getAsistentesPosibles($aText,$regexp);
	}
	/**
	 * Recupera l'atribut acividades en format de text
	 *
	 * @return string
	 */
	public function getActividadText() {
		$aText=$this->getFlipActividad();
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
	public function setActividadText($sActividad) {
		if (is_string($sActividad)) {
		    if (empty($sActividad)) { $sActividad = 'all'; }
			$this->sactividad=$this->aActividad[$sActividad];
		} else {
			return false;
		}
	}
	/**
	 * Recupera l'atribut acividades en format de integer
	 *
	 * @return integer
	 */
	public function getActividadId() {
		return $this->sactividad;

	}
	/**
	 * Recupera l'atribut actividad en format de regexp
	 *
	 * @return integer
	 */
	public function getActividadRegexp() {
		return $this->getAsistentesRegexp().$this->sactividad;

	}
	/**
	 * Recupera l'atribut acividades posibles en format de array
	 *
	 * @return array
	 */
	public function getActividadesPosibles() {
		$aText=$this->getFlipActividad();
		$GesTipoDeActividades = new GestorTipoDeActividad();
		return $GesTipoDeActividades->getActividadesPosibles($aText,$this->getAsistentesRegexp());
	}

	/**
	 * Recupera l'atribut nom_tipo en format de text
	 *
	 * @return string
	 */
	public function getNom_tipoText() {
		if (is_numeric($this->snom_tipo)) {
			if (isset($this->afNom_tipo)){
				return $this->afNom_tipo[$this->snom_tipo];
			} else {
				$this->getNom_tipoPosibles();
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
	public function setNom_tipoText($sNom_tipo) {
		if (is_string($sNom_tipo)) {
			$this->snom_tipo=$this->aNom_tipo[$sNom_tipo];
		} else {
			return false;
		}
	}
	/**
	 * Recupera l'atribut nom_tipo en format de integer
	 *
	 * @return integer
	 */
	public function getNom_tipoId() {
		return $this->snom_tipo;

	}
	/**
	 * Recupera l'atribut actividad en format de regexp
	 *
	 * @return integer
	 */
	public function getNom_tipoRegexp() {
		return $this->getActividadRegexp().$this->snom_tipo;

	}
	/**
	 * Recupera l'atribut nom_tipo posibles en format de array
	 *
	 * @return array
	 */
	public function getNom_tipoPosibles() {
		$GesTipoDeActividades = new GestorTipoDeActividad();
		$rta = $GesTipoDeActividades->getNom_tipoPosibles($this->getActividadRegexp());
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
	public function getId_tipoPosibles($regexp='.*') {
		$GesTipoDeActividades = new GestorTipoDeActividad();
		return $GesTipoDeActividades->getId_tipoPosibles($regexp,$this->getActividadRegexp());
	}

}
