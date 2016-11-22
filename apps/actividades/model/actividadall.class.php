<?php
namespace actividades\model;
use core;
//require_once('classes/web/fechas.class');
use cambios\model as cambios;
use procesos\model as procesos;
/**
 * Classe que implementa l'entitat a_actividades_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class ActividadAll Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadAll
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de ActividadAll
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * aDades de ActividadAll abans dels canvis.
	 *
	 * @var array
	 */
	 protected $aDadesActuals;

	/**
	 * Id_activ de ActividadAll
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	/**
	 * Id_tipo_activ de ActividadAll
	 *
	 * @var integer
	 */
	 protected $iid_tipo_activ;
	/**
	 * Dl_org de ActividadAll
	 *
	 * @var string
	 */
	 protected $sdl_org;
	/**
	 * Nom_activ de ActividadAll
	 *
	 * @var string
	 */
	 protected $snom_activ;
	/**
	 * Id_ubi de ActividadAll
	 *
	 * @var integer
	 */
	 protected $iid_ubi;
	/**
	 * Desc_activ de ActividadAll
	 *
	 * @var string
	 */
	 protected $sdesc_activ;
	/**
	 * F_ini de ActividadAll
	 *
	 * @var date
	 */
	 protected $df_ini;
	/**
	 * H_ini de ActividadAll
	 *
	 * @var time
	 */
	 protected $th_ini;
	/**
	 * F_fin de ActividadAll
	 *
	 * @var date
	 */
	 protected $df_fin;
	/**
	 * H_fin de ActividadAll
	 *
	 * @var time
	 */
	 protected $th_fin;
	/**
	 * Tipo_horario de ActividadAll
	 *
	 * @var integer
	 */
	 protected $itipo_horario;
	/**
	 * Precio de ActividadAll
	 *
	 * @var integer
	 */
	 protected $iprecio;
	/**
	 * Num_asistentes de ActividadAll
	 *
	 * @var integer
	 */
	 protected $inum_asistentes;
	/**
	 * Status de ActividadAll
	 *
	 * @var integer
	 */
	 protected $istatus;
	/**
	 * Observ de ActividadAll
	 *
	 * @var string
	 */
	 protected $sobserv;
	/**
	 * Nivel_stgr de ActividadAll
	 *
	 * @var integer
	 */
	 protected $inivel_stgr;
	/**
	 * Observ_material de ActividadAll
	 *
	 * @var string
	 */
	 protected $sobserv_material;
	/**
	 * Lugar_esp de ActividadAll
	 *
	 * @var string
	 */
	 protected $slugar_esp;
	/**
	 * Tarifa de ActividadAll
	 *
	 * @var integer
	 */
	 protected $itarifa;
	/**
	 * Id_repeticion de ActividadAll
	 *
	 * @var integer
	 */
	 protected $iid_repeticion;
	/**
	 * Publicado de ActividadAll
	 *
	 * @var boolean
	 */
	 protected $bpublicado;
	/**
	 * Id_tabla de ActividadAll
	 *
	 * @var string
	 */
	 protected $sid_tabla;
	/**
	 * Plazas de ActividadAll
	 *
	 * @var smallinteger
	 */
	 protected $iplazas;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * duracion de ActividadAllrequire_once('classes/gestorCanvis.class');

	 *
	 * @var integer
	 */
	 protected $iduracion;
	/**
	 * duracion Real (horas/24) de ActividadAll
	 *
	 * @var integer
	 */
	 protected $iduracionR;

	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_activ
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
				$this->iid_activ = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_activ' => $this->iid_activ);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('a_actividades_all');
	}

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 *@param bool optional $quiet : true per que no apunti els canvis. 0 (per defecte) apunta els canvis.
	 */
	public function DBGuardar($quiet=0) {
		$aDades=array();
		$aDades['id_tipo_activ'] = $this->iid_tipo_activ;
		$aDades['dl_org'] = $this->sdl_org;
		$aDades['nom_activ'] = $this->snom_activ;
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['desc_activ'] = $this->sdesc_activ;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['h_ini'] = $this->th_ini;
		$aDades['f_fin'] = $this->df_fin;
		$aDades['h_fin'] = $this->th_fin;
		$aDades['tipo_horario'] = $this->itipo_horario;
		$aDades['precio'] = $this->iprecio;
		$aDades['num_asistentes'] = $this->inum_asistentes;
		$aDades['status'] = $this->istatus;
		$aDades['observ'] = $this->sobserv;
		$aDades['nivel_stgr'] = $this->inivel_stgr;
		$aDades['observ_material'] = $this->sobserv_material;
		$aDades['lugar_esp'] = $this->slugar_esp;
		$aDades['tarifa'] = $this->itarifa;
		$aDades['id_repeticion'] = $this->iid_repeticion;
		$aDades['publicado'] = $this->bpublicado;
		$aDades['id_tabla'] = $this->sid_tabla;
		$aDades['plazas'] = $this->iplazas;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
 		if (empty($aDades['publicado']) || ($aDades['publicado'] === 'off') || ($aDades['publicado'] === 'false') || ($aDades['publicado'] === 'f')) { $aDades['publicado']='f'; } else { $aDades['publicado']='t'; }

		$a_pkey = $this->aPrimary_key;
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $aDades['dl_org']);
		$id_tabla = $aDades['id_tabla'];
		if ($dl == core\ConfigGlobal::mi_dele()) {
			$oActividadAll= new ActividadDl($a_pkey);
		} else {
			if ($id_tabla == 'dl') {
				$oActividadAll= new ActividadPub($a_pkey);
			} else {
				$oActividadAll= new ActividadEx($a_pkey);
			}
		}
		$oActividadAll->setAllAtributes($aDades);

		$oActividadAll->DBGuardar();
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (isset($this->iid_activ)) {
			//echo "SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'";
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ'")) === false) {
				$sClauError = 'ActividadAll.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
					$this->aDadesActuals=$aDades;
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
		$a_pkey = $this->aPrimary_key;
		// si es de la sf quito la 'f'
		$dl = preg_replace('/f$/', '', $aDades['dl_org']);
		$id_tabla = $aDades['id_tabla'];
		if ($dl == core\ConfigGlobal::mi_dele()) {
			$oActividadAll= new ActividadDl($a_pkey);
		} else {
			if ($id_tabla == 'dl') {
				// No se puede eliminar una actividad de otra dl. Hay que borrarla como importada
				$oImportada = new Importada($a_pkey);
				$oImportada->DBEliminar();
				return true;
			} else {
				$oActividadAll= new ActividadEx($a_pkey);
			}
		}
		$oActividadAll->DBEliminar();
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
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_tipo_activ',$aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
		if (array_key_exists('dl_org',$aDades)) $this->setDl_org($aDades['dl_org']);
		if (array_key_exists('nom_activ',$aDades)) $this->setNom_activ($aDades['nom_activ']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('desc_activ',$aDades)) $this->setDesc_activ($aDades['desc_activ']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini']);
		if (array_key_exists('h_ini',$aDades)) $this->setH_ini($aDades['h_ini']);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin']);
		if (array_key_exists('h_fin',$aDades)) $this->setH_fin($aDades['h_fin']);
		if (array_key_exists('tipo_horario',$aDades)) $this->setTipo_horario($aDades['tipo_horario']);
		if (array_key_exists('precio',$aDades)) $this->setPrecio($aDades['precio']);
		if (array_key_exists('num_asistentes',$aDades)) $this->setNum_asistentes($aDades['num_asistentes']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('nivel_stgr',$aDades)) $this->setNivel_stgr($aDades['nivel_stgr']);
		if (array_key_exists('observ_material',$aDades)) $this->setObserv_material($aDades['observ_material']);
		if (array_key_exists('lugar_esp',$aDades)) $this->setLugar_esp($aDades['lugar_esp']);
		if (array_key_exists('tarifa',$aDades)) $this->setTarifa($aDades['tarifa']);
		if (array_key_exists('id_repeticion',$aDades)) $this->setId_repeticion($aDades['id_repeticion']);
		if (array_key_exists('publicado',$aDades)) $this->setPublicado($aDades['publicado']);
		if (array_key_exists('id_tabla',$aDades)) $this->setId_tabla($aDades['id_tabla']);
		if (array_key_exists('plazas',$aDades)) $this->setPlazas($aDades['plazas']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ActividadAll en un array
	 *
	 * @return array aDades
	 */
	public function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar('tot');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de ActividadAll en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_activ' => $this->iid_activ);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_activ de ActividadAll
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
	 * estableix el valor de l'atribut iid_activ de ActividadAll
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_tipo_activ de ActividadAll
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
	 * estableix el valor de l'atribut iid_tipo_activ de ActividadAll
	 *
	 * @param integer iid_tipo_activ='' optional
	 */
	function setId_tipo_activ($iid_tipo_activ='') {
		// comprovo que té 6 digits
		if (($iid_tipo_activ/100000) < 1) {
			$serr = "id_tipo_activ incorrecto: $iid_tipo_activ";
			$sClauError = "ActividadAll.setId_tipo_activ";
			$_SESSION['oGestorErrores']->addError($serr, $sClauError, __LINE__, __FILE__);
			return false;
		}
		$this->iid_tipo_activ = $iid_tipo_activ;
	}
	/**
	 * Recupera l'atribut sdl_org de ActividadAll
	 *
	 * @return string sdl_org
	 */
	function getDl_org() {
		if (!isset($this->sdl_org)) {
			$this->DBCarregar();
		}
		return $this->sdl_org;
	}
	/**
	 * estableix el valor de l'atribut sdl_org de ActividadAll
	 *
	 * @param string sdl_org='' optional
	 */
	function setDl_org($sdl_org='') {
		$this->sdl_org = $sdl_org;
	}
	/**
	 * Recupera l'atribut snom_activ de ActividadAll
	 *
	 * @return string snom_activ
	 */
	function getNom_activ() {
		if (!isset($this->snom_activ)) {
			$this->DBCarregar();
		}
		return $this->snom_activ;
	}
	/**
	 * estableix el valor de l'atribut snom_activ de ActividadAll
	 *
	 * @param string snom_activ='' optional
	 */
	function setNom_activ($snom_activ='') {
		$this->snom_activ = $snom_activ;
	}
	/**
	 * Recupera l'atribut iid_ubi de ActividadAll
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi)) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de ActividadAll
	 *
	 * @param integer iid_ubi='' optional
	 */
	function setId_ubi($iid_ubi='') {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut sdesc_activ de ActividadAll
	 *
	 * @return string sdesc_activ
	 */
	function getDesc_activ() {
		if (!isset($this->sdesc_activ)) {
			$this->DBCarregar();
		}
		return $this->sdesc_activ;
	}
	/**
	 * estableix el valor de l'atribut sdesc_activ de ActividadAll
	 *
	 * @param string sdesc_activ='' optional
	 */
	function setDesc_activ($sdesc_activ='') {
		$this->sdesc_activ = $sdesc_activ;
	}
	/**
	 * Recupera l'atribut df_ini de ActividadAll
	 *
	 * @return date df_ini
	 */
	function getF_ini() {
		if (!isset($this->df_ini)) {
			$this->DBCarregar();
		}
		return $this->df_ini;
	}
	/**
	 * estableix el valor de l'atribut df_ini de ActividadAll
	 *
	 * @param date df_ini='' optional
	 */
	function setF_ini($df_ini='') {
		$this->df_ini = $df_ini;
	}
	/**
	 * Recupera l'atribut th_ini de ActividadAll
	 *
	 * @return time th_ini
	 */
	function getH_ini() {
		if (!isset($this->th_ini)) {
			$this->DBCarregar();
		}
		return $this->th_ini;
	}
	/**
	 * estableix el valor de l'atribut th_ini de ActividadAll
	 *
	 * @param time th_ini='' optional
	 */
	function setH_ini($th_ini='') {
		$this->th_ini = $th_ini;
	}
	/**
	 * Recupera l'atribut df_fin de ActividadAll
	 *
	 * @return date df_fin
	 */
	function getF_fin() {
		if (!isset($this->df_fin)) {
			$this->DBCarregar();
		}
		return $this->df_fin;
	}
	/**
	 * estableix el valor de l'atribut df_fin de ActividadAll
	 *
	 * @param date df_fin='' optional
	 */
	function setF_fin($df_fin='') {
		$this->df_fin = $df_fin;
	}
	/**
	 * Recupera l'atribut th_fin de ActividadAll
	 *
	 * @return time th_fin
	 */
	function getH_fin() {
		if (!isset($this->th_fin)) {
			$this->DBCarregar();
		}
		return $this->th_fin;
	}
	/**
	 * estableix el valor de l'atribut th_fin de ActividadAll
	 *
	 * @param time th_fin='' optional
	 */
	function setH_fin($th_fin='') {
		$this->th_fin = $th_fin;
	}
	/**
	 * Recupera l'atribut itipo_horario de ActividadAll
	 *
	 * @return integer itipo_horario
	 */
	function getTipo_horario() {
		if (!isset($this->itipo_horario)) {
			$this->DBCarregar();
		}
		return $this->itipo_horario;
	}
	/**
	 * estableix el valor de l'atribut itipo_horario de ActividadAll
	 *
	 * @param integer itipo_horario='' optional
	 */
	function setTipo_horario($itipo_horario='') {
		$this->itipo_horario = $itipo_horario;
	}
	/**
	 * Recupera l'atribut iprecio de ActividadAll
	 *
	 * @return integer iprecio
	 */
	function getPrecio() {
		if (!isset($this->iprecio)) {
			$this->DBCarregar();
		}
		return $this->iprecio;
	}
	/**
	 * estableix el valor de l'atribut iprecio de ActividadAll
	 *
	 * @param integer iprecio='' optional
	 */
	function setPrecio($iprecio='') {
		// adminto ',' como separador decimal.
		$iprecio = str_replace(",", ".", $iprecio);
		$this->iprecio = $iprecio;
	}
	/**
	 * Recupera l'atribut inum_asistentes de ActividadAll
	 *
	 * @return integer inum_asistentes
	 */
	function getNum_asistentes() {
		if (!isset($this->inum_asistentes)) {
			$this->DBCarregar();
		}
		return $this->inum_asistentes;
	}
	/**
	 * estableix el valor de l'atribut inum_asistentes de ActividadAll
	 *
	 * @param integer inum_asistentes='' optional
	 */
	function setNum_asistentes($inum_asistentes='') {
		$this->inum_asistentes = $inum_asistentes;
	}
	/**
	 * Recupera l'atribut istatus de ActividadAll
	 *
	 * @return integer istatus
	 */
	function getStatus() {
		if (!isset($this->istatus)) {
			$this->DBCarregar();
		}
		return $this->istatus;
	}
	/**
	 * estableix el valor de l'atribut istatus de ActividadAll
	 *
	 * @param integer istatus='' optional
	 */
	function setStatus($istatus='') {
		$this->istatus = $istatus;
	}
	/**
	 * Recupera l'atribut sobserv de ActividadAll
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv)) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de ActividadAll
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut inivel_stgr de ActividadAll
	 *
	 * @return integer inivel_stgr
	 */
	function getNivel_stgr() {
		if (!isset($this->inivel_stgr)) {
			$this->DBCarregar();
		}
		return $this->inivel_stgr;
	}
	/**
	 * estableix el valor de l'atribut inivel_stgr de ActividadAll
	 *
	 * @param integer inivel_stgr='' optional
	 */
	function setNivel_stgr($inivel_stgr='') {
		$this->inivel_stgr = $inivel_stgr;
	}
	/**
	 * Recupera l'atribut sobserv_material de ActividadAll
	 *
	 * @return string sobserv_material
	 */
	function getObserv_material() {
		if (!isset($this->sobserv_material)) {
			$this->DBCarregar();
		}
		return $this->sobserv_material;
	}
	/**
	 * estableix el valor de l'atribut sobserv_material de ActividadAll
	 *
	 * @param string sobserv_material='' optional
	 */
	function setObserv_material($sobserv_material='') {
		$this->sobserv_material = $sobserv_material;
	}
	/**
	 * Recupera l'atribut slugar_esp de ActividadAll
	 *
	 * @return string slugar_esp
	 */
	function getLugar_esp() {
		if (!isset($this->slugar_esp)) {
			$this->DBCarregar();
		}
		return $this->slugar_esp;
	}
	/**
	 * estableix el valor de l'atribut slugar_esp de ActividadAll
	 *
	 * @param string slugar_esp='' optional
	 */
	function setLugar_esp($slugar_esp='') {
		$this->slugar_esp = $slugar_esp;
	}
	/**
	 * Recupera l'atribut itarifa de ActividadAll
	 *
	 * @return integer itarifa
	 */
	function getTarifa() {
		if (!isset($this->itarifa)) {
			$this->DBCarregar();
		}
		return $this->itarifa;
	}
	/**
	 * estableix el valor de l'atribut itarifa de ActividadAll
	 *
	 * @param integer itarifa='' optional
	 */
	function setTarifa($itarifa='') {
		$this->itarifa = $itarifa;
	}
	/**
	 * Recupera l'atribut iid_repeticion de ActividadAll
	 *
	 * @return integer iid_repeticion
	 */
	function getId_repeticion() {
		if (!isset($this->iid_repeticion)) {
			$this->DBCarregar();
		}
		return $this->iid_repeticion;
	}
	/**
	 * estableix el valor de l'atribut iid_repeticion de ActividadAll
	 *
	 * @param integer iid_repeticion='' optional
	 */
	function setId_repeticion($iid_repeticion='') {
		$this->iid_repeticion = $iid_repeticion;
	}
	/**
	 * Recupera l'atribut bpublicado de ActividadAll
	 *
	 * @return boolean bpublicado
	 */
	function getPublicado() {
		if (!isset($this->bpublicado)) {
			$this->DBCarregar();
		}
		return $this->bpublicado;
	}
	/**
	 * estableix el valor de l'atribut bpublicado de ActividadAll
	 *
	 * @param boolean bpublicado='f'
	 */
	function setPublicado($bpublicado='f') {
		$this->bpublicado = $bpublicado;
	}
	/**
	 * Recupera l'atribut sid_tabla de ActividadAll
	 *
	 * @return string sid_tabla
	 */
	function getId_tabla() {
		if (!isset($this->sid_tabla)) {
			$this->DBCarregar();
		}
		return $this->sid_tabla;
	}
	/**
	 * estableix el valor de l'atribut sid_tabla de ActividadAll
	 *
	 * @param string sid_tabla='' optional
	 */
	function setId_tabla($sid_tabla='') {
		$this->sid_tabla = $sid_tabla;
	}
	/**
	 * Recupera l'atribut iplazas de ActividadAll
	 *
	 * @return string iplazas
	 */
	function getPlazas() {
		if (!isset($this->iplazas)) {
			$this->DBCarregar();
		}
		return $this->iplazas;
	}
	/**
	 * estableix el valor de l'atribut iplazas de ActividadAll
	 *
	 * @param string iplazas='' optional
	 */
	function setPlazas($iplazas='') {
		$this->iplazas = $iplazas;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
	/**
	 * Recupera l'atribut idias de ActividadAll
	 *
	 * @return integer idias
	 */
	function getDuracionEnPeriodo($inicio,$fin) {
		$num_dias = $this->getDuracionReal();

		// si la actividad empieza antes del inicio, cojo como valor del inicio de la actividad, el valor de inicio del periodo.
		$oIniTot = DateTime::createFromFormat('j/n/Y H:i:s',$inicio." 00:00:00");
		$oFinTot = DateTime::createFromFormat('j/n/Y H:i:s',$fin." 23:59:59");

		$hIni = empty($this->th_ini)? '21:00:00' : $this->th_ini;
		$hFin = empty($this->th_fin)? '10:00:00' : $this->th_fin;
		$oInicio = DateTime::createFromFormat('j/n/Y H:i:s', $this->df_ini." ".$hIni);
		$oFin = DateTime::createFromFormat('j/n/Y H:i:s', $this->df_fin." ".$hFin);

		if ($oInicio < $oIniTot) {
			$isoActivIni = $oIniTot->format('YmdHis');
			$interval = $oIniTot->diff($oFin);
			$horas = $interval->format('%a')*24 +$interval->format('%h')+$interval->format('%i')/60+$interval->format('%s')/3600;
			$num_dias=round($horas/24,2);
		} else {
			$isoActivIni = $oInicio->format('YmdHis');
		}
		// lo mismo para el final:
		if ($oFin > $oFinTot) {
			$isoActivFin = $oFinTot->format('YmdHis');
			$interval = $oInicio->diff($oFinTot);
			$horas = $interval->format('%a')*24 +$interval->format('%h')+$interval->format('%i')/60+$interval->format('%s')/3600;
			$num_dias=round($horas/24,2);
		} else {
			$isoActivFin = $oFin->format('YmdHis');
		}
	//echo "<br>A: $nom_activ [$isoActivIni][$isoActivFin]<br>";
	// miro si la actividad empieza y termina en el mismo periodo.
		$iniPeriodo = $oIniTot->format('YmdHis');
		$finPeriodo = $oFinTot->format('YmdHis');
		if ($isoActivIni <= $finPeriodo && $isoActivIni >= $iniPeriodo) {
			if ($isoActivFin >= $iniPeriodo && $isoActivFin <= $finPeriodo) { 
				//empieza y termina en el periodo.
				$iduracion_per=$num_dias;
			}
		}
		return $iduracion_per;
	}
	/**
	 * Recupera l'atribut iduracionR de ActividadAll
	 *
	 * @return integer iduracionR
	 */
	function getDuracionReal() {
		if (!isset($this->iduracionR)) {
			if (!isset($this->df_ini) || !isset($this->df_fin)) {
				$this->DBCarregar();
			}
			$hIni = empty($this->th_ini)? '21:00:00' : $this->th_ini;
			$hFin = empty($this->th_fin)? '10:00:00' : $this->th_fin;
			$oF_ini_ca = DateTime::createFromFormat('j/m/Y H:i:s', $this->df_ini." ".$hIni);
			$oF_fin_ca = DateTime::createFromFormat('j/m/Y H:i:s', $this->df_fin." ".$hFin);
			$interval = $oF_ini_ca->diff($oF_fin_ca);
			$horas = $interval->format('%a')*24 +$interval->format('%h')+$interval->format('%i')/60+$interval->format('%s')/3600;
			$dias=round($horas/24,2);
			$this->iduracionR = $dias;
		}
		return $this->iduracionR;
	}
	/**
	 * Recupera l'atribut iduracion de ActividadAll
	 *
	 * @return integer iduracion
	 */
	function getDuracion() {
		if (!isset($this->iduracion)) {
			if (!isset($this->df_ini) || !isset($this->df_fin)) {
				$this->DBCarregar();
			}
			$hIni = empty($this->th_ini)? '21:00:00' : $this->th_ini;
			$hFin = empty($this->th_fin)? '10:00:00' : $this->th_fin;
			$oF_ini_ca = new \DateTimeLocal();
			$oF_ini_ca->setFromFormat('j/m/Y H:i:s', $this->df_ini." ".$hIni);
			$oF_fin_ca = DateTime::createFromFormat('j/m/Y H:i:s', $this->df_fin." ".$hFin);

			$this->iduracion = $oF_ini_ca->duracionAjustada($oF_fin_ca);
		}
		return $this->iduracion;
	}
	

	/**
	 * Retorna el nivel_sgtr calculat a partir del id_tipo_activ
	 *
	 * @return integer nivel_stgr
	 */
	function generarNivelStgr() {
		//segun la tabla comun: public.xa_nivel_stgr
		$id_tipo_activ = $this->getId_tipo_activ();
		$nivel_stgr = '';
		switch ($id_tipo_activ) {
			case 112000: //bienio
			case 112020:
			case 133000:
			case 133020:
				$nivel_stgr=1;
				break;
			case 112021: //cuadrienio
			case 112112: // semestre n
				$nivel_stgr=2;
				break;
			case 133021:
				$nivel_stgr=3;
				break;
			case 133105: // bienio y cuadrienio
				$nivel_stgr=10;
				break;
			case 112023: //repaso
			case 133023:
				$nivel_stgr=4;
				break;
			case 133016: // ceagd
				$nivel_stgr=5;
				break;
		}
		return $nivel_stgr;
	}
	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActividadAllSet = new core\Set();

		$oActividadAllSet->add($this->getDatosId_tipo_activ());
		$oActividadAllSet->add($this->getDatosDl_org());
		$oActividadAllSet->add($this->getDatosNom_activ());
		$oActividadAllSet->add($this->getDatosId_ubi());
		$oActividadAllSet->add($this->getDatosDesc_activ());
		$oActividadAllSet->add($this->getDatosF_ini());
		$oActividadAllSet->add($this->getDatosH_ini());
		$oActividadAllSet->add($this->getDatosF_fin());
		$oActividadAllSet->add($this->getDatosH_fin());
		$oActividadAllSet->add($this->getDatosTipo_horario());
		$oActividadAllSet->add($this->getDatosPrecio());
		$oActividadAllSet->add($this->getDatosNum_asistentes());
		$oActividadAllSet->add($this->getDatosStatus());
		$oActividadAllSet->add($this->getDatosObserv());
		$oActividadAllSet->add($this->getDatosNivel_stgr());
		$oActividadAllSet->add($this->getDatosObserv_material());
		$oActividadAllSet->add($this->getDatosLugar_esp());
		$oActividadAllSet->add($this->getDatosTarifa());
		$oActividadAllSet->add($this->getDatosId_repeticion());
		$oActividadAllSet->add($this->getDatosId_tabla());
		$oActividadAllSet->add($this->getDatosPlazas());
		return $oActividadAllSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_tipo_activ de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_tipo_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_activ'));
		$oDatosCampo->setEtiqueta(_("id_tipo_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdl_org de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosDl_org() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'dl_org'));
		$oDatosCampo->setEtiqueta(_("dl_org"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom_activ de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNom_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_activ'));
		$oDatosCampo->setEtiqueta(_("nom_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_ubi de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi'));
		$oDatosCampo->setEtiqueta(_("id_ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdesc_activ de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosDesc_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'desc_activ'));
		$oDatosCampo->setEtiqueta(_("desc_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ini'));
		$oDatosCampo->setEtiqueta(_("fecha inicio"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut th_ini de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosH_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'h_ini'));
		$oDatosCampo->setEtiqueta(_("hora inicio"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_fin de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_fin'));
		$oDatosCampo->setEtiqueta(_("fecha fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut th_fin de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosH_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'h_fin'));
		$oDatosCampo->setEtiqueta(_("hora fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itipo_horario de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo_horario() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_horario'));
		$oDatosCampo->setEtiqueta(_("tipo de horario"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iprecio de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPrecio() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'precio'));
		$oDatosCampo->setEtiqueta(_("precio"));
		$oDatosCampo->setRegExp("/^(\d+)[,.]?\d{0,2}$/");
		$txt =  _('tiene un formato no válido.');
		$txt.=  "\n";
		$txt.=  _('se adminte un separador para los decimales (máximo 2)');
		$txt.=  "\n";
		$txt.=  _('No se admite separador para los miles');
		$txt.=  "\n";
		$txt.=  _('ejemplo: 1254.56');
		$oDatosCampo->setRegExpText($txt);

		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_asistentes de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNum_asistentes() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_asistentes'));
		$oDatosCampo->setEtiqueta(_("número de asistentes"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut istatus de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosStatus() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'status'));
		$oDatosCampo->setEtiqueta(_("status"));
		$oDatosCampo->setTipo('array');
		$oDatosCampo->setLista(array(1=>'proyecto',2=>'actual',3=>'terminada',4=>'borrable'));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observaciones"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inivel_stgr de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNivel_stgr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nivel_stgr'));
		$oDatosCampo->setEtiqueta(_("nivel de stgr"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv_material de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosObserv_material() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ_material'));
		$oDatosCampo->setEtiqueta(_("observaciones material"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slugar_esp de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosLugar_esp() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lugar_esp'));
		$oDatosCampo->setEtiqueta(_("lugar_esp"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itarifa de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTarifa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tarifa'));
		$oDatosCampo->setEtiqueta(_("tarifa"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_repeticion de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_repeticion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_repeticion'));
		$oDatosCampo->setEtiqueta(_("id_repeticion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpublicado de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPublicado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'publicado'));
		$oDatosCampo->setEtiqueta(_("publicado"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sid_tabla de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_tabla() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tabla'));
		$oDatosCampo->setEtiqueta(_("id_tabla"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iplazas de ActividadAll
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosPlazas() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'plazas'));
		$oDatosCampo->setEtiqueta(_("plazas"));
		return $oDatosCampo;
	}
}
?>
