<?php
namespace cambios\model\entity;
use function core\is_true;
use core;
use ubis\model\entity\Ubi;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_usuario_propiedades_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
/**
 * Classe que implementa l'entitat av_cambios_usuario_propiedades_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class CambioUsuarioPropiedadPref Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de CambioUsuarioPropiedadPref
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de CambioUsuarioPropiedadPref
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
	 * Id_item de CambioUsuarioPropiedadPref
	 *
	 * @var integer
	 */
     private $iid_item;
	/**
	 * Id_item_usuario_objeto de CambioUsuarioPropiedadPref
	 *
	 * @var integer
	 */
	 private $iid_item_usuario_objeto;
	/**
	 * Propiedad de CambioUsuarioPropiedadPref
	 *
	 * @var string
	 */
	 private $spropiedad;
	/**
	 * Operador de CambioUsuarioPropiedadPref
	 *
	 * @var string
	 */
	 private $soperador;
	/**
	 * Valor de CambioUsuarioPropiedadPref
	 *
	 * @var string
	 */
	 private $svalor;
	/**
	 * Valor_old de CambioUsuarioPropiedadPref
	 *
	 * @var boolean
	 */
	 private $bvalor_old;
	/**
	 * Valor_new de CambioUsuarioPropiedadPref
	 *
	 * @var boolean
	 */
	 private $bvalor_new;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de CambioUsuarioPropiedadPref
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de CambioUsuarioPropiedadPref
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
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_cambios_usuario_propiedades_pref');
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
		if ($this->DBCarregar('guardar') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=array();
		$aDades['id_item_usuario_objeto'] = $this->iid_item_usuario_objeto;
		$aDades['propiedad'] = $this->spropiedad;
		$aDades['operador'] = $this->soperador;
		$aDades['valor'] = $this->svalor;
		$aDades['valor_old'] = $this->bvalor_old;
		$aDades['valor_new'] = $this->bvalor_new;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['valor_old']) ) { $aDades['valor_old']='true'; } else { $aDades['valor_old']='false'; }
		if ( core\is_true($aDades['valor_new']) ) { $aDades['valor_new']='true'; } else { $aDades['valor_new']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_item_usuario_objeto   = :id_item_usuario_objeto,
					propiedad                = :propiedad,
					operador                 = :operador,
					valor                    = :valor,
					valor_old                = :valor_old,
					valor_new                = :valor_new";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'CambioUsuarioPropiedadPref.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'CambioUsuarioPropiedadPref.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_item_usuario_objeto,propiedad,operador,valor,valor_old,valor_new)";
			$valores="(:id_item_usuario_objeto,:propiedad,:operador,:valor,:valor_old,:valor_new)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CambioUsuarioPropiedadPref.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'CambioUsuarioPropiedadPref.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('av_cambios_usuario_propiedades_pref_id_item_seq');
		}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'CambioUsuarioPropiedadPref.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Para evitar posteriores cargas
			$this->bLoaded = TRUE;
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$oDblSt->rowCount()) return FALSE;
					break;
				default:
					// En el caso de no existir esta fila, $aDades = FALSE:
					if ($aDades === FALSE) {
						$this->setNullAllAtributes();
					} else {
						$this->setAllAtributes($aDades);
					}
			}
			return TRUE;
		} else {
		   	return FALSE;
		}
	}

	/**
	 * Elimina el registre de la base de dades corresponent a l'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
			$sClauError = 'CambioUsuarioPropiedadPref.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	
	/**
	 * Retorna un texte per indicar el canvi que es fará.
	 *
	 * @return string sCondicio
	 */
	function getTextCambio() {
	    if ( !is_true($this->getValor_new()) && !is_true($this->getValor_old()) ) return FALSE;
	    $sText = _("si el");
	    $sText .= ' ';
	    if ( is_true($this->getValor_new()) ) $sText .= _("nuevo valor");
	    if ( is_true($this->getValor_new()) && is_true($this->getValor_old()) ) $sText .= ' '._("o el").' ';
	    if ( is_true($this->getValor_old()) ) $sText .= _("valor actual");
	    $sText .= ' ';
	    $sText .= _("es");
	    if ($this->getOperador() == '=' ) $sText .= ' = ' . _("a");
	    if ($this->getOperador() == '>' ) $sText .= ' > ' . _("que");
	    if ($this->getOperador() == '<' ) $sText .= ' < ' . _("que");
	    if ($this->getOperador() == 'regexp' ) $sText .= ' regexp ' . _("a");
	    
	    //$sText .= ' '.$this->getValor();
	    switch ($this->getPropiedad()) {
	        case 'id_ubi':
	            $aId_ubis = explode(',',$this->getValor());
	            $sValor = '';
	            $i = 0;
	            foreach ($aId_ubis as $id_ubi) {
	                $i++;
	                $oUbi = Ubi::NewUbi($id_ubi);
	                if ($i > 1) $sValor .= ' o ';
	                $sValor .= $oUbi->getNombre_ubi();
	            }
	            break;
	        default:
	            $sValor = $this->getValor();
	    }
	    $sText .= ' '.$sValor;
	    return $sText;
	}
	
	
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_item_usuario_objeto',$aDades)) $this->setId_item_usuario_objeto($aDades['id_item_usuario_objeto']);
		if (array_key_exists('propiedad',$aDades)) $this->setPropiedad($aDades['propiedad']);
		if (array_key_exists('operador',$aDades)) $this->setOperador($aDades['operador']);
		if (array_key_exists('valor',$aDades)) $this->setValor($aDades['valor']);
		if (array_key_exists('valor_old',$aDades)) $this->setValor_old($aDades['valor_old']);
		if (array_key_exists('valor_new',$aDades)) $this->setValor_new($aDades['valor_new']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_item('');
		$this->setId_item_usuario_objeto('');
		$this->setPropiedad('');
		$this->setOperador('');
		$this->setValor('');
		$this->setValor_old('');
		$this->setValor_new('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de CambioUsuarioPropiedadPref en un array
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
	 * Recupera las claus primàries de CambioUsuarioPropiedadPref en un array
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
	 * Estableix las claus primàries de CambioUsuarioPropiedadPref en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_item de CambioUsuarioPropiedadPref
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de CambioUsuarioPropiedadPref
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_item_usuario_objeto de CambioUsuarioPropiedadPref
	 *
	 * @return integer iid_item_usuario_objeto
	 */
	function getId_item_usuario_objeto() {
		if (!isset($this->iid_item_usuario_objeto) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_item_usuario_objeto;
	}
	/**
	 * estableix el valor de l'atribut iid_item_usuario_objeto de CambioUsuarioPropiedadPref
	 *
	 * @param integer iid_item_usuario_objeto='' optional
	 */
	function setId_item_usuario_objeto($iid_item_usuario_objeto='') {
		$this->iid_item_usuario_objeto = $iid_item_usuario_objeto;
	}
	/**
	 * Recupera l'atribut spropiedad de CambioUsuarioPropiedadPref
	 *
	 * @return string spropiedad
	 */
	function getPropiedad() {
		if (!isset($this->spropiedad) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->spropiedad;
	}
	/**
	 * estableix el valor de l'atribut spropiedad de CambioUsuarioPropiedadPref
	 *
	 * @param string spropiedad='' optional
	 */
	function setPropiedad($spropiedad='') {
		$this->spropiedad = $spropiedad;
	}
	/**
	 * Recupera l'atribut soperador de CambioUsuarioPropiedadPref
	 *
	 * @return string soperador
	 */
	function getOperador() {
		if (!isset($this->soperador) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->soperador;
	}
	/**
	 * estableix el valor de l'atribut soperador de CambioUsuarioPropiedadPref
	 *
	 * @param string soperador='' optional
	 */
	function setOperador($soperador='') {
		$this->soperador = $soperador;
	}
	/**
	 * Recupera l'atribut svalor de CambioUsuarioPropiedadPref
	 *
	 * @return string svalor
	 */
	function getValor() {
		if (!isset($this->svalor) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->svalor;
	}
	/**
	 * estableix el valor de l'atribut svalor de CambioUsuarioPropiedadPref
	 *
	 * @param string svalor='' optional
	 */
	function setValor($svalor='') {
		$this->svalor = $svalor;
	}
	/**
	 * Recupera l'atribut bvalor_old de CambioUsuarioPropiedadPref
	 *
	 * @return boolean bvalor_old
	 */
	function getValor_old() {
		if (!isset($this->bvalor_old) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bvalor_old;
	}
	/**
	 * estableix el valor de l'atribut bvalor_old de CambioUsuarioPropiedadPref
	 *
	 * @param boolean bvalor_old='f' optional
	 */
	function setValor_old($bvalor_old='f') {
		$this->bvalor_old = $bvalor_old;
	}
	/**
	 * Recupera l'atribut bvalor_new de CambioUsuarioPropiedadPref
	 *
	 * @return boolean bvalor_new
	 */
	function getValor_new() {
		if (!isset($this->bvalor_new) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bvalor_new;
	}
	/**
	 * estableix el valor de l'atribut bvalor_new de CambioUsuarioPropiedadPref
	 *
	 * @param boolean bvalor_new='f' optional
	 */
	function setValor_new($bvalor_new='f') {
		$this->bvalor_new = $bvalor_new;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCambioUsuarioPropiedadPrefSet = new core\Set();

		$oCambioUsuarioPropiedadPrefSet->add($this->getDatosId_item_usuario_objeto());
		$oCambioUsuarioPropiedadPrefSet->add($this->getDatosPropiedad());
		$oCambioUsuarioPropiedadPrefSet->add($this->getDatosOperador());
		$oCambioUsuarioPropiedadPrefSet->add($this->getDatosValor());
		$oCambioUsuarioPropiedadPrefSet->add($this->getDatosValor_old());
		$oCambioUsuarioPropiedadPrefSet->add($this->getDatosValor_new());
		return $oCambioUsuarioPropiedadPrefSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_item_usuario_objeto de CambioUsuarioPropiedadPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_item_usuario_objeto() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_item_usuario_objeto'));
		$oDatosCampo->setEtiqueta(_("id_item_usuario_objeto"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spropiedad de CambioUsuarioPropiedadPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPropiedad() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'propiedad'));
		$oDatosCampo->setEtiqueta(_("propiedad"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut soperador de CambioUsuarioPropiedadPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosOperador() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'operador'));
		$oDatosCampo->setEtiqueta(_("operador"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut svalor de CambioUsuarioPropiedadPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosValor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'valor'));
		$oDatosCampo->setEtiqueta(_("valor"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bvalor_old de CambioUsuarioPropiedadPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosValor_old() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'valor_old'));
		$oDatosCampo->setEtiqueta(_("valor_old"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bvalor_new de CambioUsuarioPropiedadPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosValor_new() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'valor_new'));
		$oDatosCampo->setEtiqueta(_("valor_new"));
		return $oDatosCampo;
	}
}
