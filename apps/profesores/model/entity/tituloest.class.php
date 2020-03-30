<?php
namespace profesores\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_titulo_est
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */
/**
 * Classe que implementa l'entitat d_titulo_est
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */
class TituloEst Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TituloEst
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TituloEst
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de TituloEst
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de TituloEst
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Titulo de TituloEst
	 *
	 * @var string
	 */
	 private $stitulo;
	/**
	 * Centro_dnt de TituloEst
	 *
	 * @var string
	 */
	 private $scentro_dnt;
	/**
	 * Eclesiastico de TituloEst
	 *
	 * @var boolean
	 */
	 private $beclesiastico;
	/**
	 * Year de TituloEst
	 *
	 * @var integer
	 */
	 private $iyear;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de TituloEst
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de TituloEst
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_item,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_titulo_est');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar('guardar') === false) { $bInsert=true; } else { $bInsert=false; }
		$aDades=array();
		$aDades['titulo'] = $this->stitulo;
		$aDades['centro_dnt'] = $this->scentro_dnt;
		$aDades['eclesiastico'] = $this->beclesiastico;
		$aDades['year'] = $this->iyear;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['eclesiastico']) ) { $aDades['eclesiastico']='true'; } else { $aDades['eclesiastico']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					titulo                   = :titulo,
					centro_dnt               = :centro_dnt,
					eclesiastico             = :eclesiastico,
					year                     = :year";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item AND id_nom=$this->iid_nom")) === false) {
				$sClauError = 'TituloEst.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'TituloEst.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,titulo,centro_dnt,eclesiastico,year)";
			$valores="(:id_nom,:titulo,:centro_dnt,:eclesiastico,:year)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TituloEst.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'TituloEst.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId('d_titulo_est_id_item_seq');
		}
		$this->setAllAtributes($aDades);
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_item) && isset($this->iid_nom)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item AND id_nom=$this->iid_nom")) === false) {
				$sClauError = 'TituloEst.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return false;
					break;
				default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return true;
		} else {
		   	return false;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item AND id_nom=$this->iid_nom")) === false) {
			$sClauError = 'TituloEst.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return true;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('titulo',$aDades)) $this->setTitulo($aDades['titulo']);
		if (array_key_exists('centro_dnt',$aDades)) $this->setCentro_dnt($aDades['centro_dnt']);
		if (array_key_exists('eclesiastico',$aDades)) $this->setEclesiastico($aDades['eclesiastico']);
		if (array_key_exists('year',$aDades)) $this->setYear($aDades['year']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_item('');
		$this->setId_nom('');
		$this->setTitulo('');
		$this->setCentro_dnt('');
		$this->setEclesiastico('');
		$this->setYear('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TituloEst en un array
	 *
	 * @return array aDades
	 */
	function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar('tot');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de TituloEst en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item,'id_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de TituloEst en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_item de TituloEst
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de TituloEst
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de TituloEst
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		if (!isset($this->iid_nom)) {
			$this->DBCarregar();
		}
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de TituloEst
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut stitulo de TituloEst
	 *
	 * @return string stitulo
	 */
	function getTitulo() {
		if (!isset($this->stitulo)) {
			$this->DBCarregar();
		}
		return $this->stitulo;
	}
	/**
	 * estableix el valor de l'atribut stitulo de TituloEst
	 *
	 * @param string stitulo='' optional
	 */
	function setTitulo($stitulo='') {
		$this->stitulo = $stitulo;
	}
	/**
	 * Recupera l'atribut scentro_dnt de TituloEst
	 *
	 * @return string scentro_dnt
	 */
	function getCentro_dnt() {
		if (!isset($this->scentro_dnt)) {
			$this->DBCarregar();
		}
		return $this->scentro_dnt;
	}
	/**
	 * estableix el valor de l'atribut scentro_dnt de TituloEst
	 *
	 * @param string scentro_dnt='' optional
	 */
	function setCentro_dnt($scentro_dnt='') {
		$this->scentro_dnt = $scentro_dnt;
	}
	/**
	 * Recupera l'atribut beclesiastico de TituloEst
	 *
	 * @return boolean beclesiastico
	 */
	function getEclesiastico() {
		if (!isset($this->beclesiastico)) {
			$this->DBCarregar();
		}
		return $this->beclesiastico;
	}
	/**
	 * estableix el valor de l'atribut beclesiastico de TituloEst
	 *
	 * @param boolean beclesiastico='f' optional
	 */
	function setEclesiastico($beclesiastico='f') {
		$this->beclesiastico = $beclesiastico;
	}
	/**
	 * Recupera l'atribut iyear de TituloEst
	 *
	 * @return integer iyear
	 */
	function getYear() {
		if (!isset($this->iyear)) {
			$this->DBCarregar();
		}
		return $this->iyear;
	}
	/**
	 * estableix el valor de l'atribut iyear de TituloEst
	 *
	 * @param integer iyear='' optional
	 */
	function setYear($iyear='') {
		$this->iyear = $iyear;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTituloEstSet = new core\Set();

		$oTituloEstSet->add($this->getDatosTitulo());
		$oTituloEstSet->add($this->getDatosCentro_dnt());
		$oTituloEstSet->add($this->getDatosEclesiastico());
		$oTituloEstSet->add($this->getDatosYear());
		return $oTituloEstSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut stitulo de TituloEst
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTitulo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'titulo'));
		$oDatosCampo->setEtiqueta(_("título"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(25);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scentro_dnt de TituloEst
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCentro_dnt() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'centro_dnt'));
		$oDatosCampo->setEtiqueta(_("centro docente"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(25);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut beclesiastico de TituloEst
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEclesiastico() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'eclesiastico'));
		$oDatosCampo->setEtiqueta(_("eclesiástico"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iyear de TituloEst
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosYear() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'year'));
		$oDatosCampo->setEtiqueta(_("año"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(5);
		return $oDatosCampo;
	}
}
?>
