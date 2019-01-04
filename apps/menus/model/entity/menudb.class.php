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
 * @created 15/01/2014
 */
/**
 * Classe que implementa l'entitat $nom_tabla
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 15/01/2014
 */
class MenuDb Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de MenuDb
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de MenuDb
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_menu de MenuDb
	 *
	 * @var integer
	 */
	 private $iid_menu;
	/**
	 * Orden de MenuDb
	 *
	 * @var integer
	 */
	 private $iorden;
	/**
	 * Menu de MenuDb
	 *
	 * @var string
	 */
	 private $smenu;
	/**
	 * Parametros de MenuDb
	 *
	 * @var string
	 */
	 private $sparametros;
	/**
	 * Id_metamenu de MenuDb
	 *
	 * @var integer
	 */
	 private $iid_metamenu;
	/**
	 * Menu_perm de MenuDb
	 *
	 * @var integer
	 */
	 private $imenu_perm;
	/**
	 * Id_grupmenu de MenuDb
	 *
	 * @var integer
	 */
	 private $iid_grupmenu;
	/**
	 * Ok de MenuDb
	 *
	 * @var boolean
	 */
	 private $bok;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_menu
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_menu') && $val_id !== '') $this->iid_menu = (int)$val_id; // evitem SQL injection fent cast a integer
			}	} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_menu = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_menu' => $this->iid_menu);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('aux_menus');
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
		$aDades['menu'] = $this->smenu;
		$aDades['parametros'] = $this->sparametros;
		$aDades['id_metamenu'] = $this->iid_metamenu;
		$aDades['menu_perm'] = $this->imenu_perm;
		$aDades['id_grupmenu'] = $this->iid_grupmenu;
		$aDades['ok'] = $this->bok;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( filter_var( $aDades['ok'], FILTER_VALIDATE_BOOLEAN)) { $aDades['ok']='t'; } else { $aDades['ok']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					orden                    = :orden,
					menu                     = :menu,
					parametros               = :parametros,
					id_metamenu              = :id_metamenu,
					menu_perm                = :menu_perm,
					id_grupmenu              = :id_grupmenu,
					ok 			             = :ok";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_menu='$this->iid_menu'")) === false) {
				$sClauError = 'MenuDb.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'MenuDb.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(orden,menu,parametros,id_metamenu,menu_perm,id_grupmenu,ok)";
			$valores="(:orden,:menu,:parametros,:id_metamenu,:menu_perm,:id_grupmenu,:ok)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'MenuDb.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'MenuDb.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_menu = $oDbl->lastInsertId($nom_tabla.'_id_menu_seq');
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
		if (isset($this->iid_menu)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_menu='$this->iid_menu'")) === false) {
				$sClauError = 'MenuDb.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_menu='$this->iid_menu'")) === false) {
			$sClauError = 'MenuDb.eliminar';
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
		if (array_key_exists('id_menu',$aDades)) $this->setId_menu($aDades['id_menu']);
		if (array_key_exists('orden',$aDades)) $this->setOrden($aDades['orden']);
		if (array_key_exists('menu',$aDades)) $this->setMenu($aDades['menu']);
		if (array_key_exists('parametros',$aDades)) $this->setParametros($aDades['parametros']);
		if (array_key_exists('id_metamenu',$aDades)) $this->setId_metamenu($aDades['id_metamenu']);
		if (array_key_exists('menu_perm',$aDades)) $this->setMenu_perm($aDades['menu_perm']);
		if (array_key_exists('id_grupmenu',$aDades)) $this->setId_grupmenu($aDades['id_grupmenu']);
		if (array_key_exists('ok',$aDades)) $this->setOk($aDades['ok']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de MenuDb en un array
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
	 * Recupera las claus primàries de MenuDb en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_menu' => $this->iid_menu);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_menu de MenuDb
	 *
	 * @return integer iid_menu
	 */
	function getId_menu() {
		if (!isset($this->iid_menu)) {
			$this->DBCarregar();
		}
		return $this->iid_menu;
	}
	/**
	 * estableix el valor de l'atribut iid_menu de MenuDb
	 *
	 * @param integer iid_menu
	 */
	function setId_menu($iid_menu) {
		$this->iid_menu = $iid_menu;
	}
	/**
	 * Recupera l'atribut iorden de MenuDb
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
	 * estableix el valor de l'atribut iorden de MenuDb
	 *
	 * @param integer iorden='' optional
	 */
	function setOrden($iorden='') {
		$this->iorden = $iorden;
	}
	/**
	 * Recupera l'atribut smenu de MenuDb
	 *
	 * @return string smenu
	 */
	function getMenu() {
		if (!isset($this->smenu)) {
			$this->DBCarregar();
		}
		return $this->smenu;
	}
	/**
	 * estableix el valor de l'atribut smenu de MenuDb
	 *
	 * @param string smenu='' optional
	 */
	function setMenu($smenu='') {
		$this->smenu = $smenu;
	}
	/**
	 * Recupera l'atribut sparametros de MenuDb
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
	 * estableix el valor de l'atribut sparametros de MenuDb
	 *
	 * @param string sparametros='' optional
	 */
	function setParametros($sparametros='') {
		$this->sparametros = $sparametros;
	}
	/**
	 * Recupera l'atribut iid_metamenu de MenuDb
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
	 * estableix el valor de l'atribut iid_metamenu de MenuDb
	 *
	 * @param integer iid_metamenu='' optional
	 */
	function setId_metamenu($iid_metamenu='') {
		$this->iid_metamenu = $iid_metamenu;
	}
	/**
	 * Recupera l'atribut imenu_perm de MenuDb
	 *
	 * @return integer imenu_perm
	 */
	function getMenu_perm() {
		if (!isset($this->imenu_perm)) {
			$this->DBCarregar();
		}
		return $this->imenu_perm;
	}
	/**
	 * estableix el valor de l'atribut imenu_perm de MenuDb
	 *
	 * @param integer imenu_perm='' optional
	 */
	function setMenu_perm($imenu_perm='') {
		$this->imenu_perm = $imenu_perm;
	}
	/**
	 * Recupera l'atribut iid_grupmenu de MenuDb
	 *
	 * @return integer iid_grupmenu
	 */
	function getId_grupmenu() {
		if (!isset($this->iid_grupmenu)) {
			$this->DBCarregar();
		}
		return $this->iid_grupmenu;
	}
	/**
	 * estableix el valor de l'atribut iid_grupmenu de MenuDb
	 *
	 * @param integer iid_grupmenu='' optional
	 */
	function setId_grupmenu($iid_grupmenu='') {
		$this->iid_grupmenu = $iid_grupmenu;
	}
	/**
	 * Recupera l'atribut bok de MenuDb
	 *
	 * @return boolean bok
	 */
	function getOk() {
		if (!isset($this->bok)) {
			$this->DBCarregar();
		}
		return $this->bok;
	}
	/**
	 * estableix el valor de l'atribut bok de MenuDb
	 *
	 * @param boolean bok='f' optional
	 */
	function setOk($bok='f') {
		$this->bok = $bok;
	}

	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oMenuDbSet = new core\Set();

		$oMenuDbSet->add($this->getDatosOrden());
		$oMenuDbSet->add($this->getDatosMenu());
		$oMenuDbSet->add($this->getDatosParametros());
		$oMenuDbSet->add($this->getDatosId_metamenu());
		$oMenuDbSet->add($this->getDatosMenu_perm());
		$oMenuDbSet->add($this->getDatosId_grupmenu());
		return $oMenuDbSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iorden de MenuDb
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
	 * Recupera les propietats de l'atribut smenu de MenuDb
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosMenu() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'menu'));
		$oDatosCampo->setEtiqueta(_("menú"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sparametros de MenuDb
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosParametros() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'parametros'));
		$oDatosCampo->setEtiqueta(_("parametros"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_metamenu de MenuDb
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_metamenu() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_metamenu'));
		$oDatosCampo->setEtiqueta(_("id_metamenu"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut imenu_perm de MenuDb
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosMenu_perm() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'menu_perm'));
		$oDatosCampo->setEtiqueta(_("menu_perm"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_grupmenu de MenuDb
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_grupmenu() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_grupmenu'));
		$oDatosCampo->setEtiqueta(_("id_grupmenu"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bok de MenuDb
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosOk() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'ok'));
		$oDatosCampo->setEtiqueta(_("ok"));
		return $oDatosCampo;
	}

}
?>
