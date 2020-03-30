<?php
namespace menus\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 23/12/2013
 */
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 23/12/2013
 */
class Metamenu Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Metamenu
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Metamenu
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_metamenu de Metamenu
	 *
	 * @var integer
	 */
	 private $iid_metamenu;
	/**
	 * id_mod de Metamenu
	 *
	 * @var integer
	 */
	 private $iid_mod;
	/**
	 * Url de Metamenu
	 *
	 * @var string
	 */
	 private $surl;
	/**
	 * Parametros de Metamenu
	 *
	 * @var string
	 */
	 private $sparametros;
	/**
	 * Descripcion de Metamenu
	 *
	 * @var string
	 */
	 private $sdescripcion;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_metamenu
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_metamenu') && $val_id !== '') $this->iid_metamenu = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_metamenu = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_metamenu' => $this->iid_metamenu);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_metamenus');
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
		$aDades['id_mod'] = $this->iid_mod;
		$aDades['url'] = $this->surl;
		$aDades['parametros'] = $this->sparametros;
		$aDades['descripcion'] = $this->sdescripcion;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_mod                   = :id_mod,
					url                      = :url,
					parametros               = :parametros,
					descripcion              = :descripcion";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_metamenu='$this->iid_metamenu'")) === false) {
				$sClauError = 'Metamenu.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Metamenu.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(id_mod,url,parametros,descripcion)";
			$valores="(:id_mod,:url,:parametros,:descripcion)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Metamenu.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Metamenu.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_metamenu = $oDbl->lastInsertId('metamenus_id_metamenu_seq');
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
		if (isset($this->iid_metamenu)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_metamenu='$this->iid_metamenu'")) === false) {
				$sClauError = 'Metamenu.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_metamenu='$this->iid_metamenu'")) === false) {
			$sClauError = 'Metamenu.eliminar';
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
		if (array_key_exists('id_metamenu',$aDades)) $this->setId_metamenu($aDades['id_metamenu']);
		if (array_key_exists('id_mod',$aDades)) $this->setId_mod($aDades['id_mod']);
		if (array_key_exists('url',$aDades)) $this->setUrl($aDades['url']);
		if (array_key_exists('parametros',$aDades)) $this->setParametros($aDades['parametros']);
		if (array_key_exists('descripcion',$aDades)) $this->setDescripcion($aDades['descripcion']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setId_metamenu('');
		$this->setId_mod('');
		$this->setUrl('');
		$this->setParametros('');
		$this->setDescripcion('');
	}


	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Metamenu en un array
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
	 * Recupera las claus primàries de Metamenu en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_metamenu' => $this->iid_metamenu);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de MetaMenu en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_metamenu') && $val_id !== '') $this->iid_metamenu = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_metamenu de Metamenu
	 *
	 * @return integer iid_metamenu
	 */
	function getId_metamenu() {
		if (!isset($this->iid_metamenu)) {
			$this->DBCarregar();
		}
		return $this->iid_metamenu;
	}
	/**
	 * estableix el valor de l'atribut iid_metamenu de Metamenu
	 *
	 * @param integer iid_metamenu
	 */
	function setId_metamenu($iid_metamenu) {
		$this->iid_metamenu = $iid_metamenu;
	}
	/**
	 * Recupera l'atribut iid_mod de Metamenu
	 *
	 * @return string iid_mod
	 */
	function getId_Mod() {
		if (!isset($this->iid_mod)) {
			$this->DBCarregar();
		}
		return $this->iid_mod;
	}
	/**
	 * estableix el valor de l'atribut iid_mod de Metamenu
	 *
	 * @param string iid_mod='' optional
	 */
	function setId_Mod($iid_mod='') {
		$this->iid_mod = $iid_mod;
	}
	/**
	 * Recupera l'atribut surl de Metamenu
	 *
	 * @return string surl
	 */
	function getUrl() {
		if (!isset($this->surl)) {
			$this->DBCarregar();
		}
		return $this->surl;
	}
	/**
	 * estableix el valor de l'atribut surl de Metamenu
	 *
	 * @param string surl='' optional
	 */
	function setUrl($surl='') {
		$this->surl = $surl;
	}
	/**
	 * Recupera l'atribut sparametros de Metamenu
	 *
	 * @return string sparametros
	 */
	function getParametros() {
		if (!isset($this->sparametros)) {
			$this->DBCarregar();
		}
		return $this->sparametros;
	}
	/**
	 * estableix el valor de l'atribut sparametros de Metamenu
	 *
	 * @param string sparametros='' optional
	 */
	function setParametros($sparametros='') {
		$this->sparametros = $sparametros;
	}
	/**
	 * Recupera l'atribut sdescripcion de Metamenu
	 *
	 * @return string sdescripcion
	 */
	function getDescripcion() {
		if (!isset($this->sdescripcion)) {
			$this->DBCarregar();
		}
		return $this->sdescripcion;
	}
	/**
	 * estableix el valor de l'atribut sdescripcion de Metamenu
	 *
	 * @param string sdescripcion='' optional
	 */
	function setDescripcion($sdescripcion='') {
		$this->sdescripcion = $sdescripcion;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oMetamenuSet = new core\Set();

		$oMetamenuSet->add($this->getDatosModulo());
		$oMetamenuSet->add($this->getDatosUrl());
		$oMetamenuSet->add($this->getDatosParametros());
		$oMetamenuSet->add($this->getDatosDescripcion());
		return $oMetamenuSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_mod de Metamenu
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosModulo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_mod'));
		$oDatosCampo->setEtiqueta(_("modulo"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('devel\model\entity\Modulo'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('getNom'); // método para obtener el valor a mostrar del objeto relacionado.
		$oDatosCampo->setArgument3('getListaModulos'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut surl de Metamenu
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosUrl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'url'));
		$oDatosCampo->setEtiqueta(_("url"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(60);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sparametros de Metamenu
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosParametros() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'parametros'));
		$oDatosCampo->setEtiqueta(_("parametros"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdescripcion de Metamenu
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDescripcion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'descripcion'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(50);
		return $oDatosCampo;
	}
}
?>
