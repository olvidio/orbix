<?php
namespace asignaturas\model\entity;
use core;
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/12/2010
 */
class AsignaturaTipo Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de AsignaturaTipo
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de AsignaturaTipo
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_tipo de AsignaturaTipo
	 *
	 * @var integer
	 */
	 private $iid_tipo;
	/**
	 * Tipo_asignatura de AsignaturaTipo
	 *
	 * @var string
	 */
	 private $stipo_asignatura;
	/**
	 * Tipo_breve de AsignaturaTipo
	 *
	 * @var string
	 */
	 private $stipo_breve;
	/**
	 * Año de AsignaturaTipo
	 *
	 * @var string
	 */
	 private $saño;
	/**
	 * Tipo_latin de AsignaturaTipo
	 *
	 * @var string
	 */
	 private $stipo_latin;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_tipo
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_tipo') && $val_id !== '') $this->iid_tipo = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_tipo = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_tipo' => $this->iid_tipo);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xa_tipo_asig');
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
		$aDades['tipo_asignatura'] = $this->stipo_asignatura;
		$aDades['tipo_breve'] = $this->stipo_breve;
		$aDades['año'] = $this->saño;
		$aDades['tipo_latin'] = $this->stipo_latin;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					tipo_asignatura          = :tipo_asignatura,
					tipo_breve               = :tipo_breve,
					año                     = :año,
					tipo_latin               = :tipo_latin";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_tipo='$this->iid_tipo'")) === false) {
				$sClauError = 'AsignaturaTipo.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'AsignaturaTipo.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_tipo);
			$campos="(id_tipo,tipo_asignatura,tipo_breve,año,tipo_latin)";
			$valores="(:id_tipo,:tipo_asignatura,:tipo_breve,:año,:tipo_latin)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'AsignaturaTipo.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'AsignaturaTipo.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
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
		if (isset($this->iid_tipo)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_tipo='$this->iid_tipo'")) === false) {
				$sClauError = 'AsignaturaTipo.carregar';
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
				default:					// En el caso de no existir esta fila, $aDades = FALSE:					if ($aDades === FALSE) {
						$this->setNullAllAtributes();					} else {						$this->setAllAtributes($aDades);					}			}
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_tipo='$this->iid_tipo'")) === false) {
			$sClauError = 'AsignaturaTipo.eliminar';
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
		if (array_key_exists('id_tipo',$aDades)) $this->setId_tipo($aDades['id_tipo']);
		if (array_key_exists('tipo_asignatura',$aDades)) $this->setTipo_asignatura($aDades['tipo_asignatura']);
		if (array_key_exists('tipo_breve',$aDades)) $this->setTipo_breve($aDades['tipo_breve']);
		if (array_key_exists('año',$aDades)) $this->setAño($aDades['año']);
		if (array_key_exists('tipo_latin',$aDades)) $this->setTipo_latin($aDades['tipo_latin']);
	}	/**	 * Estableix a empty el valor de tots els atributs	 *	 */	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setId_tipo('');
		$this->setTipo_asignatura('');
		$this->setTipo_breve('');
		if (array_key_exists('año',$aDades)) $this->setAño($aDades['año']);
		$this->setTipo_latin('');
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de AsignaturaTipo en un array
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
	 * Recupera las claus primàries de AsignaturaTipo en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_tipo' => $this->iid_tipo);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_tipo de AsignaturaTipo
	 *
	 * @return integer iid_tipo
	 */
	function getId_tipo() {
		if (!isset($this->iid_tipo)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo de AsignaturaTipo
	 *
	 * @param integer iid_tipo
	 */
	function setId_tipo($iid_tipo) {
		$this->iid_tipo = $iid_tipo;
	}
	/**
	 * Recupera l'atribut stipo_asignatura de AsignaturaTipo
	 *
	 * @return string stipo_asignatura
	 */
	function getTipo_asignatura() {
		if (!isset($this->stipo_asignatura)) {
			$this->DBCarregar();
		}
		return $this->stipo_asignatura;
	}
	/**
	 * estableix el valor de l'atribut stipo_asignatura de AsignaturaTipo
	 *
	 * @param string stipo_asignatura='' optional
	 */
	function setTipo_asignatura($stipo_asignatura='') {
		$this->stipo_asignatura = $stipo_asignatura;
	}
	/**
	 * Recupera l'atribut stipo_breve de AsignaturaTipo
	 *
	 * @return string stipo_breve
	 */
	function getTipo_breve() {
		if (!isset($this->stipo_breve)) {
			$this->DBCarregar();
		}
		return $this->stipo_breve;
	}
	/**
	 * estableix el valor de l'atribut stipo_breve de AsignaturaTipo
	 *
	 * @param string stipo_breve='' optional
	 */
	function setTipo_breve($stipo_breve='') {
		$this->stipo_breve = $stipo_breve;
	}
	/**
	 * Recupera l'atribut saño de AsignaturaTipo
	 *
	 * @return string saño
	 */
	function getAño() {
		if (!isset($this->saño)) {
			$this->DBCarregar();
		}
		return $this->saño;
	}
	/**
	 * estableix el valor de l'atribut saño de AsignaturaTipo
	 *
	 * @param string saño='' optional
	 */
	function setAño($saño='') {
		$this->saño = $saño;
	}
	/**
	 * Recupera l'atribut stipo_latin de AsignaturaTipo
	 *
	 * @return string stipo_latin
	 */
	function getTipo_latin() {
		if (!isset($this->stipo_latin)) {
			$this->DBCarregar();
		}
		return $this->stipo_latin;
	}
	/**
	 * estableix el valor de l'atribut stipo_latin de AsignaturaTipo
	 *
	 * @param string stipo_latin='' optional
	 */
	function setTipo_latin($stipo_latin='') {
		$this->stipo_latin = $stipo_latin;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oAsignaturaTipoSet = new core\Set();

		$oAsignaturaTipoSet->add($this->getDatosTipo_asignatura());
		$oAsignaturaTipoSet->add($this->getDatosTipo_breve());
		$oAsignaturaTipoSet->add($this->getDatosAño());
		$oAsignaturaTipoSet->add($this->getDatosTipo_latin());
		return $oAsignaturaTipoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut stipo_asignatura de AsignaturaTipo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_asignatura() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_asignatura'));
		$oDatosCampo->setEtiqueta(_("tipo de asignatura"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo_breve de AsignaturaTipo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_breve() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_breve'));
		$oDatosCampo->setEtiqueta(_("tipo breve"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut saño de AsignaturaTipo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAño() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'año'));
		$oDatosCampo->setEtiqueta(_("año"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo_latin de AsignaturaTipo
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_latin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_latin'));
		$oDatosCampo->setEtiqueta(_("tipo_latin"));
		return $oDatosCampo;
	}
}
?>
