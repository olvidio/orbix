<?php
namespace ubis\model;
use core;
/**
 * Classe que implementa l'entitat u_centros_dl
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 27/09/2010
 */

class CentroDl Extends Centro {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * Id_auto de CentroDl
	 *
	 * @var integer
	 */
	 protected $iid_auto;
	/**
	 * N_buzon de CentroDl
	 *
	 * @var integer
	 */
	 protected $in_buzon;
	/**
	 * Num_pi de CentroDl
	 *
	 * @var integer
	 */
	 protected $inum_pi;
	/**
	 * Num_cartas de CentroDl
	 *
	 * @var integer
	 */
	 protected $inum_cartas;
	/**
	 * Observ de CentroDl
	 *
	 * @var string
	 */
	 protected $sobserv;
	/**
	 * Num_habit_indiv de CentroDl
	 *
	 * @var integer
	 */
	 protected $inum_habit_indiv;
	/**
	 * Plazas de CentroDl
	 *
	 * @var integer
	 */
	 protected $iplazas;
	/**
	 * Id_zona de CentroDl
	 *
	 * @var integer
	 */
	 protected $iid_zona;
	/**
	 * Num_cartas_mensuales de CentroDl
	 *
	 * @var integer
	 */
	 protected $inum_cartas_mensuales;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array $iid_ubi
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_ubi = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_ubi' => $this->iid_ubi);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_centros_dl');
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
		$aDades['tipo_ubi'] = $this->stipo_ubi;
		$aDades['nombre_ubi'] = $this->snombre_ubi;
		$aDades['dl'] = $this->sdl;
		$aDades['pais'] = $this->spais;
		$aDades['region'] = $this->sregion;
		$aDades['status'] = $this->bstatus;
		$aDades['f_status'] = $this->df_status;
		$aDades['sv'] = $this->bsv;
		$aDades['sf'] = $this->bsf;
		$aDades['tipo_ctr'] = $this->stipo_ctr;
		$aDades['tipo_labor'] = $this->itipo_labor;
		$aDades['cdc'] = $this->bcdc;
		$aDades['id_ctr_padre'] = $this->iid_ctr_padre;
		$aDades['n_buzon'] = $this->in_buzon;
		$aDades['num_pi'] = $this->inum_pi;
		$aDades['num_cartas'] = $this->inum_cartas;
		$aDades['observ'] = $this->sobserv;
		$aDades['num_habit_indiv'] = $this->inum_habit_indiv;
		$aDades['plazas'] = $this->iplazas;
		$aDades['id_zona'] = $this->iid_zona;
		$aDades['num_cartas_mensuales'] = $this->inum_cartas_mensuales;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
 		if (empty($aDades['status']) || ($aDades['status'] === 'off') || ($aDades['status'] === 'false') || ($aDades['status'] === 'f')) { $aDades['status']='f'; } else { $aDades['status']='t'; }
 		if (empty($aDades['sv']) || ($aDades['sv'] === 'off') || ($aDades['sv'] === 'false') || ($aDades['sv'] === 'f')) { $aDades['sv']='f'; } else { $aDades['sv']='t'; }
		if (empty($aDades['sf']) || ($aDades['sf'] === 'off') || ($aDades['sf'] === 'false') || ($aDades['sf'] === 'f')) { $aDades['sf']='f'; } else { $aDades['sf']='t'; }
		if (empty($aDades['cdc']) || ($aDades['cdc'] === 'off') || ($aDades['cdc'] === 'false') || ($aDades['cdc'] === 'f')) { $aDades['cdc']='f'; } else { $aDades['cdc']='t'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					tipo_ubi                 = :tipo_ubi,
					nombre_ubi               = :nombre_ubi,
					dl                       = :dl,
					pais                     = :pais,
					region                   = :region,
					status                   = :status,
					f_status                 = :f_status,
					sv                       = :sv,
					sf                       = :sf,
					tipo_ctr                 = :tipo_ctr,
					tipo_labor               = :tipo_labor,
					cdc                      = :cdc,
					id_ctr_padre              = :id_ctr_padre,
					n_buzon                  = :n_buzon,
					num_pi                   = :num_pi,
					num_cartas               = :num_cartas,
					observ                   = :observ,
					num_habit_indiv          = :num_habit_indiv,
					plazas                   = :plazas,
					id_zona                  = :id_zona,
					num_cartas_mensuales     = :num_cartas_mensuales";
			//print_r($aDades);
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'CentroDl.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'CentroDl.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			$campos="(tipo_ubi,nombre_ubi,dl,pais,region,status,f_status,sv,sf,tipo_ctr,tipo_labor,cdc,id_ctr_padre,n_buzon,num_pi,num_cartas,observ,num_habit_indiv,plazas,id_zona,num_cartas_mensuales)";
			$valores="(:tipo_ubi,:nombre_ubi,:dl,:pais,:region,:status,:f_status,:sv,:sf,:tipo_ctr,:tipo_labor,:cdc,:id_ctr_padre,:n_buzon,:num_pi,:num_cartas,:observ,:num_habit_indiv,:plazas,:id_zona,:num_cartas_mensuales)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'CentroDl.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'CentroDl.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_auto'] = $oDbl->lastInsertId('u_centros_dl_id_auto_seq');
			$aDades['id_ubi'] = $oDbl->query("SELECT id_ubi FROM $nom_tabla WHERE id_auto =".$aDades['id_auto'])->fetchColumn();
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
		if (isset($this->iid_ubi)) {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
				$sClauError = 'CentroDl.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\PDO::FETCH_ASSOC);
			switch ($que) {
				case 'tot':
					$this->aDades=$aDades;
					break;
				case 'guardar':
					if (!$qRs->rowCount()) return false;
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_ubi='$this->iid_ubi'")) === false) {
			$sClauError = 'CentroDl.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		//print_r($aDades);
		if (!is_array($aDades)) return;
		if (array_key_exists('tipo_ubi',$aDades)) $this->setTipo_ubi($aDades['tipo_ubi']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('nombre_ubi',$aDades)) $this->setNombre_ubi($aDades['nombre_ubi']);
		if (array_key_exists('dl',$aDades)) $this->setDl($aDades['dl']);
		if (array_key_exists('pais',$aDades)) $this->setPais($aDades['pais']);
		if (array_key_exists('region',$aDades)) $this->setRegion($aDades['region']);
		if (array_key_exists('status',$aDades)) $this->setStatus($aDades['status']);
		if (array_key_exists('f_status',$aDades)) $this->setF_status($aDades['f_status']);
		if (array_key_exists('sv',$aDades)) $this->setSv($aDades['sv']);
		if (array_key_exists('sf',$aDades)) $this->setSf($aDades['sf']);
		if (array_key_exists('tipo_ctr',$aDades)) $this->setTipo_ctr($aDades['tipo_ctr']);
		if (array_key_exists('tipo_labor',$aDades)) $this->setTipo_labor($aDades['tipo_labor']);
		if (array_key_exists('cdc',$aDades)) $this->setCdc($aDades['cdc']);
		if (array_key_exists('id_ctr_padre',$aDades)) $this->setId_ctr_padre($aDades['id_ctr_padre']);
		if (array_key_exists('n_buzon',$aDades)) $this->setN_buzon($aDades['n_buzon']);
		if (array_key_exists('num_pi',$aDades)) $this->setNum_pi($aDades['num_pi']);
		if (array_key_exists('num_cartas',$aDades)) $this->setNum_cartas($aDades['num_cartas']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('num_habit_indiv',$aDades)) $this->setNum_habit_indiv($aDades['num_habit_indiv']);
		if (array_key_exists('plazas',$aDades)) $this->setPlazas($aDades['plazas']);
		if (array_key_exists('id_zona',$aDades)) $this->setId_zona($aDades['id_zona']);
		if (array_key_exists('num_cartas_mensuales',$aDades)) $this->setNum_cartas_mensuales($aDades['num_cartas_mensuales']);
	}

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	 * Recupera l'atribut in_buzon de CentroDl
	 *
	 * @return integer in_buzon
	 */
	function getN_buzon() {
		if (!isset($this->in_buzon)) {
			$this->DBCarregar();
		}
		return $this->in_buzon;
	}
	/**
	 * estableix el valor de l'atribut in_buzon de CentroDl
	 *
	 * @param integer in_buzon='' optional
	 */
	function setN_buzon($in_buzon='') {
		$this->in_buzon = $in_buzon;
	}
	/**
	 * Recupera l'atribut inum_pi de CentroDl
	 *
	 * @return integer inum_pi
	 */
	function getNum_pi() {
		if (!isset($this->inum_pi)) {
			$this->DBCarregar();
		}
		return $this->inum_pi;
	}
	/**
	 * estableix el valor de l'atribut inum_pi de CentroDl
	 *
	 * @param integer inum_pi='' optional
	 */
	function setNum_pi($inum_pi='') {
		$this->inum_pi = $inum_pi;
	}
	/**
	 * Recupera l'atribut inum_cartas de CentroDl
	 *
	 * @return integer inum_cartas
	 */
	function getNum_cartas() {
		if (!isset($this->inum_cartas)) {
			$this->DBCarregar();
		}
		return $this->inum_cartas;
	}
	/**
	 * estableix el valor de l'atribut inum_cartas de CentroDl
	 *
	 * @param integer inum_cartas='' optional
	 */
	function setNum_cartas($inum_cartas='') {
		$this->inum_cartas = $inum_cartas;
	}
	/**
	 * Recupera l'atribut sobserv de CentroDl
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
	 * estableix el valor de l'atribut sobserv de CentroDl
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut inum_habit_indiv de CentroDl
	 *
	 * @return integer inum_habit_indiv
	 */
	function getNum_habit_indiv() {
		if (!isset($this->inum_habit_indiv)) {
			$this->DBCarregar();
		}
		return $this->inum_habit_indiv;
	}
	/**
	 * estableix el valor de l'atribut inum_habit_indiv de CentroDl
	 *
	 * @param integer inum_habit_indiv='' optional
	 */
	function setNum_habit_indiv($inum_habit_indiv='') {
		$this->inum_habit_indiv = $inum_habit_indiv;
	}
	/**
	 * Recupera l'atribut iplazas de CentroDl
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
	 * estableix el valor de l'atribut iplazas de CentroDl
	 *
	 * @param integer iplazas='' optional
	 */
	function setPlazas($iplazas='') {
		$this->iplazas = $iplazas;
	}
	/**
	 * Recupera l'atribut iid_zona de CentroDl
	 *
	 * @return integer iid_zona
	 */
	function getId_zona() {
		if (!isset($this->iid_zona)) {
			$this->DBCarregar();
		}
		return $this->iid_zona;
	}
	/**
	 * estableix el valor de l'atribut iid_zona de CentroDl
	 *
	 * @param integer iid_zona='' optional
	 */
	function setId_zona($iid_zona='') {
		$this->iid_zona = $iid_zona;
	}
	/**
	 * Recupera l'atribut inum_cartas_mensuales de CentroDl
	 *
	 * @return integer inum_cartas_mensuales
	 */
	function getNum_cartas_mensuales() {
		if (!isset($this->inum_cartas_mensuales)) {
			$this->DBCarregar();
		}
		return $this->inum_cartas_mensuales;
	}
	/**
	 * estableix el valor de l'atribut inum_cartas_mensuales de CentroDl
	 *
	 * @param integer inum_cartas_mensuales='' optional
	 */
	function setNum_cartas_mensuales($inum_cartas_mensuales='') {
		$this->inum_cartas_mensuales = $inum_cartas_mensuales;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oCentroooDlSet = new core\Set();

		$oCentroooDlSet->add($this->getDatosTipo_ubi());
		$oCentroooDlSet->add($this->getDatosNombre_ubi());
		$oCentroooDlSet->add($this->getDatosDl());
		$oCentroooDlSet->add($this->getDatosPais());
		$oCentroooDlSet->add($this->getDatosRegion());
		$oCentroooDlSet->add($this->getDatosStatus());
		$oCentroooDlSet->add($this->getDatosF_status());
		$oCentroooDlSet->add($this->getDatosSv());
		$oCentroooDlSet->add($this->getDatosSf());
		$oCentroooDlSet->add($this->getDatosTipo_ctr());
		$oCentroooDlSet->add($this->getDatosTipo_labor());
		$oCentroooDlSet->add($this->getDatosCdc());
		$oCentroooDlSet->add($this->getDatosId_ctr_padre());
		$oCentroooDlSet->add($this->getDatosN_buzon());
		$oCentroooDlSet->add($this->getDatosNum_pi());
		$oCentroooDlSet->add($this->getDatosNum_cartas());
		$oCentroooDlSet->add($this->getDatosObserv());
		$oCentroooDlSet->add($this->getDatosNum_habit_indiv());
		$oCentroooDlSet->add($this->getDatosPlazas());
		$oCentroooDlSet->add($this->getDatosId_zona());
		$oCentroooDlSet->add($this->getDatosNum_cartas_mensuales());
		return $oCentroooDlSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut in_buzon de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosN_buzon() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'n_buzon'));
		$oDatosCampo->setEtiqueta(_("n_buzon"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_pi de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNum_pi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_pi'));
		$oDatosCampo->setEtiqueta(_("num_pi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_cartas de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNum_cartas() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_cartas'));
		$oDatosCampo->setEtiqueta(_("num_cartas"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_habit_indiv de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNum_habit_indiv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_habit_indiv'));
		$oDatosCampo->setEtiqueta(_("num_habit_indiv"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iplazas de CentroooDl
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
	/**
	 * Recupera les propietats de l'atribut iid_zona de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_zona() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_zona'));
		$oDatosCampo->setEtiqueta(_("id_zona"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_cartas_mensuales de CentroooDl
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosNum_cartas_mensuales() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_cartas_mensuales'));
		$oDatosCampo->setEtiqueta(_("num_cartas_mensuales"));
		return $oDatosCampo;
	}
}
?>
