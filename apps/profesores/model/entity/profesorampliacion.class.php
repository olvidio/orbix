<?php
namespace profesores\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_profesor_ampliacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
/**
 * Classe que implementa l'entitat d_profesor_ampliacion
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/04/2014
 */
class ProfesorAmpliacion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ProfesorAmpliacion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ProfesorAmpliacion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de ProfesorAmpliacion
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de ProfesorAmpliacion
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Id_asignatura de ProfesorAmpliacion
	 *
	 * @var integer
	 */
	 private $iid_asignatura;
	/**
	 * Escrito_nombramiento de ProfesorAmpliacion
	 *
	 * @var string
	 */
	 private $sescrito_nombramiento;
	/**
	 * F_nombramiento de ProfesorAmpliacion
	 *
	 * @var date
	 */
	 private $df_nombramiento;
	/**
	 * Escrito_cese de ProfesorAmpliacion
	 *
	 * @var string
	 */
	 private $sescrito_cese;
	/**
	 * F_cese de ProfesorAmpliacion
	 *
	 * @var date
	 */
	 private $df_cese;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ProfesorAmpliacion
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ProfesorAmpliacion
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
		$this->setNomTabla('d_profesor_ampliacion');
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
		$aDades['id_asignatura'] = $this->iid_asignatura;
		$aDades['escrito_nombramiento'] = $this->sescrito_nombramiento;
		$aDades['f_nombramiento'] = $this->df_nombramiento;
		$aDades['escrito_cese'] = $this->sescrito_cese;
		$aDades['f_cese'] = $this->df_cese;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_asignatura            = :id_asignatura,
					escrito_nombramiento     = :escrito_nombramiento,
					f_nombramiento           = :f_nombramiento,
					escrito_cese             = :escrito_cese,
					f_cese                   = :f_cese";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === false) {
				$sClauError = 'ProfesorAmpliacion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorAmpliacion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,id_asignatura,escrito_nombramiento,f_nombramiento,escrito_cese,f_cese)";
			$valores="(:id_nom,:id_asignatura,:escrito_nombramiento,:f_nombramiento,:escrito_cese,:f_cese)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ProfesorAmpliacion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorAmpliacion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId('d_profesor_ampliacion_id_item_seq');
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
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
				$sClauError = 'ProfesorAmpliacion.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
			$sClauError = 'ProfesorAmpliacion.eliminar';
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
		if (array_key_exists('id_asignatura',$aDades)) $this->setId_asignatura($aDades['id_asignatura']);
		if (array_key_exists('escrito_nombramiento',$aDades)) $this->setEscrito_nombramiento($aDades['escrito_nombramiento']);
		if (array_key_exists('f_nombramiento',$aDades)) $this->setF_nombramiento($aDades['f_nombramiento']);
		if (array_key_exists('escrito_cese',$aDades)) $this->setEscrito_cese($aDades['escrito_cese']);
		if (array_key_exists('f_cese',$aDades)) $this->setF_cese($aDades['f_cese']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ProfesorAmpliacion en un array
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
	 * Recupera las claus primàries de ProfesorAmpliacion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item'=>$this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de ProfesorAmpliacion
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
	 * estableix el valor de l'atribut iid_item de ProfesorAmpliacion
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de ProfesorAmpliacion
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
	 * estableix el valor de l'atribut iid_nom de ProfesorAmpliacion
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_asignatura de ProfesorAmpliacion
	 *
	 * @return integer iid_asignatura
	 */
	function getId_asignatura() {
		if (!isset($this->iid_asignatura)) {
			$this->DBCarregar();
		}
		return $this->iid_asignatura;
	}
	/**
	 * estableix el valor de l'atribut iid_asignatura de ProfesorAmpliacion
	 *
	 * @param integer iid_asignatura='' optional
	 */
	function setId_asignatura($iid_asignatura='') {
		$this->iid_asignatura = $iid_asignatura;
	}
	/**
	 * Recupera l'atribut sescrito_nombramiento de ProfesorAmpliacion
	 *
	 * @return string sescrito_nombramiento
	 */
	function getEscrito_nombramiento() {
		if (!isset($this->sescrito_nombramiento)) {
			$this->DBCarregar();
		}
		return $this->sescrito_nombramiento;
	}
	/**
	 * estableix el valor de l'atribut sescrito_nombramiento de ProfesorAmpliacion
	 *
	 * @param string sescrito_nombramiento='' optional
	 */
	function setEscrito_nombramiento($sescrito_nombramiento='') {
		$this->sescrito_nombramiento = $sescrito_nombramiento;
	}
	/**
	 * Recupera l'atribut df_nombramiento de ProfesorAmpliacion
	 *
	 * @return date df_nombramiento
	 */
	function getF_nombramiento() {
		if (!isset($this->df_nombramiento)) {
			$this->DBCarregar();
		}
		return $this->df_nombramiento;
	}
	/**
	 * estableix el valor de l'atribut df_nombramiento de ProfesorAmpliacion
	 *
	 * @param date df_nombramiento='' optional
	 */
	function setF_nombramiento($df_nombramiento='') {
		$this->df_nombramiento = $df_nombramiento;
	}
	/**
	 * Recupera l'atribut sescrito_cese de ProfesorAmpliacion
	 *
	 * @return string sescrito_cese
	 */
	function getEscrito_cese() {
		if (!isset($this->sescrito_cese)) {
			$this->DBCarregar();
		}
		return $this->sescrito_cese;
	}
	/**
	 * estableix el valor de l'atribut sescrito_cese de ProfesorAmpliacion
	 *
	 * @param string sescrito_cese='' optional
	 */
	function setEscrito_cese($sescrito_cese='') {
		$this->sescrito_cese = $sescrito_cese;
	}
	/**
	 * Recupera l'atribut df_cese de ProfesorAmpliacion
	 *
	 * @return date df_cese
	 */
	function getF_cese() {
		if (!isset($this->df_cese)) {
			$this->DBCarregar();
		}
		return $this->df_cese;
	}
	/**
	 * estableix el valor de l'atribut df_cese de ProfesorAmpliacion
	 *
	 * @param date df_cese='' optional
	 */
	function setF_cese($df_cese='') {
		$this->df_cese = $df_cese;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oProfesorAmpliacionSet = new core\Set();

		$oProfesorAmpliacionSet->add($this->getDatosId_asignatura());
		$oProfesorAmpliacionSet->add($this->getDatosEscrito_nombramiento());
		$oProfesorAmpliacionSet->add($this->getDatosF_nombramiento());
		$oProfesorAmpliacionSet->add($this->getDatosEscrito_cese());
		$oProfesorAmpliacionSet->add($this->getDatosF_cese());
		return $oProfesorAmpliacionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_asignatura de ProfesorAmpliacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_asignatura() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_asignatura'));
		$oDatosCampo->setEtiqueta(_("asignatura"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('asignaturas\model\entity\Asignatura'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('nombre_corto'); // clave con la que crear el objeto relacionado
		$oDatosCampo->setArgument3('getListaAsignaturas'); // método con que crear la lista de opciones del Gestor objeto relacionado.
	
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sescrito_nombramiento de ProfesorAmpliacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosEscrito_nombramiento() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'escrito_nombramiento'));
		$oDatosCampo->setEtiqueta(_("escrito de nombramiento"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_nombramiento de ProfesorAmpliacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_nombramiento() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_nombramiento'));
		$oDatosCampo->setEtiqueta(_("fecha de nombramiento"));
		$oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sescrito_cese de ProfesorAmpliacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosEscrito_cese() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'escrito_cese'));
		$oDatosCampo->setEtiqueta(_("escrito de cese"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_cese de ProfesorAmpliacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_cese() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_cese'));
		$oDatosCampo->setEtiqueta(_("fecha de cese"));
		$oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
}
?>
