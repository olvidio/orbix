<?php
namespace ubis\model\entity;
use core;
/**
 * Classe que implementa l'entitat u_dir_cdc_ex
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

class DireccionCdcEx Extends DireccionCdc {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * Id_auto de Direccion
	 *
	 * @var integer
	 */
	 private $iid_auto;

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_direccion
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBRC'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				$nom_id='i'.$nom_id; //imagino que es un integer
				if ($val_id !== '') $this->$nom_id = intval($val_id); // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_direccion = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('id_direccion' => $this->iid_direccion);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('u_dir_cdc_ex');
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
		$aDades['direccion'] = $this->sdireccion;
		$aDades['c_p'] = $this->sc_p;
		$aDades['poblacion'] = $this->spoblacion;
		$aDades['provincia'] = $this->sprovincia;
		$aDades['a_p'] = $this->sa_p;
		$aDades['pais'] = $this->spais;
		$aDades['f_direccion'] = $this->df_direccion;
		$aDades['observ'] = $this->sobserv;
		$aDades['cp_dcha'] = $this->bcp_dcha;
		$aDades['latitud'] = $this->ilatitud;
		$aDades['longitud'] = $this->ilongitud;
		$aDades['plano_doc'] = $this->iplano_doc;
		$aDades['plano_nom'] = $this->splano_nom;
		$aDades['plano_extension'] = $this->splano_extension;
		$aDades['nom_sede'] = $this->snom_sede;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['cp_dcha']) ) { $aDades['cp_dcha']='true'; } else { $aDades['cp_dcha']='false'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					direccion                = :direccion,
					c_p                      = :c_p,
					poblacion                = :poblacion,
					provincia                = :provincia,
					a_p                      = :a_p,
					pais                     = :pais,
					f_direccion              = :f_direccion,
					observ                   = :observ,
					cp_dcha                  = :cp_dcha,
					latitud                  = :latitud,
					longitud                 = :longitud,
					plano_doc                = :plano_doc,
					plano_extension          = :plano_extension,
					plano_nom                = :plano_nom,
					nom_sede                 = :nom_sede";

			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_direccion='$this->iid_direccion'")) === false) {
				$sClauError = 'Direccion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Exterior.Direccion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_direccion);
			$campos="(direccion,c_p,poblacion,provincia,a_p,pais,f_direccion,observ,cp_dcha,latitud,longitud,plano_doc,plano_extension,plano_nom,nom_sede)";
			$valores="(:direccion,:c_p,:poblacion,:provincia,:a_p,:pais,:f_direccion,:observ,:cp_dcha,:latitud,:longitud,:plano_doc,:plano_extension,:plano_nom,:nom_sede)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Direccion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Direccion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$aDades['id_auto'] = $oDbl->lastInsertId('u_dir_cdc_ex_id_auto_seq');
			$aDades['id_direccion'] = $oDbl->query("SELECT id_direccion FROM $nom_tabla WHERE id_auto =".$aDades['id_auto'])->fetchColumn();
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
		if (isset($this->iid_direccion)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_direccion='$this->iid_direccion'")) === false) {
				$sClauError = 'Direccion.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_direccion='$this->iid_direccion'")) === false) {
			$sClauError = 'Direccion.eliminar';
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
	function setAllAtributes($aDades,$convert=FALSE) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_schema',$aDades)) $this->setId_schema($aDades['id_schema']);
		if (array_key_exists('id_direccion',$aDades)) $this->setId_direccion($aDades['id_direccion']);
		if (array_key_exists('direccion',$aDades)) $this->setDireccion($aDades['direccion']);
		if (array_key_exists('c_p',$aDades)) $this->setC_p($aDades['c_p']);
		if (array_key_exists('poblacion',$aDades)) $this->setPoblacion($aDades['poblacion']);
		if (array_key_exists('provincia',$aDades)) $this->setProvincia($aDades['provincia']);
		if (array_key_exists('a_p',$aDades)) $this->setA_p($aDades['a_p']);
		if (array_key_exists('pais',$aDades)) $this->setPais($aDades['pais']);
		if (array_key_exists('f_direccion',$aDades)) $this->setF_direccion($aDades['f_direccion'],$convert);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('cp_dcha',$aDades)) $this->setCp_dcha($aDades['cp_dcha']);
		if (array_key_exists('latitud',$aDades)) $this->setLatitud($aDades['latitud']);
		if (array_key_exists('longitud',$aDades)) $this->setLongitud($aDades['longitud']);
		if (array_key_exists('plano_doc',$aDades)) $this->setPlano_doc($aDades['plano_doc']);
		if (array_key_exists('plano_extension',$aDades)) $this->setPlano_extension($aDades['plano_extension']);
		if (array_key_exists('plano_nom',$aDades)) $this->setPlano_nom($aDades['plano_nom']);
		if (array_key_exists('nom_sede',$aDades)) $this->setNom_sede($aDades['nom_sede']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_direccion('');
		$this->setDireccion('');
		$this->setC_p('');
		$this->setPoblacion('');
		$this->setProvincia('');
		$this->setA_p('');
		$this->setPais('');
		$this->setF_direccion('');
		$this->setObserv('');
		$this->setCp_dcha('');
		$this->setLatitud('');
		$this->setLongitud('');
		$this->setPlano_doc('');
		$this->setPlano_extension('');
		$this->setPlano_nom('');
		$this->setNom_sede('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
}
?>
