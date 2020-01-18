<?php
namespace cambios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula av_cambios_usuario_objeto_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
/**
 * Classe que implementa l'entitat av_cambios_usuario_objeto_pref
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 17/4/2019
 */
class CambioUsuarioObjetoPref Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de CambioUsuarioObjetoPref
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de CambioUsuarioObjetoPref
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item_usuario_objeto de CambioUsuarioObjetoPref
	 *
	 * @var integer
	 */
	 private $iid_item_usuario_objeto;
	/**
	 * Id_usuario de CambioUsuarioObjetoPref
	 *
	 * @var integer
	 */
	 private $iid_usuario;
	/**
	 * Dl_org de CambioUsuarioObjetoPref
	 *
	 * @var string
	 */
	 private $sdl_org;
	/**
	 * Id_tipo_activ_txt de CambioUsuarioObjetoPref
	 *
	 * @var string
	 */
	 private $sid_tipo_activ_txt;
	/**
	 * Id_fase_ini de CambioUsuarioObjetoPref
	 *
	 * @var integer
	 */
	 private $iid_fase_ini;
	/**
	 * Id_fase_fin de CambioUsuarioObjetoPref
	 *
	 * @var integer
	 */
	 private $iid_fase_fin;
	/**
	 * Objeto de CambioUsuarioObjetoPref
	 *
	 * @var string
	 */
	 private $sobjeto;
	/**
	 * Aviso_tipo de CambioUsuarioObjetoPref
	 *
	 * @var integer
	 */
	 private $iaviso_tipo;
	/**
	 * Aviso_donde de CambioUsuarioObjetoPref
	 *
	 * @var string
	 */
	 private $saviso_donde;
	/**
	 * Id_pau de CambioUsuarioObjetoPref
	 *
	 * @var string
	 */
	 private $sid_pau;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de CambioUsuarioObjetoPref
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de CambioUsuarioObjetoPref
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
	 * @param integer|array iid_item_usuario_objeto
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBE'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item_usuario_objeto') && $val_id !== '') $this->iid_item_usuario_objeto = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item_usuario_objeto = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item_usuario_objeto' => $this->iid_item_usuario_objeto);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('av_cambios_usuario_objeto_pref');
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
		$aDades['id_usuario'] = $this->iid_usuario;
		$aDades['dl_org'] = $this->sdl_org;
		$aDades['id_tipo_activ_txt'] = $this->sid_tipo_activ_txt;
		$aDades['id_fase_ini'] = $this->iid_fase_ini;
		$aDades['id_fase_fin'] = $this->iid_fase_fin;
		$aDades['objeto'] = $this->sobjeto;
		$aDades['aviso_tipo'] = $this->iaviso_tipo;
		$aDades['aviso_donde'] = $this->saviso_donde;
		$aDades['id_pau'] = $this->sid_pau;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_usuario               = :id_usuario,
					dl_org                   = :dl_org,
					id_tipo_activ_txt        = :id_tipo_activ_txt,
					id_fase_ini              = :id_fase_ini,
					id_fase_fin              = :id_fase_fin,
					objeto                   = :objeto,
					aviso_tipo               = :aviso_tipo,
					aviso_donde              = :aviso_donde,
					id_pau                   = :id_pau";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item_usuario_objeto='$this->iid_item_usuario_objeto'")) === FALSE) {
				$sClauError = 'CambioUsuarioObjetoPref.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CambioUsuarioObjetoPref.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_usuario,dl_org,id_tipo_activ_txt,id_fase_ini,id_fase_fin,objeto,aviso_tipo,aviso_donde,id_pau)";
			$valores="(:id_usuario,:dl_org,:id_tipo_activ_txt,:id_fase_ini,:id_fase_fin,:objeto,:aviso_tipo,:aviso_donde,:id_pau)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'CambioUsuarioObjetoPref.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				if ($oDblSt->execute($aDades) === FALSE) {
					$sClauError = 'CambioUsuarioObjetoPref.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item_usuario_objeto = $oDbl->lastInsertId('av_cambios_usuario_objeto_pref_id_item_usuario_objeto_seq');
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
		if (isset($this->iid_item_usuario_objeto)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item_usuario_objeto='$this->iid_item_usuario_objeto'")) === FALSE) {
				$sClauError = 'CambioUsuarioObjetoPref.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item_usuario_objeto='$this->iid_item_usuario_objeto'")) === FALSE) {
			$sClauError = 'CambioUsuarioObjetoPref.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
	
	/**
	 * retorna un arary amb els possibles tipus d'avis.
	 *
	 * @retrun array aTipos_aviso
	 */
	
	public static function getTipos_aviso() {
	    $aTipos_aviso = [ 1 =>  _("anotar en lista"),
                        2 =>  _("e-mail")
                    ];
	    
	    return $aTipos_aviso;
	}
	
	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_item_usuario_objeto',$aDades)) $this->setId_item_usuario_objeto($aDades['id_item_usuario_objeto']);
		if (array_key_exists('id_usuario',$aDades)) $this->setId_usuario($aDades['id_usuario']);
		if (array_key_exists('dl_org',$aDades)) $this->setDl_org($aDades['dl_org']);
		if (array_key_exists('id_tipo_activ_txt',$aDades)) $this->setId_tipo_activ_txt($aDades['id_tipo_activ_txt']);
		if (array_key_exists('id_fase_ini',$aDades)) $this->setId_fase_ini($aDades['id_fase_ini']);
		if (array_key_exists('id_fase_fin',$aDades)) $this->setId_fase_fin($aDades['id_fase_fin']);
		if (array_key_exists('objeto',$aDades)) $this->setObjeto($aDades['objeto']);
		if (array_key_exists('aviso_tipo',$aDades)) $this->setAviso_tipo($aDades['aviso_tipo']);
		if (array_key_exists('aviso_donde',$aDades)) $this->setAviso_donde($aDades['aviso_donde']);
		if (array_key_exists('id_pau',$aDades)) $this->setId_pau($aDades['id_pau']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_item_usuario_objeto('');
		$this->setId_usuario('');
		$this->setDl_org('');
		$this->setId_tipo_activ_txt('');
		$this->setId_fase_ini('');
		$this->setId_fase_fin('');
		$this->setObjeto('');
		$this->setAviso_tipo('');
		$this->setAviso_donde('');
		$this->setId_pau('');
	}



	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de CambioUsuarioObjetoPref en un array
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
	 * Recupera las claus primàries de CambioUsuarioObjetoPref en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item_usuario_objeto' => $this->iid_item_usuario_objeto);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item_usuario_objeto de CambioUsuarioObjetoPref
	 *
	 * @return integer iid_item_usuario_objeto
	 */
	function getId_item_usuario_objeto() {
		if (!isset($this->iid_item_usuario_objeto)) {
			$this->DBCarregar();
		}
		return $this->iid_item_usuario_objeto;
	}
	/**
	 * estableix el valor de l'atribut iid_item_usuario_objeto de CambioUsuarioObjetoPref
	 *
	 * @param integer iid_item_usuario_objeto
	 */
	function setId_item_usuario_objeto($iid_item_usuario_objeto) {
		$this->iid_item_usuario_objeto = $iid_item_usuario_objeto;
	}
	/**
	 * Recupera l'atribut iid_usuario de CambioUsuarioObjetoPref
	 *
	 * @return integer iid_usuario
	 */
	function getId_usuario() {
		if (!isset($this->iid_usuario)) {
			$this->DBCarregar();
		}
		return $this->iid_usuario;
	}
	/**
	 * estableix el valor de l'atribut iid_usuario de CambioUsuarioObjetoPref
	 *
	 * @param integer iid_usuario='' optional
	 */
	function setId_usuario($iid_usuario='') {
		$this->iid_usuario = $iid_usuario;
	}
	/**
	 * Recupera l'atribut sdl_org de CambioUsuarioObjetoPref
	 *
	 * @return boolean sdl_org
	 */
	function getDl_org() {
		if (!isset($this->sdl_org)) {
			$this->DBCarregar();
		}
		return $this->sdl_org;
	}
	/**
	 * estableix el valor de l'atribut sdl_org de CambioUsuarioObjetoPref
	 *
	 * @param string sdl_org='x' optional
	 */
	function setDl_org($sdl_org='x') {
		$this->sdl_org = $sdl_org;
	}
	/**
	 * Recupera l'atribut sid_tipo_activ_txt de CambioUsuarioObjetoPref
	 *
	 * @return string sid_tipo_activ_txt
	 */
	function getId_tipo_activ_txt() {
		if (!isset($this->sid_tipo_activ_txt)) {
			$this->DBCarregar();
		}
		return $this->sid_tipo_activ_txt;
	}
	/**
	 * estableix el valor de l'atribut sid_tipo_activ_txt de CambioUsuarioObjetoPref
	 *
	 * @param string sid_tipo_activ_txt='' optional
	 */
	function setId_tipo_activ_txt($sid_tipo_activ_txt='') {
		$this->sid_tipo_activ_txt = $sid_tipo_activ_txt;
	}
	/**
	 * Recupera l'atribut iid_fase_ini de CambioUsuarioObjetoPref
	 *
	 * @return integer iid_fase_ini
	 */
	function getId_fase_ini() {
		if (!isset($this->iid_fase_ini)) {
			$this->DBCarregar();
		}
		return $this->iid_fase_ini;
	}
	/**
	 * estableix el valor de l'atribut iid_fase_ini de CambioUsuarioObjetoPref
	 *
	 * @param integer iid_fase_ini='' optional
	 */
	function setId_fase_ini($iid_fase_ini='') {
		$this->iid_fase_ini = $iid_fase_ini;
	}
	/**
	 * Recupera l'atribut iid_fase_fin de CambioUsuarioObjetoPref
	 *
	 * @return integer iid_fase_fin
	 */
	function getId_fase_fin() {
		if (!isset($this->iid_fase_fin)) {
			$this->DBCarregar();
		}
		return $this->iid_fase_fin;
	}
	/**
	 * estableix el valor de l'atribut iid_fase_fin de CambioUsuarioObjetoPref
	 *
	 * @param integer iid_fase_fin='' optional
	 */
	function setId_fase_fin($iid_fase_fin='') {
		$this->iid_fase_fin = $iid_fase_fin;
	}
	/**
	 * Recupera l'atribut sobjeto de CambioUsuarioObjetoPref
	 *
	 * @return string sobjeto
	 */
	function getObjeto() {
		if (!isset($this->sobjeto)) {
			$this->DBCarregar();
		}
		return $this->sobjeto;
	}
	/**
	 * estableix el valor de l'atribut sobjeto de CambioUsuarioObjetoPref
	 *
	 * @param string sobjeto='' optional
	 */
	function setObjeto($sobjeto='') {
		$this->sobjeto = $sobjeto;
	}
	/**
	 * Recupera l'atribut iaviso_tipo de CambioUsuarioObjetoPref
	 *
	 * @return integer iaviso_tipo
	 */
	function getAviso_tipo() {
		if (!isset($this->iaviso_tipo)) {
			$this->DBCarregar();
		}
		return $this->iaviso_tipo;
	}
	/**
	 * estableix el valor de l'atribut iaviso_tipo de CambioUsuarioObjetoPref
	 *
	 * @param integer iaviso_tipo='' optional
	 */
	function setAviso_tipo($iaviso_tipo='') {
		$this->iaviso_tipo = $iaviso_tipo;
	}
	/**
	 * Recupera l'atribut saviso_donde de CambioUsuarioObjetoPref
	 *
	 * @return string saviso_donde
	 */
	function getAviso_donde() {
		if (!isset($this->saviso_donde)) {
			$this->DBCarregar();
		}
		return $this->saviso_donde;
	}
	/**
	 * estableix el valor de l'atribut saviso_donde de CambioUsuarioObjetoPref
	 *
	 * @param string saviso_donde='' optional
	 */
	function setAviso_donde($saviso_donde='') {
		$this->saviso_donde = $saviso_donde;
	}
	/**
	 * Recupera l'atribut sid_pau de CambioUsuarioObjetoPref
	 *
	 * @return string sid_pau
	 */
	function getId_pau() {
		if (!isset($this->sid_pau)) {
			$this->DBCarregar();
		}
		return $this->sid_pau;
	}
	/**
	 * estableix el valor de l'atribut sid_pau de CambioUsuarioObjetoPref
	 *
	 * @param string sid_pau='' optional
	 */
	function setId_pau($sid_pau='') {
		$this->sid_pau = $sid_pau;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCambioUsuarioObjetoPrefSet = new core\Set();

		$oCambioUsuarioObjetoPrefSet->add($this->getDatosId_usuario());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosDl_org());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosId_tipo_activ_txt());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosId_fase_ini());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosId_fase_fin());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosObjeto());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosAviso_tipo());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosAviso_donde());
		$oCambioUsuarioObjetoPrefSet->add($this->getDatosId_pau());
		return $oCambioUsuarioObjetoPrefSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_usuario de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_usuario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_usuario'));
		$oDatosCampo->setEtiqueta(_("id_usuario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl_org de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDl_org() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl_org'));
		$oDatosCampo->setEtiqueta(_("dl_org"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_tipo_activ_txt de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_activ_txt() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_activ_txt'));
		$oDatosCampo->setEtiqueta(_("id_tipo_activ_txt"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase_ini de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase_ini'));
		$oDatosCampo->setEtiqueta(_("id_fase_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_fase_fin de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_fase_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_fase_fin'));
		$oDatosCampo->setEtiqueta(_("id_fase_fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobjeto de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObjeto() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'objeto'));
		$oDatosCampo->setEtiqueta(_("objeto"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iaviso_tipo de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAviso_tipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'aviso_tipo'));
		$oDatosCampo->setEtiqueta(_("aviso_tipo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut saviso_donde de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosAviso_donde() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'aviso_donde'));
		$oDatosCampo->setEtiqueta(_("aviso_donde"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_pau de CambioUsuarioObjetoPref
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_pau() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_pau'));
		$oDatosCampo->setEtiqueta(_("id_pau"));
		return $oDatosCampo;
	}
}
