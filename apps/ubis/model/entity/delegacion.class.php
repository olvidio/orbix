<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat xu_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 20/11/2010
 */
class Delegacion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Delegacion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Delegacion
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
	 * Id_dl de Delegacion
	 *
	 * @var integer
	 */
	 private $iid_dl;
	/**
	 * Dl de Delegacion
	 *
	 * @var string
	 */
	 private $sdl;
	/**
	 * Region de Delegacion
	 *
	 * @var string
	 */
	 private $sregion;
	/**
	 * Nombre_dl de Delegacion
	 *
	 * @var string
	 */
	 private $snombre_dl;
	/**
	 * grupo_estudios de Delegacion
	 *
	 * @var string
	 */
	 private $sgrupo_estudios;
	/**
	 * region_stgr de Delegacion
	 *
	 * @var string
	 */
	 private $sregion_stgr;
	/**
	 * Status de Delegacion
	 *
	 * @var boolean
	 */
	 private $bstatus;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array sdl,sregion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBPC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id === 'dl') && $val_id !== '') $this->sdl = (string)$val_id; // evitem SQL injection fent cast a string
				if (($nom_id === 'region') && $val_id !== '') $this->sregion = (string)$val_id; // evitem SQL injection fent cast a string
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('xu_dl');
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
		$aDades['id_dl'] = $this->iid_dl;
		$aDades['dl'] = $this->sdl;
		$aDades['region'] = $this->sregion;
		$aDades['nombre_dl'] = $this->snombre_dl;
		$aDades['grupo_estudios'] = $this->sgrupo_estudios;
		$aDades['region_stgr'] = $this->sregion_stgr;
		$aDades['status'] = $this->bstatus;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['status']) ) { $aDades['status']='true'; } else { $aDades['status']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_dl                    = :id_dl,
					dl                    	 = :dl,
					region                   = :region,
					nombre_dl                = :nombre_dl,
					grupo_estudios           = :grupo_estudios,
					region_stgr              = :region_stgr,
					status                   = :status";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE dl='$this->sdl' AND region='$this->sregion'")) === false) {
				$sClauError = 'Delegacion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Delegacion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->sdl, $this->sregion);
			$campos="(id_dl,dl,region,nombre_dl,status,grupo_estudios,region_stgr)";
			$valores="(:id_dl,:dl,:region,:nombre_dl,:status,:grupo_estudios,:region_stgr)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Delegacion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Delegacion.insertar.execute';
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
		if (isset($this->sdl) && isset($this->sregion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE dl='$this->sdl' AND region='$this->sregion'")) === false) {
				$sClauError = 'Delegacion.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
			// Para evitar posteriores cargas
			$this->bLoaded = TRUE;
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE dl='$this->sdl' AND region='$this->sregion'")) === false) {
			$sClauError = 'Delegacion.eliminar';
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
		if (array_key_exists('id_dl',$aDades)) $this->setId_dl($aDades['id_dl']);
		if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
		if (array_key_exists('region',$aDades)) $this->setRegion($aDades['region']);
		if (array_key_exists('nombre_dl',$aDades)) $this->setNombre_dl($aDades['nombre_dl']);
		if (array_key_exists('grupo_estudios',$aDades)) $this->setGrupo_estudios($aDades['grupo_estudios']);
		if (array_key_exists('region_stgr',$aDades)) $this->setRegion_stgr($aDades['region_stgr']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_dl('');
		$this->setDl('');
		$this->setRegion('');
		$this->setNombre_dl('');
		$this->setGrupo_estudios('');
		$this->setRegion_stgr('');
		$this->setStatus('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera tots els atributs de Delegacion en un array
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
	 * Recupera las claus primàries de Delegacion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('dl' => $this->sdl,'region' => $this->sregion);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Delegacion en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'dl') && $val_id !== '') $this->sdl = $val_id;
	            if (($nom_id == 'region') && $val_id !== '') $this->sregion = $val_id;
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_dl de Delegacion
	 *
	 * @return integer iid_dl
	 */
	function getId_dl() {
		if (!isset($this->iid_dl) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_dl;
	}
	/**
	 * estableix el valor de l'atribut iid_dl de Delegacion
	 *
	 * @param integer iid_dl='' optional
	 */
	function setId_dl($iid_dl='') {
		$this->iid_dl = $iid_dl;
	}
	/**
	 * Recupera l'atribut sdl de Delegacion
	 *
	 * @return string sdl
	 */
	function getDl() {
		if (!isset($this->sdl) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sdl;
	}
	/**
	 * estableix el valor de l'atribut sdl de Delegacion
	 *
	 * @param string sdl
	 */
	function setDl($sdl) {
		$this->sdl = $sdl;
	}
	/**
	 * Recupera l'atribut sregion de Delegacion
	 *
	 * @return string sregion
	 */
	function getRegion() {
		if (!isset($this->sregion) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sregion;
	}
	/**
	 * estableix el valor de l'atribut sregion de Delegacion
	 *
	 * @param string sregion
	 */
	function setRegion($sregion) {
		$this->sregion = $sregion;
	}
	/**
	 * Recupera l'atribut snombre_dl de Delegacion
	 *
	 * @return string snombre_dl
	 */
	function getNombre_dl() {
		if (!isset($this->snombre_dl) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->snombre_dl;
	}
	/**
	 * estableix el valor de l'atribut snombre_dl de Delegacion
	 *
	 * @param string snombre_dl='' optional
	 */
	function setNombre_dl($snombre_dl='') {
		$this->snombre_dl = $snombre_dl;
	}
	/**
	 * Recupera l'atribut sgrupo_estudios de Delegacion
	 *
	 * @return string sgrupo_estudios
	 */
	function getGrupo_estudios() {
		if (!isset($this->sgrupo_estudios) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sgrupo_estudios;
	}
	/**
	 * estableix el valor de l'atribut sgrupo_estudios de Delegacion
	 *
	 * @param string sgrupo_estudios='' optional
	 */
	function setGrupo_estudios($sgrupo_estudios='') {
		$this->sgrupo_estudios = $sgrupo_estudios;
	}
	/**
	 * Recupera l'atribut sregion_stgr de Delegacion
	 *
	 * @return string sregion_stgr
	 */
	function getRegion_stgr() {
		if (!isset($this->sregion_stgr) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sregion_stgr;
	}
	/**
	 * estableix el valor de l'atribut sregion_stgr de Delegacion
	 *
	 * @param string sregion_stgr='' optional
	 */
	function setRegion_stgr($region_stgr='') {
		$this->sregion_stgr = $region_stgr;
	}
	/**
	 * Recupera l'atribut bstatus de Delegacion
	 *
	 * @return boolean bstatus
	 */
	function getStatus() {
		if (!isset($this->bstatus) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bstatus;
	}
	/**
	 * estableix el valor de l'atribut bstatus de Delegacion
	 *
	 * @param boolean bstatus='f' optional
	 */
	function setStatus($bstatus='f') {
		$this->bstatus = $bstatus;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDelegacionSet = new core\Set();

		$oDelegacionSet->add($this->getDatosId_dl());
		$oDelegacionSet->add($this->getDatosRegion());
		$oDelegacionSet->add($this->getDatosDl());
		$oDelegacionSet->add($this->getDatosNombre_dl());
		$oDelegacionSet->add($this->getDatosGrupo_estudios());
		$oDelegacionSet->add($this->getDatosRegion_stgr());
		$oDelegacionSet->add($this->getDatosStatus());
		return $oDelegacionSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_dl de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_dl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_dl'));
		$oDatosCampo->setEtiqueta(_("id_dl"));
		$oDatosCampo->setTipo('texto');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl'));
		$oDatosCampo->setEtiqueta(_("sigla"));
		$oDatosCampo->setTipo('texto');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snombre_dl de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNombre_dl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nombre_dl'));
		$oDatosCampo->setEtiqueta(_("nombre de la delegación"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sgrupo_estudios de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosGrupo_estudios() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'grupo_estudios'));
		$oDatosCampo->setEtiqueta(_("grupo del stgr"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(3);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sregion de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosRegion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'region'));
		$oDatosCampo->setEtiqueta(_("nombre de la región"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('ubis\model\entity\Region');
		$oDatosCampo->setArgument2('getRegion'); // método para obtener el valor a mostrar del objeto relacionado.
		$oDatosCampo->setArgument3('getListaRegiones');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sregion_stgr de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosRegion_stgr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'region_stgr'));
		$oDatosCampo->setEtiqueta(_("región del stgr"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(3);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bstatus de Delegacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosStatus() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status'));
		$oDatosCampo->setEtiqueta(_("en activo"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}
?>
