<?php
namespace web;
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
	 * aDades de ProcesoTipo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_tipo_activ de ProcesoTipo
	 *
	 * @var integer
	 */
	 private $iid_tipo_activ;
	/**
	 * Regexp_id_tipo_activ de ProcesoTipo
	 *
	 * @var string
	 */
	 private $sregexp_id_tipo_activ;

	/**
	 * sfsv de ProcesoTipo
	 *
	 * @var string
	 */
	 private $ssfsv;
	/**
	 * asistentes de ProcesoTipo
	 *
	 * @var string
	 */
	 private $sasistentes;
	/**
	 * actividad de ProcesoTipo
	 *
	 * @var string
	 */
	 private $sactividad;
	/**
	 * nom_tipo de ProcesoTipo
	 *
	 * @var string
	 */
	 private $snom_tipo;
	/**
	 * aSfsv de ProcesoTipo
	 *
	 * @var array
	 */
	private $aSfsv=array(
				"sv"=>1,
				"sf"=>2,
				"reservada"=>3,
				"todos"=>'.'
			);
	
	/**
	 * aAsistentes de ProcesoTipo
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
				"todos"=>'.'
			);

	/**
	 * aActividad de ProcesoTipo
	 *
	 * @var array
	 */
	private $aActividad = array (
				"crt"=>1,
				"ca"=>2,
				"cv"=>3,
				"cve"=>4,
				"cv-crt"=>5,
				"todos"=>'.'
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

	/* METODES PUBLICS ----------------------------------------------------------*/
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
	
	function separarId($sregexp_id_tipo_activ) {
		if(!empty($sregexp_id_tipo_activ)) {
			for ($i=strlen($sregexp_id_tipo_activ);$i<6;$i++) {
				$sregexp_id_tipo_activ.='.';
			}
			preg_match('/(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\[\d+\]|\d|\.)(\d{3}|\.{3})/', $sregexp_id_tipo_activ,$matches);
			$this->sregexp_id_tipo_activ=$matches[0];
			$this->ssfsv=$matches[1];
			$this->sasistentes=$matches[2];
			$this->sactividad=$matches[3];
			$this->snom_tipo=$matches[4];
		} else {
			return false;
		}
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
		if ($this->getAsistentesText() <> _('todos')) $txt.= ' '.$this->getAsistentesText();
		if ($this->getActividadText() <> _('todos')) $txt.= ' '.$this->getActividadText();
		if ($this->getNom_tipoId() <> 0 && $this->getNom_tipoText() <> _('todos')) $txt.= ' '.$this->getNom_tipoText();
		return $txt;
	}
	/**
	 * Recupera l'atribut nom en format de text
	 *
	 * @return string
	 */
	public function getNom() {
		$txt= $this->getSfsvText();
		if ($this->getAsistentesText() <> _('todos')) $txt.= ' '.$this->getAsistentesText();
		if ($this->getActividadText() <> _('todos')) $txt.= ' '.$this->getActividadText();
		if ($this->getNom_tipoText() <> _('todos')) $txt.= ' '.$this->getNom_tipoText();
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
			return _('todos');
		}
	}
	/**
	 * Estableix l'atribut sfsv en format de text
	 *
	 * @return string
	 */
	public function setSfsvText($sSfsv) {
		if (is_string($sSfsv)) {
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
		$oDbl = $GLOBALS['oDBPC'];
		$aText=$this->getFlipSfsv();
		$query_ta="select substr(id_tipo_activ::text,1,1) as ta1 from a_tipos_actividad where id_tipo_activ::text ~'' group by ta1 order by ta1";
		$oDBPCASt_q_ta=$oDbl->query($query_ta);
		$i=0;
		foreach ($oDBPCASt_q_ta->fetchAll() as $row) {
			$i++;
			//$sfsv[$i]=$row[0]."#".$aText[$row[0]];
			$sfsv[$row[0]]=$aText[$row[0]];
		}
		return $sfsv;
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
			return _('todos');
		}
	}
	/**
	 * Estableix l'atribut asistentes en format de text
	 *
	 * @return false si falla
	 */
	public function setAsistentesText($sAsistentes) {
		if (is_string($sAsistentes)) {
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
		$oDbl = $GLOBALS['oDBPC'];
		$aText=$this->getFlipAsistentes();
		if (!empty($this->sasistentes)) {
			$regexp=$this->getAsistentesRegexp();
		} else {
			$regexp=$this->getSfsvRegexp();
		}
		$query_ta="select substr(id_tipo_activ::text,2,1) as ta2
			from a_tipos_actividad where id_tipo_activ::text ~'".$regexp."' group by ta2 order by ta2";
		//echo "query: $query_ta<br>";
		$oDBPCASt_q_ta=$oDbl->query($query_ta);
		foreach ($oDBPCASt_q_ta->fetchAll() as $row) {
			$asistentes[$row[0]]=$aText[$row[0]];
		}
		return $asistentes;
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
			return _('todos');
		}
	}
	/**
	 * Estableix l'atribut actividades en format de text
	 *
	 * @return false si falla
	 */
	public function setActividadText($sActividad) {
		if (is_string($sActividad)) {
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
		$oDbl = $GLOBALS['oDBPC'];
		$aText=$this->getFlipActividad();
		$query_ta="select substr(id_tipo_activ::text,3,1) as ta3
			from a_tipos_actividad where id_tipo_activ::text ~'".$this->getAsistentesRegexp()."' group by ta3 order by ta3";
		$oDBPCASt_q_ta=$oDbl->query($query_ta);
		$i=0;
		foreach ($oDBPCASt_q_ta->fetchAll() as $row) {
			$i++;
			//$asistentes[$i]=$row[0]."#".$aText[$row[0]];
			$actividades[$row[0]]=$aText[$row[0]];
		}
		return $actividades;
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
			return _('todos');
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
		$oDbl = $GLOBALS['oDBPC'];
		$query="SELECT * FROM a_tipos_actividad where id_tipo_activ::text ~'".$this->getActividadRegexp()."' order by id_tipo_activ";
		//echo $query;
		$oDBPCASt_id=$oDbl->query($query);
		$i=0;
		foreach ($oDBPCASt_id->fetchAll() as $row) {
			$i++;
			$nom_tipo[$i] = $row['nombre'].'#'.$row['id_tipo_activ'];
			$num=substr($row['id_tipo_activ'],3,3);
			$tipo_nom[$num] = $row['nombre'];
		}
		$this->afNom_tipo =$tipo_nom;
		$this->aNom_tipo=$nom_tipo;
		return $tipo_nom;
	}
	/**
	 * Retorna els posibles id_tipo en format de array
	 *
	 * @param regexp expresió regular per tornar el id (substring('bla' from regexp) del postgresql). 
	 * @return array
	 */
	public function getId_tipoPosibles($regexp='.*') {
		$oDbl = $GLOBALS['oDBPC'];
		$query="SELECT substring(id_tipo_activ::text from '".$regexp."') 
		   	FROM a_tipos_actividad  where id_tipo_activ::text ~'".$this->getActividadRegexp()."' order by id_tipo_activ";
		//echo $query;
		$oDBPCASt_id=$oDbl->query($query);
		$a_id_tipos = array();
		foreach ($oDBPCASt_id->fetchAll() as $row) {
			$id_tipo = $row[0];
			$a_id_tipos[$id_tipo] = true;
		}
		return $a_id_tipos;
	}

}
?>
