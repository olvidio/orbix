<?php
namespace asignaturas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class Departamento Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Departamento
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Departamento
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_departamento de Departamento
	 *
	 * @var integer
	 */
	 private $iid_departamento;
	/**
	 * Departamento de Departamento
	 *
	 * @var string
	 */
	 private $sdepartamento;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_departamento
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id === 'id_departamento') && $val_id !== '') $this->iid_departamento = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_departamento = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_departamento' => $this->iid_departamento);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xe_departamentos');
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
		$aDades['departamento'] = $this->sdepartamento;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					departamento             = :departamento";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_departamento='$this->iid_departamento'")) === false) {
				$sClauError = 'Departamento.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Departamento.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(departamento)";
			$valores="(:departamento)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Departamento.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Departamento.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_departamento'] = $oDbl->lastInsertId('xe_departamen_id_departamen_seq');
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
		if (isset($this->iid_departamento)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_departamento='$this->iid_departamento'")) === false) {
				$sClauError = 'Departamento.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_departamento='$this->iid_departamento'")) === false) {
			$sClauError = 'Departamento.eliminar';
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
		if (array_key_exists('id_departamento',$aDades)) $this->setId_departamento($aDades['id_departamento']);
		if (array_key_exists('departamento',$aDades)) $this->setDepartamento($aDades['departamento']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_departamento('');
		$this->setDepartamento('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Departamento en un array
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
	 * Recupera las claus primàries de Departamento en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_departamento' => $this->iid_departamento);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Estableix las claus primàries de Departamento en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_departamento') && $val_id !== '') $this->iid_departamento = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_departamento de Departamento
	 *
	 * @return integer iid_departamento
	 */
	function getId_departamento() {
		if (!isset($this->iid_departamento)) {
			$this->DBCarregar();
		}
		return $this->iid_departamento;
	}
	/**
	 * estableix el valor de l'atribut iid_departamento de Departamento
	 *
	 * @param integer iid_departamento
	 */
	function setId_departamento($iid_departamento) {
		$this->iid_departamento = $iid_departamento;
	}
	/**
	 * Recupera l'atribut sdepartamento de Departamento
	 *
	 * @return string sdepartamento
	 */
	function getDepartamento() {
		if (!isset($this->sdepartamento)) {
			$this->DBCarregar();
		}
		return $this->sdepartamento;
	}
	/**
	 * estableix el valor de l'atribut sdepartamento de Departamento
	 *
	 * @param string sdepartamento='' optional
	 */
	function setDepartamento($sdepartamento='') {
		$this->sdepartamento = $sdepartamento;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDepartamentoSet = new core\Set();

		$oDepartamentoSet->add($this->getDatosDepartamento());
		return $oDepartamentoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut sdepartamento de Departamento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDepartamento() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'departamento'));
		$oDatosCampo->setEtiqueta(_("departamento"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(50);
		return $oDatosCampo;
	}
}
?>
