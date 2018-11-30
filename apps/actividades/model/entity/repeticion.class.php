<?php
namespace actividades\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/02/2011
 */
class Repeticion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Repeticion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Repeticion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_repeticion de Repeticion
	 *
	 * @var integer
	 */
	 private $iid_repeticion;
	/**
	 * Repeticion de Repeticion
	 *
	 * @var string
	 */
	 private $srepeticion;
	/**
	 * Temporada de Repeticion
	 *
	 * @var string
	 */
	 private $stemporada;
	/**
	 * Tipo de Repeticion
	 *
	 * @var integer
	 */
	 private $itipo;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
 
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_repeticion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_repeticion') && $val_id !== '') $this->iid_repeticion = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_repeticion = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_repeticion' => $this->iid_repeticion);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xa_tipo_repeticion');
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
		$aDades['repeticion'] = $this->srepeticion;
		$aDades['temporada'] = $this->stemporada;
		$aDades['tipo'] = $this->itipo;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					repeticion               = :repeticion,
					temporada                = :temporada,
					tipo                = :tipo";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_repeticion='$this->iid_repeticion'")) === false) {
				$sClauError = 'Repeticion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Repeticion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(repeticion,temporada,tipo)";
			$valores="(:repeticion,:temporada,:tipo)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Repeticion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Repeticion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_repeticion'] = $oDbl->lastInsertId($nom_tabla.'_id_repeticion_seq');
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
		if (isset($this->iid_repeticion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_repeticion='$this->iid_repeticion'")) === false) {
				$sClauError = 'Repeticion.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_repeticion='$this->iid_repeticion'")) === false) {
			$sClauError = 'Repeticion.eliminar';
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
		if (array_key_exists('id_repeticion',$aDades)) $this->setId_repeticion($aDades['id_repeticion']);
		if (array_key_exists('repeticion',$aDades)) $this->setRepeticion($aDades['repeticion']);
		if (array_key_exists('temporada',$aDades)) $this->setTemporada($aDades['temporada']);
		if (array_key_exists('tipo',$aDades)) $this->setTipo($aDades['tipo']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Repeticion en un array
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
	 * Recupera las claus primàries de Repeticion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_repeticion' => $this->iid_repeticion);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_repeticion de Repeticion
	 *
	 * @return integer iid_repeticion
	 */
	function getId_repeticion() {
		if (!isset($this->iid_repeticion)) {
			$this->DBCarregar();
		}
		return $this->iid_repeticion;
	}
	/**
	 * estableix el valor de l'atribut iid_repeticion de Repeticion
	 *
	 * @param integer iid_repeticion
	 */
	function setId_repeticion($iid_repeticion) {
		$this->iid_repeticion = $iid_repeticion;
	}
	/**
	 * Recupera l'atribut srepeticion de Repeticion
	 *
	 * @return string srepeticion
	 */
	function getRepeticion() {
		if (!isset($this->srepeticion)) {
			$this->DBCarregar();
		}
		return $this->srepeticion;
	}
	/**
	 * estableix el valor de l'atribut srepeticion de Repeticion
	 *
	 * @param string srepeticion='' optional
	 */
	function setRepeticion($srepeticion='') {
		$this->srepeticion = $srepeticion;
	}
	/**
	 * Recupera l'atribut stemporada de Repeticion
	 *
	 * @return string stemporada
	 */
	function getTemporada() {
		if (!isset($this->stemporada)) {
			$this->DBCarregar();
		}
		return $this->stemporada;
	}
	/**
	 * estableix el valor de l'atribut stemporada de Repeticion
	 *
	 * @param string stemporada='' optional
	 */
	function setTemporada($stemporada='') {
		$this->stemporada = $stemporada;
	}
	/**
	 * Recupera l'atribut itipo de Repeticion
	 *
	 * @return string itipo
	 */
	function getTipo() {
		if (!isset($this->itipo)) {
			$this->DBCarregar();
		}
		return $this->itipo;
	}
	/**
	 * estableix el valor de l'atribut itipo de Repeticion
	 *
	 * @param string itipo='' optional
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
		$oRepeticionSet = new core\Set();

		$oRepeticionSet->add($this->getDatosRepeticion());
		$oRepeticionSet->add($this->getDatosTemporada());
		$oRepeticionSet->add($this->getDatosTipo());
		return $oRepeticionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut srepeticion de Repeticion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosRepeticion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'repeticion'));
		$oDatosCampo->setEtiqueta(_("repetición"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stemporada de Repeticion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTemporada() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'temporada'));
		$oDatosCampo->setEtiqueta(_("temporada"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(1);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo de Repeticion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo'));
		$oDatosCampo->setEtiqueta(_("tipo"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(1);
		return $oDatosCampo;
	}
}
?>
