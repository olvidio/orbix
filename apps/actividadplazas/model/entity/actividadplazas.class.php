<?php
namespace actividadplazas\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula da_plazas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2016
 */
/**
 * Classe que implementa l'entitat da_plazas
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 09/11/2016
 */
class actividadPlazas Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de actividadPlazas
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de actividadPlazas
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_schema de actividadPlazas
	 *
	 * @var integer
	 */
	 protected $iid_schema;
	/**
	 * Id_activ de actividadPlazas
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	/**
	 * Id_dl de actividadPlazas
	 *
	 * @var integer
	 */
	 protected $iid_dl;
	/**
	 * Plazas de actividadPlazas
	 *
	 * @var integer
	 */
	 protected $iplazas;
	/**
	 * Cl de actividadPlazas
	 *
	 * @var string
	 */
	 protected $scl;
	/**
	 * Dl_tabla de actividadPlazas
	 *
	 * @var string
	 */
	 protected $sdl_tabla;
	/**
	 * Cedidas de actividadPlazas
	 *
	 * @var object JSON
	 */
	 protected $ocedidas;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de actividadPlazas
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de actividadPlazas
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
	 * @param integer|array iid_activ,iid_dl,sdl_tabla
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_dl') && $val_id !== '') $this->iid_dl = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'dl_tabla') && $val_id !== '') $this->sdl_tabla = (string)$val_id; // evitem SQL injection fent cast a string
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('da_plazas');
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
		$aDades['plazas'] = $this->iplazas;
		$aDades['cl'] = $this->scl;
		$aDades['cedidas'] = $this->ocedidas;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					plazas                   = :plazas,
					cl                       = :cl,
					cedidas                  = :cedidas";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_dl='$this->iid_dl' AND dl_tabla='$this->sdl_tabla'")) === false) {
				$sClauError = 'actividadPlazas.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'actividadPlazas.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_activ, $this->iid_dl, $this->sdl_tabla);
			$campos="(id_activ,id_dl,dl_tabla,plazas,cl,cedidas)";
			$valores="(:id_activ,:id_dl,:dl_tabla,:plazas,:cl,:cedidas)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'actividadPlazas.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'actividadPlazas.insertar.execute';
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
		if (isset($this->iid_activ) && isset($this->iid_dl) && isset($this->sdl_tabla)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_dl='$this->iid_dl' AND dl_tabla='$this->sdl_tabla'")) === false) {
				$sClauError = 'actividadPlazas.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_dl='$this->iid_dl' AND dl_tabla='$this->sdl_tabla'")) === false) {
			$sClauError = 'actividadPlazas.eliminar';
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
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_dl',$aDades)) $this->setId_dl($aDades['id_dl']);
		if (array_key_exists('plazas',$aDades)) $this->setPlazas($aDades['plazas']);
		if (array_key_exists('cl',$aDades)) $this->setCl($aDades['cl']);
		if (array_key_exists('dl_tabla',$aDades)) $this->setDl_tabla($aDades['dl_tabla']);
		if (array_key_exists('cedidas',$aDades)) $this->setCedidas($aDades['cedidas']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_activ('');
		$this->setId_dl('');
		$this->setPlazas('');
		$this->setCl('');
		$this->setDl_tabla('');
		$this->setCedidas('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de actividadPlazas en un array
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
	 * Recupera las claus primàries de actividadPlazas en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_activ' => $this->iid_activ,'id_dl' => $this->iid_dl,'dl_tabla' => $this->sdl_tabla);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Estableix las claus primàries de actividadPlazas en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_dl') && $val_id !== '') $this->iid_dl = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'dl_tabla') && $val_id !== '') $this->sdl_tabla = $val_id;
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_activ de actividadPlazas
	 *
	 * @return integer iid_activ
	 */
	function getId_activ() {
		if (!isset($this->iid_activ)) {
			$this->DBCarregar();
		}
		return $this->iid_activ;
	}
	/**
	 * estableix el valor de l'atribut iid_activ de actividadPlazas
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_dl de actividadPlazas
	 *
	 * @return integer iid_dl
	 */
	function getId_dl() {
		if (!isset($this->iid_dl)) {
			$this->DBCarregar();
		}
		return $this->iid_dl;
	}
	/**
	 * estableix el valor de l'atribut iid_dl de actividadPlazas
	 *
	 * @param integer iid_dl
	 */
	function setId_dl($iid_dl) {
		$this->iid_dl = $iid_dl;
	}
	/**
	 * Recupera l'atribut iplazas de actividadPlazas
	 *
	 * @return integer iplazas
	 */
	function getPlazas() {
		if (!isset($this->iplazas)) {
			$this->DBCarregar();
		}
		return $this->iplazas;
	}
	/**
	 * estableix el valor de l'atribut iplazas de actividadPlazas
	 *
	 * @param integer iplazas='' optional
	 */
	function setPlazas($iplazas='') {
		$this->iplazas = $iplazas;
	}
	/**
	 * Recupera l'atribut scl de actividadPlazas
	 *
	 * @return string scl
	 */
	function getCl() {
		if (!isset($this->scl)) {
			$this->DBCarregar();
		}
		return $this->scl;
	}
	/**
	 * estableix el valor de l'atribut scl de actividadPlazas
	 *
	 * @param string scl='' optional
	 */
	function setCl($scl='') {
		$this->scl = $scl;
	}
	/**
	 * Recupera l'atribut sdl_tabla de actividadPlazas
	 *
	 * @return string sdl_tabla
	 */
	function getDl_tabla() {
		if (!isset($this->sdl_tabla)) {
			$this->DBCarregar();
		}
		return $this->sdl_tabla;
	}
	/**
	 * estableix el valor de l'atribut sdl_tabla de actividadPlazas
	 *
	 * @param string sdl_tabla
	 */
	function setDl_tabla($sdl_tabla) {
		$this->sdl_tabla = $sdl_tabla;
	}
	/**
	 * Recupera l'atribut ocedidas de actividadPlazas
	 *
	 * @return object json ocedidas
	 */
	function getCedidas() {
		if (!isset($this->ocedidas)) {
			$this->DBCarregar();
		}
		return $this->ocedidas;
	}
	/**
	 * estableix el valor de l'atribut ocedidas de actividadPlazas
	 *
	 * @param json ocedidas='' optional
	 */
	function setCedidas($ocedidas='') {
		$this->ocedidas = $ocedidas;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oactividadPlazasSet = new core\Set();

		$oactividadPlazasSet->add($this->getDatosId_schema());
		$oactividadPlazasSet->add($this->getDatosPlazas());
		$oactividadPlazasSet->add($this->getDatosCl());
		return $oactividadPlazasSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_schema de actividadPlazas
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
	 * Recupera les propietats de l'atribut iplazas de actividadPlazas
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPlazas() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'plazas'));
		$oDatosCampo->setEtiqueta(_("plazas"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scl de actividadPlazas
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCl() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cl'));
		$oDatosCampo->setEtiqueta(_("cl"));
		return $oDatosCampo;
	}
}
?>