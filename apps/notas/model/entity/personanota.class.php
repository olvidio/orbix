<?php
namespace notas\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula e_notas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat e_notas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class PersonaNota Extends core\ClasePropiedades {
	
	// tipo_acta constants.
    const FORMATO_ACTA     = 1; // Acta.
    const FORMATO_CERTIFICADO    = 2; // Certificado.
	
    const EPOCA_CA     = 1; // ca verano.
    const EPOCA_INVIERNO   = 2; // semestre invierno.
    const EPOCA_OTRO   = 3; // sin especificar.
	
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de PersonaNota
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de PersonaNota
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_schema de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_schema;
	/**
	 * Id_nom de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_nom;
	/**
	 * Id_nivel de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_nivel;
	/**
	 * Id_asignatura de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_asignatura;
	/**
	 * Id_situacion de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_situacion;
	/**
	 * Acta de PersonaNota
	 *
	 * @var string
	 */
	 protected $sacta;
	/**
	 * F_acta de PersonaNota
	 *
	 * @var web\DateTimeLocal
	 */
	 protected $df_acta;
	/**
	 * Detalle de PersonaNota
	 *
	 * @var string
	 */
	 protected $sdetalle;
	/**
	 * Preceptor de PersonaNota
	 *
	 * @var boolean
	 */
	 protected $bpreceptor;
	/**
	 * Id_preceptor de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_preceptor;
	/**
	 * Epoca de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iepoca;
	/**
	 * Id_activ de PersonaNota
	 *
	 * @var integer
	 */
	 protected $iid_activ;
 	/**
	 * Nota_num de Nota
	 *
	 * @var integer
	 */
	 protected $inota_num;
	/**
	 * Nota_max de Nota
	 *
	 * @var integer
	 */
	 protected $inota_max;
	/**
	 * Tipo_acta de Nota
	 *
	 * @var integer
	 */
	 protected $itipo_acta;

	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de PersonaNota
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de PersonaNota
	 *
	 * @var string
	 */
	 protected $sNomTabla;
	/**
	 * Nota_txt de Nota
	 *
	 * @var string
	 */
	 protected $sNota_txt;
 	 /* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array iid_nom,iid_nivel
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_asignatura') && $val_id !== '') $this->iid_asignatura = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nivel') && $val_id !== '') $this->iid_nivel = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_notas');
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
		$aDades['id_nivel'] = $this->iid_nivel;
		$aDades['id_asignatura'] = $this->iid_asignatura;
		$aDades['id_situacion'] = $this->iid_situacion;
		$aDades['acta'] = $this->sacta;
		$aDades['f_acta'] = $this->df_acta;
		$aDades['detalle'] = $this->sdetalle;
		$aDades['preceptor'] = $this->bpreceptor;
		$aDades['id_preceptor'] = $this->iid_preceptor;
		$aDades['epoca'] = $this->iepoca;
		$aDades['id_activ'] = $this->iid_activ;
		$aDades['nota_num'] = $this->inota_num;
		$aDades['nota_max'] = $this->inota_max;
		$aDades['tipo_acta'] = $this->itipo_acta;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['preceptor'] = ($aDades['preceptor'] === 't')? 'true' : $aDades['preceptor'];
		if ( filter_var( $aDades['preceptor'], FILTER_VALIDATE_BOOLEAN)) { $aDades['preceptor']='t'; } else { $aDades['preceptor']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_nivel	             = :id_nivel,
					id_asignatura            = :id_asignatura,
					id_situacion             = :id_situacion,
					acta                     = :acta,
					f_acta                   = :f_acta,
					detalle                  = :detalle,
					preceptor                = :preceptor,
					id_preceptor             = :id_preceptor,
					epoca                    = :epoca,
					id_activ                 = :id_activ,
					nota_num                 = :nota_num,
					nota_max                 = :nota_max,
					tipo_acta                = :tipo_acta";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_nom=$this->iid_nom AND id_nivel=$this->iid_nivel ")) === false) {
				$sClauError = 'PersonaNota.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'PersonaNota.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_schema, $this->iid_nom);
			$campos="(id_schema,id_nom,id_nivel,id_asignatura,id_situacion,acta,f_acta,detalle,preceptor,id_preceptor,epoca,id_activ,nota_num,nota_max,tipo_acta)";
			$valores="(:id_schema,:id_nom,:id_nivel,:id_asignatura,:id_situacion,:acta,:f_acta,:detalle,:preceptor,:id_preceptor,:epoca,:id_activ,:nota_num,:nota_max,:tipo_acta)";		
			//echo "INSERT INTO $nom_tabla $campos VALUES $valores";
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'PersonaNota.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'PersonaNota.insertar.execute';
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
		if (isset($this->iid_nom) && isset($this->iid_nivel)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_nom=$this->iid_nom AND id_nivel=$this->iid_nivel ")) === false) {
				$sClauError = 'PersonaNota.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_nom=$this->iid_nom AND id_nivel='$this->iid_nivel'")) === false) {
			$sClauError = 'PersonaNota.eliminar';
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
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('id_nivel',$aDades)) $this->setId_nivel($aDades['id_nivel']);
		if (array_key_exists('id_asignatura',$aDades)) $this->setId_asignatura($aDades['id_asignatura']);
		if (array_key_exists('id_situacion',$aDades)) $this->setId_situacion($aDades['id_situacion']);
		// la fecha debe estar antes del acta por si hay que usar la funcion inventarActa.
		if (array_key_exists('f_acta',$aDades)) $this->setF_acta($aDades['f_acta'],$convert);
		if (array_key_exists('acta',$aDades)) $this->setActa($aDades['acta']);
		if (array_key_exists('detalle',$aDades)) $this->setDetalle($aDades['detalle']);
		if (array_key_exists('preceptor',$aDades)) $this->setPreceptor($aDades['preceptor']);
		if (array_key_exists('id_preceptor',$aDades)) $this->setId_preceptor($aDades['id_preceptor']);
		if (array_key_exists('epoca',$aDades)) $this->setEpoca($aDades['epoca']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('nota_num',$aDades)) $this->setNota_num($aDades['nota_num']);
		if (array_key_exists('nota_max',$aDades)) $this->setNota_max($aDades['nota_max']);
		if (array_key_exists('tipo_acta',$aDades)) $this->setTipo_acta($aDades['tipo_acta']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_nom('');
		$this->setId_nivel('');
		$this->setId_asignatura('');
		$this->setId_situacion('');
		// la fecha debe estar antes del acta por si hay que usar la funcion inventarActa.
		$this->setF_acta('');
		$this->setActa('');
		$this->setDetalle('');
		$this->setPreceptor('');
		$this->setId_preceptor('');
		$this->setEpoca('');
		$this->setId_activ('');
		$this->setNota_num('');
		$this->setNota_max('');
		$this->setTipo_acta('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de PersonaNota en un array
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
	 * Recupera las claus primàries de PersonaNota en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_nom' => $this->iid_nom,'id_nivel' => $this->iid_nivel);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de PersonaNota en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
	            if (($nom_id == 'id_nivel') && $val_id !== '') $this->iid_nivel = (int)$val_id; // evitem SQL injection fent cast a integer
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut iid_nom de PersonaNota
	 *
	 * @return integer iid_nom
	 */
	function getId_nom() {
		if (!isset($this->iid_nom)) {
			$this->DBCarregar();
		}
		return $this->iid_nom;
	}
	/**
	 * estableix el valor de l'atribut iid_nom de PersonaNota
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_nivel de PersonaNota
	 *
	 * @return integer iid_nivel
	 */
	function getId_nivel() {
		if (!isset($this->iid_nivel)) {
			$this->DBCarregar();
		}
		return $this->iid_nivel;
	}
	/**
	 * estableix el valor de l'atribut iid_nivel de PersonaNota
	 *
	 * @param integer iid_nivel
	 */
	function setId_nivel($iid_nivel) {
		$this->iid_nivel = $iid_nivel;
	}
	/**
	 * Recupera l'atribut iid_asignatura de PersonaNota
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
	 * estableix el valor de l'atribut iid_asignatura de PersonaNota
	 *
	 * @param integer iid_asignatura='' optional
	 */
	function setId_asignatura($iid_asignatura='') {
		$this->iid_asignatura = $iid_asignatura;
	}
	/**
	 * Recupera l'atribut iid_situacion de PersonaNota
	 *
	 * @return integer iid_situacion
	 */
	function getId_situacion() {
		if (!isset($this->iid_situacion)) {
			$this->DBCarregar();
		}
		return $this->iid_situacion;
	}
	/**
	 * estableix el valor de l'atribut iid_situacion de PersonaNota
	 *
	 * @param integer iid_situacion='' optional
	 */
	function setId_situacion($iid_situacion='') {
		$this->iid_situacion = $iid_situacion;
	}
	/**
	 * Recupera l'atribut sacta de PersonaNota
	 *
	 * @return string sacta
	 */
	function getActa() {
		if (!isset($this->sacta)) {
			$this->DBCarregar();
		}
		return $this->sacta;
	}
	/**
	 * estableix el valor de l'atribut sacta de PersonaNota
	 *
	 * @param string sacta='' optional
	 */
	function setActa($sacta='') {
		$this->sacta = $sacta;
	}
	/**
	 * Recupera l'atribut df_acta de PersonaNota
	 *
	 * @return web\DateTimeLocal df_acta
	 */
	function getF_acta() {
	    if (!isset($this->df_acta)) {
	        $this->DBCarregar();
	    }
	    if (empty($this->df_acta)) {
	    	return new web\NullDateTimeLocal();
	    }
	    $oConverter = new core\Converter('date', $this->df_acta);
	    return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_acta de PersonaNota
	* Si df_acta es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
	* Si convert es false, df_acta debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	*
	* @param date|string df_acta='' optional.
	* @param boolean convert=true optional. Si es false, df_acta debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_acta($df_acta='',$convert=true) {
	    if ($convert === true && !empty($df_acta)) {
	        $oConverter = new core\Converter('date', $df_acta);
	        $this->df_acta = $oConverter->toPg();
	    } else {
	        $this->df_acta = $df_acta;
	    }
	}
	/**
	 * Recupera l'atribut sdetalle de PersonaNota
	 *
	 * @return string sdetalle
	 */
	function getDetalle() {
		if (!isset($this->sdetalle)) {
			$this->DBCarregar();
		}
		return $this->sdetalle;
	}
	/**
	 * estableix el valor de l'atribut sdetalle de PersonaNota
	 *
	 * @param string sdetalle='' optional
	 */
	function setDetalle($sdetalle='') {
		$this->sdetalle = $sdetalle;
	}
	/**
	 * Recupera l'atribut bpreceptor de PersonaNota
	 *
	 * @return boolean bpreceptor
	 */
	function getPreceptor() {
		if (!isset($this->bpreceptor)) {
			$this->DBCarregar();
		}
		return $this->bpreceptor;
	}
	/**
	 * estableix el valor de l'atribut bpreceptor de PersonaNota
	 *
	 * @param boolean bpreceptor='f' optional
	 */
	function setPreceptor($bpreceptor='f') {
		$this->bpreceptor = $bpreceptor;
	}
	/**
	 * Recupera l'atribut iid_preceptor de PersonaNota
	 *
	 * @return integer iid_preceptor
	 */
	function getId_preceptor() {
		if (!isset($this->iid_preceptor)) {
			$this->DBCarregar();
		}
		return $this->iid_preceptor;
	}
	/**
	 * estableix el valor de l'atribut iid_preceptor de PersonaNota
	 *
	 * @param integer iid_preceptor='' optional
	 */
	function setId_preceptor($iid_preceptor='') {
		$this->iid_preceptor = $iid_preceptor;
	}
	/**
	 * Recupera l'atribut iepoca de PersonaNota
	 *
	 * @return integer iepoca
	 */
	function getEpoca() {
		if (!isset($this->iepoca)) {
			$this->DBCarregar();
		}
		return $this->iepoca;
	}
	/**
	 * estableix el valor de l'atribut iepoca de PersonaNota
	 *
	 * @param integer iepoca='' optional
	 */
	function setEpoca($iepoca='') {
		$this->iepoca = $iepoca;
	}
	/**
	 * Recupera l'atribut iid_activ de PersonaNota
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
	 * estableix el valor de l'atribut iid_activ de PersonaNota
	 *
	 * @param integer iid_activ='' optional
	 */
	function setId_activ($iid_activ='') {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut inota_num de PersonaNota
	 *
	 * @return integer inota_num
	 */
	function getNota_num() {
		if (!isset($this->inota_num)) {
			$this->DBCarregar();
		}
		return $this->inota_num;
	}
	/**
	 * estableix el valor de l'atribut inota_num de PersonaNota
	 *
	 * @param integer inota_num='' optional
	 */
	function setNota_num($inota_num='') {
		// adminto ',' como separador decimal.
		$inota_num = str_replace(",", ".", $inota_num);
		$this->inota_num = $inota_num;
	}
	/**
	 * Recupera l'atribut inota_max de PersonaNota
	 *
	 * @return integer inota_max
	 */
	function getNota_max() {
		if (!isset($this->inota_max)) {
			$this->DBCarregar();
		}
		return $this->inota_max;
	}
	/**
	 * estableix el valor de l'atribut inota_max de PersonaNota
	 *
	 * @param integer inota_max='' optional
	 */
	function setNota_max($inota_max='') {
		$this->inota_max = $inota_max;
	}
	/**
	 * Recupera l'atribut itipo_acta de PersonaNota
	 *
	 * @return integer itipo_acta
	 */
	function getTipo_acta() {
		if (!isset($this->itipo_acta)) {
			$this->DBCarregar();
		}
		return $this->itipo_acta;
	}
	/**
	 * estableix el valor de l'atribut itipo_acta de PersonaNota
	 *
	 * @param integer itipo_acta='' optional
	 */
	function setTipo_acta($itipo_acta='') {
		$this->itipo_acta = $itipo_acta;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/
	/**
	 * Recupera la nota en forma de text
	 *
	 * @return string snota_txt
	 */
	function getNota_txt() {
		$nota_txt = 'Hollla';
		$id_situacion = $this->getId_situacion();
		switch ($id_situacion) {
			case '3': // Magna
				$nota_txt = 'Magna cum laude (8,6-9,5/10)';
				break;
			case '4': // Summa
				$nota_txt = 'Summa cum laude (9,6-10/10)';
				break;
			case '10': // Nota numérica
				$num = $this->getNota_num();
				//$a = new \NumberFormatter("es_ES.UTF-8", \NumberFormatter::DECIMAL);
				// SI dejo el locale en blanco coje el que se ha definido por defecto en el usuario.
				$a = new \NumberFormatter("", \NumberFormatter::DECIMAL);
				$num_local = $a->format($num);
                $max = $this->getNota_max();
                $nota_txt = $num_local.'/'.$max;
				if ($max == 10) {
					if ($num > 9.5) {
						$nota_txt = _("Summa cum laude") . ' (' .$nota_txt .')'; 
					} elseif ($num > 8.5) {
						$nota_txt = _("Magna cum laude") . ' (' .$nota_txt .')'; 
					} elseif ($num > 7.5) {
						$nota_txt = _("Cum laude") . ' (' .$nota_txt .')'; 
					} elseif ($num > 6.5) {
						$nota_txt = _("Bene probatus") . ' (' .$nota_txt .')'; 
					} elseif ($num >= 6) {
						$nota_txt = _("Probatus") . ' (' .$nota_txt .')'; 
					} else {
						$nota_txt = _("Non probatus") . ' (' .$nota_txt .')'; 
					}
				}
				break;
			default:
				$oNota = new Nota($id_situacion);
				$nota_txt = $oNota->getDescripcion();
				break;
		}
		return $nota_txt;
	}

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oPersonaNotaSet = new core\Set();

		$oPersonaNotaSet->add($this->getDatosId_asignatura());
		$oPersonaNotaSet->add($this->getDatosId_situacion());
		$oPersonaNotaSet->add($this->getDatosActa());
		$oPersonaNotaSet->add($this->getDatosF_acta());
		$oPersonaNotaSet->add($this->getDatosDetalle());
		$oPersonaNotaSet->add($this->getDatosPreceptor());
		$oPersonaNotaSet->add($this->getDatosId_preceptor());
		$oPersonaNotaSet->add($this->getDatosEpoca());
		$oPersonaNotaSet->add($this->getDatosId_activ());
		$oPersonaNotaSet->add($this->getDatosNota_num());
		$oPersonaNotaSet->add($this->getDatosNota_max());
		$oPersonaNotaSet->add($this->getDatosTipo_acta());
		return $oPersonaNotaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_asignatura de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_asignatura() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_asignatura'));
		$oDatosCampo->setEtiqueta(_("id_asignatura"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_situacion de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_situacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_situacion'));
		$oDatosCampo->setEtiqueta(_("id_situacion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sacta de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosActa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'acta'));
		$oDatosCampo->setEtiqueta(_("acta"));
		// Las actcas de otras r sólo tienen la sigla de la r
		$oDatosCampo->setRegExp("/^(\?|\w{1,6}\??)(\s+([0-9]{0,3})\/([0-9]{2})\??)?$/");
		$txt = "No tienen el formato: 'dlxx nn/aa'. Debe tener sólo un espacio.";
		$txt .= "\nSi sólo se sabe la region/dl poner la sigla.\nSi no se sabe nada poner ?.\n";
		$oDatosCampo->setRegExpText(_($txt));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_acta de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_acta() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_acta'));
		$oDatosCampo->setEtiqueta(_("fecha acta"));
        $oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdetalle de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDetalle() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'detalle'));
		$oDatosCampo->setEtiqueta(_("detalle"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpreceptor de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPreceptor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'preceptor'));
		$oDatosCampo->setEtiqueta(_("preceptor"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_preceptor de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_preceptor() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_preceptor'));
		$oDatosCampo->setEtiqueta(_("id_preceptor"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iepoca de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEpoca() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'epoca'));
		$oDatosCampo->setEtiqueta(_("época"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_activ de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_activ'));
		$oDatosCampo->setEtiqueta(_("id_activ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inota_num de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNota_num() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nota_num'));
		$oDatosCampo->setEtiqueta(_("nota num"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inota_max de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNota_max() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nota_max'));
		$oDatosCampo->setEtiqueta(_("nota max"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut itipo_acta de PersonaNota
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_acta() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_acta'));
		$oDatosCampo->setEtiqueta(_("tipo de acta"));
		return $oDatosCampo;
	}

}