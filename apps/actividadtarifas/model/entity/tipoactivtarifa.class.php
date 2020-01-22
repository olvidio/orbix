<?php
namespace actividadtarifas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula xa_tipo_activ_tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/11/2018
 */
/**
 * Classe que implementa l'entitat xa_tipo_activ_tarifa
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 08/11/2018
 */
class TipoActivTarifa Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de TipoActivTarifa
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de TipoActivTarifa
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de TipoActivTarifa
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Id_item de TipoActivTarifa
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_tarifa de TipoActivTarifa
	 *
	 * @var integer
	 */
	 private $iid_tarifa;
	/**
	 * Id_tipo_activ de TipoActivTarifa
	 *
	 * @var integer
	 */
	 private $iid_tipo_activ;
	/**
	 * Temporada de TipoActivTarifa
	 *
	 * @var integer
	 */
	 private $itemporada;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de TipoActivTarifa
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de TipoActivTarifa
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
		$oDbl = $GLOBALS['oDBC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xa_tipo_activ_tarifa');
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
		$aDades['id_tarifa'] = $this->iid_tarifa;
		$aDades['id_tipo_activ'] = $this->iid_tipo_activ;
		$aDades['temporada'] = $this->itemporada;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_tarifa                = :id_tarifa,
					id_tipo_activ            = :id_tipo_activ,
					temporada                = :temporada";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === false) {
				$sClauError = 'TipoActivTarifa.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoActivTarifa.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(id_tarifa,id_tipo_activ,temporada)";
			$valores="(:id_tarifa,:id_tipo_activ,:temporada)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'TipoActivTarifa.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'TipoActivTarifa.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId('xa_tipo_activ_tarifa_id_item_seq');
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
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
				$sClauError = 'TipoActivTarifa.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === false) {
			$sClauError = 'TipoActivTarifa.eliminar';
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_tarifa',$aDades)) $this->setId_tarifa($aDades['id_tarifa']);
		if (array_key_exists('id_tipo_activ',$aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
		if (array_key_exists('temporada',$aDades)) $this->setTemporada($aDades['temporada']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setId_tarifa('');
		$this->setId_tipo_activ('');
		$this->setTemporada('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de TipoActivTarifa en un array
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
	 * Recupera las claus primàries de TipoActivTarifa en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_schema de TipoActivTarifa
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
	 * estableix el valor de l'atribut iid_schema de TipoActivTarifa
	 *
	 * @param integer iid_schema='' optional
	 */
	function setId_schema($iid_schema='') {
		$this->iid_schema = $iid_schema;
	}
	/**
	 * Recupera l'atribut iid_item de TipoActivTarifa
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
	 * estableix el valor de l'atribut iid_item de TipoActivTarifa
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_tarifa de TipoActivTarifa
	 *
	 * @return integer iid_tarifa
	 */
	function getId_tarifa() {
		if (!isset($this->iid_tarifa)) {
			$this->DBCarregar();
		}
		return $this->iid_tarifa;
	}
	/**
	 * estableix el valor de l'atribut iid_tarifa de TipoActivTarifa
	 *
	 * @param integer iid_tarifa='' optional
	 */
	function setId_tarifa($iid_tarifa='') {
		$this->iid_tarifa = $iid_tarifa;
	}
	/**
	 * Recupera l'atribut iid_tipo_activ de TipoActivTarifa
	 *
	 * @return integer iid_tipo_activ
	 */
	function getId_tipo_activ() {
		if (!isset($this->iid_tipo_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_activ de TipoActivTarifa
	 *
	 * @param integer iid_tipo_activ='' optional
	 */
	function setId_tipo_activ($iid_tipo_activ='') {
		$this->iid_tipo_activ = $iid_tipo_activ;
	}
	/**
	 * Recupera l'atribut itemporada de TipoActivTarifa
	 *
	 * @return integer itemporada
	 */
	function getTemporada() {
		if (!isset($this->itemporada)) {
			$this->DBCarregar();
		}
		return $this->itemporada;
	}
	/**
	 * estableix el valor de l'atribut itemporada de TipoActivTarifa
	 *
	 * @param integer itemporada='' optional
	 */
	function setTemporada($itemporada='') {
		$this->itemporada = $itemporada;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oTipoActivTarifaSet = new core\Set();

		$oTipoActivTarifaSet->add($this->getDatosId_schema());
		$oTipoActivTarifaSet->add($this->getDatosTarifa());
		$oTipoActivTarifaSet->add($this->getDatosId_tipo_activ());
		$oTipoActivTarifaSet->add($this->getDatosTemporada());
		return $oTipoActivTarifaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema de TipoActivTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_schema() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_schema'));
		$oDatosCampo->setEtiqueta(_("id_schema"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tarifa de TipoActivTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tarifa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tarifa'));
		$oDatosCampo->setEtiqueta(_("tarifa"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tipo_activ de TipoActivTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_activ'));
		$oDatosCampo->setEtiqueta(_("id_tipo_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itemporada de TipoActivTarifa
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTemporada() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'temporada'));
		$oDatosCampo->setEtiqueta(_("temporada"));
		$oDatosCampo->setTipo('array');
		$oDatosCampo->setArgument(array( "A"=> _("t.alta"), "B"=> _("t.baja")));
		return $oDatosCampo;
	}
}
