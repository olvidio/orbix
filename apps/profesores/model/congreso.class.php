<?php
namespace profesores\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_congresos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */
/**
 * Classe que implementa l'entitat d_congresos
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */
class Congreso Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Congreso
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Congreso
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de Congreso
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Id_item de Congreso
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de Congreso
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Congreso de Congreso
	 *
	 * @var string
	 */
	 private $scongreso;
	/**
	 * Lugar de Congreso
	 *
	 * @var string
	 */
	 private $slugar;
	/**
	 * F_ini de Congreso
	 *
	 * @var date
	 */
	 private $df_ini;
	/**
	 * F_fin de Congreso
	 *
	 * @var date
	 */
	 private $df_fin;
	/**
	 * Organiza de Congreso
	 *
	 * @var string
	 */
	 private $sorganiza;
	/**
	 * Tipo de Congreso
	 *
	 * @var integer
	 */
	 private $itipo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Congreso
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Congreso
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
	 * @param integer|array iid_item
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
		$this->setNomTabla('d_congresos');
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
		$aDades['congreso'] = $this->scongreso;
		$aDades['lugar'] = $this->slugar;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['organiza'] = $this->sorganiza;
		$aDades['tipo'] = $this->itipo;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					congreso                 = :congreso,
					lugar                    = :lugar,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin,
					organiza                 = :organiza,
					tipo                     = :tipo";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'Congreso.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Congreso.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,congreso,lugar,f_ini,f_fin,organiza,tipo)";
			$valores="(:id_nom,:congreso,:lugar,:f_ini,:f_fin,:organiza,:tipo)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Congreso.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'Congreso.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId('d_congresos_id_item_seq');
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
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE  id_item='$this->iid_item' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'Congreso.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
					break;
				default:
					$this->setAllAtributes($aDades);
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE  id_item='$this->iid_item' AND id_nom='$this->iid_nom'")) === false) {
			$sClauError = 'Congreso.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('congreso',$aDades)) $this->setCongreso($aDades['congreso']);
		if (array_key_exists('lugar',$aDades)) $this->setLugar($aDades['lugar']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini']);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin']);
		if (array_key_exists('organiza',$aDades)) $this->setOrganiza($aDades['organiza']);
		if (array_key_exists('tipo',$aDades)) $this->setTipo($aDades['tipo']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Congreso en un array
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
	 * Recupera las claus primàries de Congreso en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item'=>$this->iid_item,'id_nom'=>$this->iid_nom);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_schema de Congreso
	 *
	 * @return integer iid_schema
	 */
	function getId_schema() {
		if (!isset($this->iid_schema)) {
			$this->DBCarregar();
		}
		return $this->iid_schema;
	}
	/**
	 * estableix el valor de l'atribut iid_schema de Congreso
	 *
	 * @param integer iid_schema='' optional
	 */
	function setId_schema($iid_schema='') {
		$this->iid_schema = $iid_schema;
	}
	/**
	 * Recupera l'atribut iid_item de Congreso
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
	 * estableix el valor de l'atribut iid_item de Congreso
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de Congreso
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
	 * estableix el valor de l'atribut iid_nom de Congreso
	 *
	 * @param integer iid_nom='' optional
	 */
	function setId_nom($iid_nom='') {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut scongreso de Congreso
	 *
	 * @return string scongreso
	 */
	function getCongreso() {
		if (!isset($this->scongreso)) {
			$this->DBCarregar();
		}
		return $this->scongreso;
	}
	/**
	 * estableix el valor de l'atribut scongreso de Congreso
	 *
	 * @param string scongreso='' optional
	 */
	function setCongreso($scongreso='') {
		$this->scongreso = $scongreso;
	}
	/**
	 * Recupera l'atribut slugar de Congreso
	 *
	 * @return string slugar
	 */
	function getLugar() {
		if (!isset($this->slugar)) {
			$this->DBCarregar();
		}
		return $this->slugar;
	}
	/**
	 * estableix el valor de l'atribut slugar de Congreso
	 *
	 * @param string slugar='' optional
	 */
	function setLugar($slugar='') {
		$this->slugar = $slugar;
	}
	/**
	 * Recupera l'atribut df_ini de Congreso
	 *
	 * @return date df_ini
	 */
	function getF_ini() {
		if (!isset($this->df_ini)) {
			$this->DBCarregar();
		}
		return $this->df_ini;
	}
	/**
	 * estableix el valor de l'atribut df_ini de Congreso
	 *
	 * @param date df_ini='' optional
	 */
	function setF_ini($df_ini='') {
		$this->df_ini = $df_ini;
	}
	/**
	 * Recupera l'atribut df_fin de Congreso
	 *
	 * @return date df_fin
	 */
	function getF_fin() {
		if (!isset($this->df_fin)) {
			$this->DBCarregar();
		}
		return $this->df_fin;
	}
	/**
	 * estableix el valor de l'atribut df_fin de Congreso
	 *
	 * @param date df_fin='' optional
	 */
	function setF_fin($df_fin='') {
		$this->df_fin = $df_fin;
	}
	/**
	 * Recupera l'atribut sorganiza de Congreso
	 *
	 * @return string sorganiza
	 */
	function getOrganiza() {
		if (!isset($this->sorganiza)) {
			$this->DBCarregar();
		}
		return $this->sorganiza;
	}
	/**
	 * estableix el valor de l'atribut sorganiza de Congreso
	 *
	 * @param string sorganiza='' optional
	 */
	function setOrganiza($sorganiza='') {
		$this->sorganiza = $sorganiza;
	}
	/**
	 * Recupera l'atribut itipo de Congreso
	 *
	 * @return integer itipo
	 */
	function getTipo() {
		if (!isset($this->itipo)) {
			$this->DBCarregar();
		}
		return $this->itipo;
	}
	/**
	 * estableix el valor de l'atribut itipo de Congreso
	 *
	 * @param integer itipo='' optional
	 */
	function setTipo($itipo='') {
		$this->itipo = $itipo;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCongresoSet = new core\Set();

		$oCongresoSet->add($this->getDatosCongreso());
		$oCongresoSet->add($this->getDatosLugar());
		$oCongresoSet->add($this->getDatosF_ini());
		$oCongresoSet->add($this->getDatosF_fin());
		$oCongresoSet->add($this->getDatosOrganiza());
		$oCongresoSet->add($this->getDatosTipo());
		return $oCongresoSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut scongreso de Congreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCongreso() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'congreso'));
		$oDatosCampo->setEtiqueta(_("congreso"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(80);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slugar de Congreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosLugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lugar'));
		$oDatosCampo->setEtiqueta(_("lugar"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de Congreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ini'));
		$oDatosCampo->setEtiqueta(_("fecha inico"));
		$oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_fin de Congreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_fin'));
		$oDatosCampo->setEtiqueta(_("fecha fin"));
		$oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sorganiza de Congreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosOrganiza() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'organiza'));
		$oDatosCampo->setEtiqueta(_("organiza"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(50);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itipo de Congreso
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo'));
		$oDatosCampo->setEtiqueta(_("tipo"));
		 $oDatosCampo->setTipo('array');
        $oDatosCampo->setArgument(array( 1=> _("cv"), 2=> _("congreso"), 3=> _("reunión") ));
		return $oDatosCampo;
	}
}
?>
