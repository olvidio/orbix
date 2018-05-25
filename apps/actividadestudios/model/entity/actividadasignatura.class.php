<?php
namespace actividadestudios\model\entity;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_asignaturas_activ_all
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */
/**
 * Classe que implementa l'entitat d_asignaturas_activ_all
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 14/11/2014
 */
class ActividadAsignatura Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ActividadAsignatura
	 *
	 * @var array
	 */
	protected $aPrimary_key;

	/**
	 * aDades de ActividadAsignatura
	 *
	 * @var array
	 */
	 protected  $aDades;

	/**
	 * Id_activ de ActividadAsignatura
	 *
	 * @var integer
	 */
	protected $iid_activ;
	/**
	 * Id_asignatura de ActividadAsignatura
	 *
	 * @var integer
	 */
	 protected  $iid_asignatura;
	/**
	 * Id_profesor de ActividadAsignatura
	 *
	 * @var integer
	 */
	 protected $iid_profesor;
	/**
	 * Avis_profesor de ActividadAsignatura
	 *
	 * @var string
	 */
	 protected $savis_profesor;
	/**
	 * Tipo de ActividadAsignatura
	 *
	 * @var string
	 */
	 protected $stipo;
	/**
	 * F_ini de ActividadAsignatura
	 *
	 * @var date
	 */
	 protected $df_ini;
	/**
	 * F_fin de ActividadAsignatura
	 *
	 * @var date
	 */
	 protected $df_fin;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ActividadAsignatura
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ActividadAsignatura
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
	 * @param integer|array iid_activ,iid_asignatura
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_activ') && $val_id !== '') $this->iid_activ = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_asignaturas_activ_all');
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
		$aDades['id_profesor'] = $this->iid_profesor;
		$aDades['avis_profesor'] = $this->savis_profesor;
		$aDades['tipo'] = $this->stipo;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['f_fin'] = $this->df_fin;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_profesor              = :id_profesor,
					avis_profesor            = :avis_profesor,
					tipo                     = :tipo,
					f_ini                    = :f_ini,
					f_fin                    = :f_fin";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura'")) === false) {
				$sClauError = 'ActividadAsignatura.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ActividadAsignatura.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_activ, $this->iid_asignatura);
			$campos="(id_activ,id_asignatura,id_profesor,avis_profesor,tipo,f_ini,f_fin)";
			$valores="(:id_activ,:id_asignatura,:id_profesor,:avis_profesor,:tipo,:f_ini,:f_fin)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ActividadAsignatura.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ActividadAsignatura.insertar.execute';
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
		if (isset($this->iid_activ) && isset($this->iid_asignatura)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura'")) === false) {
				$sClauError = 'ActividadAsignatura.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_activ='$this->iid_activ' AND id_asignatura='$this->iid_asignatura'")) === false) {
			$sClauError = 'ActividadAsignatura.eliminar';
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
		if (!is_array($aDades)) return;
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('id_asignatura',$aDades)) $this->setId_asignatura($aDades['id_asignatura']);
		if (array_key_exists('id_profesor',$aDades)) $this->setId_profesor($aDades['id_profesor']);
		if (array_key_exists('avis_profesor',$aDades)) $this->setAvis_profesor($aDades['avis_profesor']);
		if (array_key_exists('tipo',$aDades)) $this->setTipo($aDades['tipo']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini']);
		if (array_key_exists('f_fin',$aDades)) $this->setF_fin($aDades['f_fin']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ActividadAsignatura en un array
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
	 * Recupera las claus primàries de ActividadAsignatura en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_activ' => $this->iid_activ,'id_asignatura' => $this->iid_asignatura);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_activ de ActividadAsignatura
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
	 * estableix el valor de l'atribut iid_activ de ActividadAsignatura
	 *
	 * @param integer iid_activ
	 */
	function setId_activ($iid_activ) {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut iid_asignatura de ActividadAsignatura
	 *
	 * @return integer iid_asignatura
	 */
	function getId_asignatura() {
		if (!isset($this->iid_asignatura)) {
			$this->DBCarregar();
		}
		return $this->iid_asignatura;
	}
	/**
	 * estableix el valor de l'atribut iid_asignatura de ActividadAsignatura
	 *
	 * @param integer iid_asignatura
	 */
	function setId_asignatura($iid_asignatura) {
		$this->iid_asignatura = $iid_asignatura;
	}
	/**
	 * Recupera l'atribut iid_profesor de ActividadAsignatura
	 *
	 * @return integer iid_profesor
	 */
	function getId_profesor() {
		if (!isset($this->iid_profesor)) {
			$this->DBCarregar();
		}
		return $this->iid_profesor;
	}
	/**
	 * estableix el valor de l'atribut iid_profesor de ActividadAsignatura
	 *
	 * @param integer iid_profesor='' optional
	 */
	function setId_profesor($iid_profesor='') {
		$this->iid_profesor = $iid_profesor;
	}
	/**
	 * Recupera l'atribut savis_profesor de ActividadAsignatura
	 *
	 * @return string savis_profesor
	 */
	function getAvis_profesor() {
		if (!isset($this->savis_profesor)) {
			$this->DBCarregar();
		}
		return $this->savis_profesor;
	}
	/**
	 * estableix el valor de l'atribut savis_profesor de ActividadAsignatura
	 *
	 * @param string savis_profesor='' optional
	 */
	function setAvis_profesor($savis_profesor='') {
		$this->savis_profesor = $savis_profesor;
	}
	/**
	 * Recupera l'atribut stipo de ActividadAsignatura
	 *
	 * @return string stipo
	 */
	function getTipo() {
		if (!isset($this->stipo)) {
			$this->DBCarregar();
		}
		return $this->stipo;
	}
	/**
	 * estableix el valor de l'atribut stipo de ActividadAsignatura
	 *
	 * @param string stipo='' optional
	 */
	function setTipo($stipo='') {
		$this->stipo = $stipo;
	}
	/**
	 * Recupera l'atribut df_ini de ActividadAsignatura
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
	 * estableix el valor de l'atribut df_ini de ActividadAsignatura
	 *
	 * @param date df_ini='' optional
	 */
	function setF_ini($df_ini='') {
		$this->df_ini = $df_ini;
	}
	/**
	 * Recupera l'atribut df_fin de ActividadAsignatura
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
	 * estableix el valor de l'atribut df_fin de ActividadAsignatura
	 *
	 * @param date df_fin='' optional
	 */
	function setF_fin($df_fin='') {
		$this->df_fin = $df_fin;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActividadAsignaturaSet = new core\Set();

		$oActividadAsignaturaSet->add($this->getDatosId_profesor());
		$oActividadAsignaturaSet->add($this->getDatosAvis_profesor());
		$oActividadAsignaturaSet->add($this->getDatosTipo());
		$oActividadAsignaturaSet->add($this->getDatosF_ini());
		$oActividadAsignaturaSet->add($this->getDatosF_fin());
		return $oActividadAsignaturaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_profesor de ActividadAsignatura
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_profesor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_profesor'));
		$oDatosCampo->setEtiqueta(_("id_profesor"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut savis_profesor de ActividadAsignatura
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosAvis_profesor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'avis_profesor'));
		$oDatosCampo->setEtiqueta(_("avis_profesor"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo de ActividadAsignatura
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo'));
		$oDatosCampo->setEtiqueta(_("tipo"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de ActividadAsignatura
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ini'));
		$oDatosCampo->setEtiqueta(_("f_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_fin de ActividadAsignatura
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosF_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_fin'));
		$oDatosCampo->setEtiqueta(_("f_fin"));
		return $oDatosCampo;
	}
}
?>