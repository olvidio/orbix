<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat xd_desc_teleco
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class DescTeleco Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de DescTeleco
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de DescTeleco
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de DescTeleco
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Orden de DescTeleco
	 *
	 * @var integer
	 */
	 private $iorden;
	/**
	 * Tipo_teleco de DescTeleco
	 *
	 * @var string
	 */
	 private $stipo_teleco;
	/**
	 * Desc_teleco de DescTeleco
	 *
	 * @var string
	 */
	 private $sdesc_teleco;
	/**
	 * Ubi de DescTeleco
	 *
	 * @var boolean
	 */
	 private $bubi;
	/**
	 * Persona de DescTeleco
	 *
	 * @var boolean
	 */
	 private $bpersona;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
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
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xd_desc_teleco');
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
		$aDades['orden'] = $this->iorden;
		$aDades['tipo_teleco'] = $this->stipo_teleco;
		$aDades['desc_teleco'] = $this->sdesc_teleco;
		$aDades['ubi'] = $this->bubi;
		$aDades['persona'] = $this->bpersona;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['ubi'] = ($aDades['ubi'] === 't')? 'true' : $aDades['ubi'];
		if ( filter_var( $aDades['ubi'], FILTER_VALIDATE_BOOLEAN)) { $aDades['ubi']='t'; } else { $aDades['ubi']='f'; }
		$aDades['persona'] = ($aDades['persona'] === 't')? 'true' : $aDades['persona'];
		if ( filter_var( $aDades['persona'], FILTER_VALIDATE_BOOLEAN)) { $aDades['persona']='t'; } else { $aDades['persona']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					orden                    = :orden,
					tipo_teleco              = :tipo_teleco,
					desc_teleco              = :desc_teleco,
					ubi                      = :ubi,
					persona                  = :persona";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'DescTeleco.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'DescTeleco.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_item);
			$campos="(id_item,orden,tipo_teleco,desc_teleco,ubi,persona)";
			$valores="(:id_item,:orden,:tipo_teleco,:desc_teleco,:ubi,:persona)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'DescTeleco.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'DescTeleco.insertar.execute';
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
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'DescTeleco.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
			$sClauError = 'DescTeleco.eliminar';
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
		if (array_key_exists('orden',$aDades)) $this->setOrden($aDades['orden']);
		if (array_key_exists('tipo_teleco',$aDades)) $this->setTipo_teleco($aDades['tipo_teleco']);
		if (array_key_exists('desc_teleco',$aDades)) $this->setDesc_teleco($aDades['desc_teleco']);
		if (array_key_exists('ubi',$aDades)) $this->setUbi($aDades['ubi']);
		if (array_key_exists('persona',$aDades)) $this->setPersona($aDades['persona']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setOrden('');
		$this->setTipo_teleco('');
		$this->setDesc_teleco('');
		$this->setUbi('');
		$this->setPersona('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de DescTeleco en un array
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
	 * Recupera las claus primàries de DescTeleco en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de DescTeleco
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
	 * estableix el valor de l'atribut iid_item de DescTeleco
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iorden de DescTeleco
	 *
	 * @return integer iorden
	 */
	function getOrden() {
		if (!isset($this->iorden)) {
			$this->DBCarregar();
		}
		return $this->iorden;
	}
	/**
	 * estableix el valor de l'atribut iorden de DescTeleco
	 *
	 * @param integer iorden='' optional
	 */
	function setOrden($iorden='') {
		$this->iorden = $iorden;
	}
	/**
	 * Recupera l'atribut stipo_teleco de DescTeleco
	 *
	 * @return string stipo_teleco
	 */
	function getTipo_teleco() {
		if (!isset($this->stipo_teleco)) {
			$this->DBCarregar();
		}
		return $this->stipo_teleco;
	}
	/**
	 * estableix el valor de l'atribut stipo_teleco de DescTeleco
	 *
	 * @param string stipo_teleco='' optional
	 */
	function setTipo_teleco($stipo_teleco='') {
		$this->stipo_teleco = $stipo_teleco;
	}
	/**
	 * Recupera l'atribut sdesc_teleco de DescTeleco
	 *
	 * @return string sdesc_teleco
	 */
	function getDesc_teleco() {
		if (!isset($this->sdesc_teleco)) {
			$this->DBCarregar();
		}
		return $this->sdesc_teleco;
	}
	/**
	 * estableix el valor de l'atribut sdesc_teleco de DescTeleco
	 *
	 * @param string sdesc_teleco='' optional
	 */
	function setDesc_teleco($sdesc_teleco='') {
		$this->sdesc_teleco = $sdesc_teleco;
	}
	/**
	 * Recupera l'atribut bubi de DescTeleco
	 *
	 * @return boolean bubi
	 */
	function getUbi() {
		if (!isset($this->bubi)) {
			$this->DBCarregar();
		}
		return $this->bubi;
	}
	/**
	 * estableix el valor de l'atribut bubi de DescTeleco
	 *
	 * @param boolean bubi='f' optional
	 */
	function setUbi($bubi='f') {
		$this->bubi = $bubi;
	}
	/**
	 * Recupera l'atribut bpersona de DescTeleco
	 *
	 * @return boolean bpersona
	 */
	function getPersona() {
		if (!isset($this->bpersona)) {
			$this->DBCarregar();
		}
		return $this->bpersona;
	}
	/**
	 * estableix el valor de l'atribut bpersona de DescTeleco
	 *
	 * @param boolean bpersona='f' optional
	 */
	function setPersona($bpersona='f') {
		$this->bpersona = $bpersona;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDescTelecoSet = new core\Set();

		$oDescTelecoSet->add($this->getDatosOrden());
		$oDescTelecoSet->add($this->getDatosTipo_teleco());
		$oDescTelecoSet->add($this->getDatosDesc_teleco());
		$oDescTelecoSet->add($this->getDatosUbi());
		$oDescTelecoSet->add($this->getDatosPersona());
		return $oDescTelecoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iorden de DescTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosOrden() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'orden'));
		$oDatosCampo->setEtiqueta(_("orden"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo_teleco de DescTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_teleco'));
		$oDatosCampo->setEtiqueta(_("tipo de teleco"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdesc_teleco de DescTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDesc_teleco() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_teleco'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bubi de DescTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosUbi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ubi'));
		$oDatosCampo->setEtiqueta(_("ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpersona de DescTeleco
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPersona() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'persona'));
		$oDatosCampo->setEtiqueta(_("persona"));
		return $oDatosCampo;
	}
}
?>
