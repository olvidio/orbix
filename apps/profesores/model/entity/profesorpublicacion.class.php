<?php
namespace profesores\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula d_publicaciones
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */
/**
 * Classe que implementa l'entitat d_publicaciones
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 04/09/2015
 */
class ProfesorPublicacion Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ProfesorPublicacion
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ProfesorPublicacion
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_item de ProfesorPublicacion
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de ProfesorPublicacion
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Tipo_publicacion de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $stipo_publicacion;
	/**
	 * Titulo de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $stitulo;
	/**
	 * Editorial de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $seditorial;
	/**
	 * Coleccion de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $scoleccion;
	/**
	 * F_publicacion de ProfesorPublicacion
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_publicacion;
	/**
	 * Pendiente de ProfesorPublicacion
	 *
	 * @var boolean
	 */
	 private $bpendiente;
	/**
	 * Referencia de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $sreferencia;
	/**
	 * Lugar de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $slugar;
	/**
	 * Observ de ProfesorPublicacion
	 *
	 * @var string
	 */
	 private $sobserv;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ProfesorPublicacion
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ProfesorPublicacion
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
	 * @param integer|array iid_item,iid_nom
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
				if (($nom_id == 'id_nom') && $val_id !== '') $this->iid_nom = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_publicaciones');
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
		$aDades['tipo_publicacion'] = $this->stipo_publicacion;
		$aDades['titulo'] = $this->stitulo;
		$aDades['editorial'] = $this->seditorial;
		$aDades['coleccion'] = $this->scoleccion;
		$aDades['f_publicacion'] = $this->df_publicacion;
		$aDades['pendiente'] = $this->bpendiente;
		$aDades['referencia'] = $this->sreferencia;
		$aDades['lugar'] = $this->slugar;
		$aDades['observ'] = $this->sobserv;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		$aDades['pendiente'] = ($aDades['pendiente'] === 't')? 'true' : $aDades['pendiente'];
		if ( filter_var( $aDades['pendiente'], FILTER_VALIDATE_BOOLEAN)) { $aDades['pendiente']='t'; } else { $aDades['pendiente']='f'; }

		if ($bInsert === false) {
			//UPDATE
			$update="
					tipo_publicacion         = :tipo_publicacion,
					titulo                   = :titulo,
					editorial                = :editorial,
					coleccion                = :coleccion,
					f_publicacion            = :f_publicacion,
					pendiente                = :pendiente,
					referencia               = :referencia,
					lugar                    = :lugar,
					observ                   = :observ";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'ProfesorPublicacion.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorPublicacion.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,tipo_publicacion,titulo,editorial,coleccion,f_publicacion,pendiente,referencia,lugar,observ)";
			$valores="(:id_nom,:tipo_publicacion,:titulo,:editorial,:coleccion,:f_publicacion,:pendiente,:referencia,:lugar,:observ)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ProfesorPublicacion.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($oDblSt->execute($aDades) === false) {
					$sClauError = 'ProfesorPublicacion.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId('d_publicaciones_id_item_seq');
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
		if (isset($this->iid_item) && isset($this->iid_nom)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
				$sClauError = 'ProfesorPublicacion.carregar';
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
		if (($oDblSt = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item=$this->iid_item")) === false) {
			$sClauError = 'ProfesorPublicacion.eliminar';
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('tipo_publicacion',$aDades)) $this->setTipo_publicacion($aDades['tipo_publicacion']);
		if (array_key_exists('titulo',$aDades)) $this->setTitulo($aDades['titulo']);
		if (array_key_exists('editorial',$aDades)) $this->setEditorial($aDades['editorial']);
		if (array_key_exists('coleccion',$aDades)) $this->setColeccion($aDades['coleccion']);
		if (array_key_exists('f_publicacion',$aDades)) $this->setF_publicacion($aDades['f_publicacion'],$convert);
		if (array_key_exists('pendiente',$aDades)) $this->setPendiente($aDades['pendiente']);
		if (array_key_exists('referencia',$aDades)) $this->setReferencia($aDades['referencia']);
		if (array_key_exists('lugar',$aDades)) $this->setLugar($aDades['lugar']);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
	}

	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$this->setId_schema('');
		$this->setId_item('');
		$this->setId_nom('');
		$this->setTipo_publicacion('');
		$this->setTitulo('');
		$this->setEditorial('');
		$this->setColeccion('');
		$this->setF_publicacion('');
		$this->setPendiente('');
		$this->setReferencia('');
		$this->setLugar('');
		$this->setObserv('');
	}



	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ProfesorPublicacion en un array
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
	 * Recupera las claus primàries de ProfesorPublicacion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_item de ProfesorPublicacion
	 *
	 * @return integer iid_item
	 */
	function getId_item() {
		if (!isset($this->iid_item)) {
			$this->DBCarregar();
		}
		return $this->iid_item;
	}
	/**
	 * estableix el valor de l'atribut iid_item de ProfesorPublicacion
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de ProfesorPublicacion
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
	 * estableix el valor de l'atribut iid_nom de ProfesorPublicacion
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut stipo_publicacion de ProfesorPublicacion
	 *
	 * @return string stipo_publicacion
	 */
	function getTipo_publicacion() {
		if (!isset($this->stipo_publicacion)) {
			$this->DBCarregar();
		}
		return $this->stipo_publicacion;
	}
	/**
	 * estableix el valor de l'atribut stipo_publicacion de ProfesorPublicacion
	 *
	 * @param string stipo_publicacion='' optional
	 */
	function setTipo_publicacion($stipo_publicacion='') {
		$this->stipo_publicacion = $stipo_publicacion;
	}
	/**
	 * Recupera l'atribut stitulo de ProfesorPublicacion
	 *
	 * @return string stitulo
	 */
	function getTitulo() {
		if (!isset($this->stitulo)) {
			$this->DBCarregar();
		}
		return $this->stitulo;
	}
	/**
	 * estableix el valor de l'atribut stitulo de ProfesorPublicacion
	 *
	 * @param string stitulo='' optional
	 */
	function setTitulo($stitulo='') {
		$this->stitulo = $stitulo;
	}
	/**
	 * Recupera l'atribut seditorial de ProfesorPublicacion
	 *
	 * @return string seditorial
	 */
	function getEditorial() {
		if (!isset($this->seditorial)) {
			$this->DBCarregar();
		}
		return $this->seditorial;
	}
	/**
	 * estableix el valor de l'atribut seditorial de ProfesorPublicacion
	 *
	 * @param string seditorial='' optional
	 */
	function setEditorial($seditorial='') {
		$this->seditorial = $seditorial;
	}
	/**
	 * Recupera l'atribut scoleccion de ProfesorPublicacion
	 *
	 * @return string scoleccion
	 */
	function getColeccion() {
		if (!isset($this->scoleccion)) {
			$this->DBCarregar();
		}
		return $this->scoleccion;
	}
	/**
	 * estableix el valor de l'atribut scoleccion de ProfesorPublicacion
	 *
	 * @param string scoleccion='' optional
	 */
	function setColeccion($scoleccion='') {
		$this->scoleccion = $scoleccion;
	}
	/**
	 * Recupera l'atribut df_publicacion de ProfesorPublicacion
	 *
	 * @return web\DateTimeLocal df_publicacion
	 */
	function getF_publicacion() {
	    if (!isset($this->df_publicacion)) {
	        $this->DBCarregar();
	    }
	    if (empty($this->df_publicacion)) {
	    	return new web\NullDateTimeLocal();
	    }
	    $oConverter = new core\Converter('date', $this->df_publicacion);
	    return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_publicacion de ProfesorPublicacion
	* Si df_publicacion es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
	* Si convert es false, df_publicacion debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	*
	* @param date|string df_publicacion='' optional.
	* @param boolean convert=true optional. Si es false, df_publicacion debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_publicacion($df_publicacion='',$convert=true) {
		if ($convert === true && !empty($df_publicacion)) {
	        $oConverter = new core\Converter('date', $df_publicacion);
	        $this->df_publicacion =$oConverter->toPg();
	    } else {
	        $this->df_publicacion = $df_publicacion;
	    }
	}
	/**
	 * Recupera l'atribut bpendiente de ProfesorPublicacion
	 *
	 * @return boolean bpendiente
	 */
	function getPendiente() {
		if (!isset($this->bpendiente)) {
			$this->DBCarregar();
		}
		return $this->bpendiente;
	}
	/**
	 * estableix el valor de l'atribut bpendiente de ProfesorPublicacion
	 *
	 * @param boolean bpendiente='f' optional
	 */
	function setPendiente($bpendiente='f') {
		$this->bpendiente = $bpendiente;
	}
	/**
	 * Recupera l'atribut sreferencia de ProfesorPublicacion
	 *
	 * @return string sreferencia
	 */
	function getReferencia() {
		if (!isset($this->sreferencia)) {
			$this->DBCarregar();
		}
		return $this->sreferencia;
	}
	/**
	 * estableix el valor de l'atribut sreferencia de ProfesorPublicacion
	 *
	 * @param string sreferencia='' optional
	 */
	function setReferencia($sreferencia='') {
		$this->sreferencia = $sreferencia;
	}
	/**
	 * Recupera l'atribut slugar de ProfesorPublicacion
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
	 * estableix el valor de l'atribut slugar de ProfesorPublicacion
	 *
	 * @param string slugar='' optional
	 */
	function setLugar($slugar='') {
		$this->slugar = $slugar;
	}
	/**
	 * Recupera l'atribut sobserv de ProfesorPublicacion
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
	 * estableix el valor de l'atribut sobserv de ProfesorPublicacion
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
		$oProfesorPublicacionSet = new core\Set();

		$oProfesorPublicacionSet->add($this->getDatosTipo_publicacion());
		$oProfesorPublicacionSet->add($this->getDatosTitulo());
		$oProfesorPublicacionSet->add($this->getDatosEditorial());
		$oProfesorPublicacionSet->add($this->getDatosColeccion());
		$oProfesorPublicacionSet->add($this->getDatosF_publicacion());
		$oProfesorPublicacionSet->add($this->getDatosPendiente());
		$oProfesorPublicacionSet->add($this->getDatosReferencia());
		$oProfesorPublicacionSet->add($this->getDatosLugar());
		$oProfesorPublicacionSet->add($this->getDatosObserv());
		return $oProfesorPublicacionSet->getTot();
	}


	/**
	 * Recupera les propietats de l'atribut stipo_publicacion de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTipo_publicacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo_publicacion'));
		$oDatosCampo->setEtiqueta(_("tipo de publicación"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(15);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stitulo de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosTitulo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'titulo'));
		$oDatosCampo->setEtiqueta(_("título"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(100);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut seditorial de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEditorial() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'editorial'));
		$oDatosCampo->setEtiqueta(_("editorial"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(50);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scoleccion de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosColeccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'coleccion'));
		$oDatosCampo->setEtiqueta(_("colección"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(50);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_publicacion de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_publicacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_publicacion'));
		$oDatosCampo->setEtiqueta(_("fecha de la publicación"));
		$oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bpendiente de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPendiente() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pendiente'));
		$oDatosCampo->setEtiqueta(_("pendiente"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sreferencia de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosReferencia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'referencia'));
		$oDatosCampo->setEtiqueta(_("referencia"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(50);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut slugar de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'lugar'));
		$oDatosCampo->setEtiqueta(_("lugar"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(100);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de ProfesorPublicacion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observaciones"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(100);
		return $oDatosCampo;
	}
}