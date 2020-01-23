<?php
namespace notas\model\entity;
use core\Converter;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula e_actas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
/**
 * Classe que implementa l'entitat e_actas
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 07/04/2014
 */
class Acta Extends core\ClasePropiedades {
    
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Acta
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Acta
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Acta de Acta
	 *
	 * @var string
	 */
	 protected $sacta;
	/**
	 * Id_asignatura de Acta
	 *
	 * @var integer
	 */
	 protected $iid_asignatura;
	/**
	 * Id_activ de Acta
	 *
	 * @var integer
	 */
	 protected $iid_activ;
	/**
	 * F_acta de Acta
	 *
	 * @var web\DateTimeLocal
	 */
	 protected $df_acta;
	/**
	 * Libro de Acta
	 *
	 * @var integer
	 */
	 protected $ilibro;
	/**
	 * Pagina de Acta
	 *
	 * @var integer
	 */
	 protected $ipagina;
	/**
	 * Linea de Acta
	 *
	 * @var integer
	 */
	 protected $ilinea;
	/**
	 * Lugar de Acta
	 *
	 * @var string
	 */
	 protected $slugar;
	/**
	 * Observ de Acta
	 *
	 * @var string
	 */
	 protected $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Acta
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Acta
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
	 * @param integer|array sacta
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDBP'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'acta') && $val_id !== '') $this->sacta = (string)$val_id; // evitem SQL injection fent cast a string
			}	
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->sacta = (string)$a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('acta' => $this->sacta);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('e_actas');
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
		$aDades['id_asignatura'] = $this->iid_asignatura;
		$aDades['id_activ'] = $this->iid_activ;
		$aDades['f_acta'] = $this->df_acta;
		$aDades['libro'] = $this->ilibro;
		$aDades['pagina'] = $this->ipagina;
		$aDades['linea'] = $this->ilinea;
		$aDades['lugar'] = $this->slugar;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					f_acta                   = :f_acta,
					libro                    = :libro,
					pagina                   = :pagina,
					linea                    = :linea,
					lugar                    = :lugar,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE acta='$this->sacta'")) === false) {
				$sClauError = 'Acta.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Acta.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->sacta);
			$campos="(acta,id_asignatura,id_activ,f_acta,libro,pagina,linea,lugar,observ)";
			$valores="(:acta,:id_asignatura,:id_activ,:f_acta,:libro,:pagina,:linea,:lugar,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'Acta.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'Acta.insertar.execute';
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
		if (isset($this->sacta)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE acta='$this->sacta'")) === false) {
				$sClauError = 'Acta.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE acta='$this->sacta'")) === false) {
			$sClauError = 'Acta.eliminar';
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
		// la fecha debe estar antes del acta por si hay que usar la funcion inventarActa.
		if (array_key_exists('f_acta',$aDades)) $this->setF_acta($aDades['f_acta'],$convert);
		if (array_key_exists('acta',$aDades)) $this->setActa($aDades['acta']);
		if (array_key_exists('id_asignatura',$aDades)) $this->setId_asignatura($aDades['id_asignatura']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('libro',$aDades)) $this->setLibro($aDades['libro']);
		if (array_key_exists('pagina',$aDades)) $this->setPagina($aDades['pagina']);
		if (array_key_exists('linea',$aDades)) $this->setLinea($aDades['linea']);
		if (array_key_exists('lugar',$aDades)) $this->setLugar($aDades['lugar']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		// la fecha debe estar antes del acta por si hay que usar la funcion inventarActa.
		$this->setF_acta('');
		$this->setActa('');
		$this->setId_asignatura('');
		$this->setId_activ('');
		$this->setLibro('');
		$this->setPagina('');
		$this->setLinea('');
		$this->setLugar('');
		$this->setObserv('');
		$this->setPrimary_key($aPK);
	}


	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Acta en un array
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
	 * Recupera las claus primàries de Acta en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('acta' => $this->sacta);
		}
		return $this->aPrimary_key;
	}
	
	/**
	 * Estableix las claus primàries de Acta en un array
	 *
	 * @return array aPrimary_key
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) {
	        $this->aPrimary_key = $a_id;
	        foreach($a_id as $nom_id=>$val_id) {
	            if (($nom_id == 'acta') && $val_id !== '') $this->sacta = $val_id;
	        }
	    }
	}
	
	/**
	 * Recupera l'atribut sacta de Acta
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
	 * inventa el valor de l'atribut sacta de Acta
	 *
	 * @param string sacta
	 */
	function inventarActa($valor,$fecha='') {
		// Se puede usar la funcion desde personaNota, por eso se puede pasar la fecha.
		$fecha = !empty($fecha)? $fecha : $this->getF_acta();
		if (empty($fecha)) {
		   	$any = '?';
			$num_acta = 'x';
		} else {
		    if (is_object($fecha)) {
		        $oData = $fecha;
		    } else {
			    $oData = web\DateTimeLocal::createFromLocal($fecha);
		    }
			$any = $oData->format('y');
			// inventar acta.
			$oGesActas = new gestorActa();
			$num_acta = 1 + $oGesActas->getUltimaActa($valor,$any);
		}
		// no sé nada
		if ( $valor == '?' ) {
			// 'dl? xx/15?';
			$valor = "dl? $num_acta/$any?";
		} else {  // solo la región o dl
			// 'region xx/15?';
			$valor = "$valor $num_acta/$any?";
		}
		return $valor;
	}
	/**
	 * estableix el valor de l'atribut sacta de Acta
	 *
	 * @param string sacta
	 */
	function setActa($sacta) {
		$this->sacta = $sacta;
	}
	/**
	 * Recupera l'atribut iid_asignatura de Acta
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
	 * estableix el valor de l'atribut iid_asignatura de Acta
	 *
	 * @param integer iid_asignatura='' optional
	 */
	function setId_asignatura($iid_asignatura='') {
		$this->iid_asignatura = $iid_asignatura;
	}
	/**
	 * Recupera l'atribut iid_activ de Acta
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
	 * estableix el valor de l'atribut iid_activ de Acta
	 *
	 * @param integer iid_activ='' optional
	 */
	function setId_activ($iid_activ='') {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut df_acta de Acta
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
	 * estableix el valor de l'atribut df_acta de Acta
	* Si df_acta es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
	* Si convert es false, df_acta debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	*
	* @param date|string df_acta='' optional.
	* @param boolean convert=true optional. Si es false, df_acta debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_acta($df_acta='',$convert=true) {
		if ($convert === true && !empty($df_acta)) {
	        $oConverter = new core\Converter('date', $df_acta);
	        $this->df_acta =$oConverter->toPg();
	    } else {
	        $this->df_acta = $df_acta;
	    }
	}
	/**
	 * Recupera l'atribut ilibro de Acta
	 *
	 * @return integer ilibro
	 */
	function getLibro() {
		if (!isset($this->ilibro)) {
			$this->DBCarregar();
		}
		return $this->ilibro;
	}
	/**
	 * estableix el valor de l'atribut ilibro de Acta
	 *
	 * @param integer ilibro='' optional
	 */
	function setLibro($ilibro='') {
		$this->ilibro = $ilibro;
	}
	/**
	 * Recupera l'atribut ipagina de Acta
	 *
	 * @return integer ipagina
	 */
	function getPagina() {
		if (!isset($this->ipagina)) {
			$this->DBCarregar();
		}
		return $this->ipagina;
	}
	/**
	 * estableix el valor de l'atribut ipagina de Acta
	 *
	 * @param integer ipagina='' optional
	 */
	function setPagina($ipagina='') {
		$this->ipagina = $ipagina;
	}
	/**
	 * Recupera l'atribut ilinea de Acta
	 *
	 * @return integer ilinea
	 */
	function getLinea() {
		if (!isset($this->ilinea)) {
			$this->DBCarregar();
		}
		return $this->ilinea;
	}
	/**
	 * estableix el valor de l'atribut ilinea de Acta
	 *
	 * @param integer ilinea='' optional
	 */
	function setLinea($ilinea='') {
		$this->ilinea = $ilinea;
	}
	/**
	 * Recupera l'atribut slugar de Acta
	 *
	 * @return string slugar
	 */
	function getLugar() {
		if (!isset($this->slugar)) {
			$this->DBCarregar();
		}
		return $this->slugar;
	}
	/**
	 * estableix el valor de l'atribut slugar de Acta
	 *
	 * @param string slugar='' optional
	 */
	function setLugar($slugar='') {
		$this->slugar = $slugar;
	}
	/**
	 * Recupera l'atribut sobserv de Acta
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
	 * estableix el valor de l'atribut sobserv de Acta
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oActaSet = new core\Set();

		$oActaSet->add($this->getDatosActa());
		$oActaSet->add($this->getDatosId_asignatura());
		$oActaSet->add($this->getDatosId_activ());
		$oActaSet->add($this->getDatosF_acta());
		$oActaSet->add($this->getDatosLibro());
		$oActaSet->add($this->getDatosPagina());
		$oActaSet->add($this->getDatosLinea());
		$oActaSet->add($this->getDatosLugar());
		$oActaSet->add($this->getDatosObserv());
		return $oActaSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut sacta de Acta
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
	 * Recupera les propietats de l'atribut iid_asignatura de Acta
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
	 * Recupera les propietats de l'atribut iid_activ de Acta
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
	 * Recupera les propietats de l'atribut df_acta de Acta
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
	 * Recupera les propietats de l'atribut ilibro de Acta
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLibro() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'libro'));
		$oDatosCampo->setEtiqueta(_("libro"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ipagina de Acta
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPagina() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pagina'));
		$oDatosCampo->setEtiqueta(_("página"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ilinea de Acta
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLinea() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'linea'));
		$oDatosCampo->setEtiqueta(_("línea"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slugar de Acta
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lugar'));
		$oDatosCampo->setEtiqueta(_("lugar"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de Acta
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observaciones"));
		return $oDatosCampo;
	}
}