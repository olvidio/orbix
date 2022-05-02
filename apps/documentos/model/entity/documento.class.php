<?php
namespace documentos\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula doc_documentos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
/**
 * Classe que implementa l'entitat doc_documentos
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/4/2022
 */
class Documento Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de Documento
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de Documento
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de Documento
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de Documento
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_doc de Documento
	 *
	 * @var integer
	 */
	 private $iid_doc;
	/**
	 * Id_tipo_doc de Documento
	 *
	 * @var integer
	 */
	 private $iid_tipo_doc;
	/**
	 * Id_ubi de Documento
	 *
	 * @var integer
	 */
	 private $iid_ubi;
	/**
	 * Id_lugar de Documento
	 *
	 * @var integer
	 */
	 private $iid_lugar;
	/**
	 * F_recibido de Documento
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_recibido;
	/**
	 * F_asignado de Documento
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_asignado;
	/**
	 * Observ de Documento
	 *
	 * @var string
	 */
	 private $sobserv;
	/**
	 * F_ult_comprobacion de Documento
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ult_comprobacion;
	/**
	 * En_busqueda de Documento
	 *
	 * @var boolean
	 */
	 private $ben_busqueda;
	/**
	 * Perdido de Documento
	 *
	 * @var boolean
	 */
	 private $bperdido;
	/**
	 * F_perdido de Documento
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_perdido;
	/**
	 * Eliminado de Documento
	 *
	 * @var boolean
	 */
	 private $beliminado;
	/**
	 * F_eliminado de Documento
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_eliminado;
	/**
	 * Num_reg de Documento
	 *
	 * @var integer
	 */
	 private $inum_reg;
	/**
	 * Num_ini de Documento
	 *
	 * @var integer
	 */
	 private $inum_ini;
	/**
	 * Num_fin de Documento
	 *
	 * @var integer
	 */
	 private $inum_fin;
	/**
	 * Identificador de Documento
	 *
	 * @var string
	 */
	 private $sidentificador;
	/**
	 * Num_ejemplares de Documento
	 *
	 * @var integer
	 */
	 private $inum_ejemplares;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de Documento
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de Documento
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
	 * @param integer|array iid_doc
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */
	function __construct($a_id='') {
		$oDbl = $GLOBALS['oDB'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_doc') && $val_id !== '') $this->iid_doc = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_doc = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_doc' => $this->iid_doc);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('doc_documentos');
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
		$aDades['id_tipo_doc'] = $this->iid_tipo_doc;
		$aDades['id_ubi'] = $this->iid_ubi;
		$aDades['id_lugar'] = $this->iid_lugar;
		$aDades['f_recibido'] = $this->df_recibido;
		$aDades['f_asignado'] = $this->df_asignado;
		$aDades['observ'] = $this->sobserv;
		$aDades['f_ult_comprobacion'] = $this->df_ult_comprobacion;
		$aDades['en_busqueda'] = $this->ben_busqueda;
		$aDades['perdido'] = $this->bperdido;
		$aDades['f_perdido'] = $this->df_perdido;
		$aDades['eliminado'] = $this->beliminado;
		$aDades['f_eliminado'] = $this->df_eliminado;
		$aDades['num_reg'] = $this->inum_reg;
		$aDades['num_ini'] = $this->inum_ini;
		$aDades['num_fin'] = $this->inum_fin;
		$aDades['identificador'] = $this->sidentificador;
		$aDades['num_ejemplares'] = $this->inum_ejemplares;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['en_busqueda']) ) { $aDades['en_busqueda']='true'; } else { $aDades['en_busqueda']='false'; }
		if ( core\is_true($aDades['perdido']) ) { $aDades['perdido']='true'; } else { $aDades['perdido']='false'; }
		if ( core\is_true($aDades['eliminado']) ) { $aDades['eliminado']='true'; } else { $aDades['eliminado']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_tipo_doc              = :id_tipo_doc,
					id_ubi                   = :id_ubi,
					id_lugar                 = :id_lugar,
					f_recibido               = :f_recibido,
					f_asignado               = :f_asignado,
					observ                   = :observ,
					f_ult_comprobacion       = :f_ult_comprobacion,
					en_busqueda              = :en_busqueda,
					perdido                  = :perdido,
					f_perdido                = :f_perdido,
					eliminado                = :eliminado,
					f_eliminado              = :f_eliminado,
					num_reg                  = :num_reg,
					num_ini                  = :num_ini,
					num_fin                  = :num_fin,
					identificador            = :identificador,
					num_ejemplares           = :num_ejemplares";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_doc='$this->iid_doc'")) === FALSE) {
				$sClauError = 'Documento.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Documento.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			$campos="(id_tipo_doc,id_ubi,id_lugar,f_recibido,f_asignado,observ,f_ult_comprobacion,en_busqueda,perdido,f_perdido,eliminado,f_eliminado,num_reg,num_ini,num_fin,identificador,num_ejemplares)";
			$valores="(:id_tipo_doc,:id_ubi,:id_lugar,:f_recibido,:f_asignado,:observ,:f_ult_comprobacion,:en_busqueda,:perdido,:f_perdido,:eliminado,:f_eliminado,:num_reg,:num_ini,:num_fin,:identificador,:num_ejemplares)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'Documento.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'Documento.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_doc = $oDbl->lastInsertId('doc_documentos_id_doc_seq');
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
		if (isset($this->iid_doc)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_doc='$this->iid_doc'")) === FALSE) {
				$sClauError = 'Documento.carregar';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_doc='$this->iid_doc'")) === FALSE) {
			$sClauError = 'Documento.eliminar';
			$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
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
		if (array_key_exists('id_doc',$aDades)) $this->setId_doc($aDades['id_doc']);
		if (array_key_exists('id_tipo_doc',$aDades)) $this->setId_tipo_doc($aDades['id_tipo_doc']);
		if (array_key_exists('id_ubi',$aDades)) $this->setId_ubi($aDades['id_ubi']);
		if (array_key_exists('id_lugar',$aDades)) $this->setId_lugar($aDades['id_lugar']);
		if (array_key_exists('f_recibido',$aDades)) $this->setF_recibido($aDades['f_recibido'],$convert);
		if (array_key_exists('f_asignado',$aDades)) $this->setF_asignado($aDades['f_asignado'],$convert);
		if (array_key_exists('observ',$aDades)) $this->setObserv($aDades['observ']);
		if (array_key_exists('f_ult_comprobacion',$aDades)) $this->setF_ult_comprobacion($aDades['f_ult_comprobacion'],$convert);
		if (array_key_exists('en_busqueda',$aDades)) $this->setEn_busqueda($aDades['en_busqueda']);
		if (array_key_exists('perdido',$aDades)) $this->setPerdido($aDades['perdido']);
		if (array_key_exists('f_perdido',$aDades)) $this->setF_perdido($aDades['f_perdido'],$convert);
		if (array_key_exists('eliminado',$aDades)) $this->setEliminado($aDades['eliminado']);
		if (array_key_exists('f_eliminado',$aDades)) $this->setF_eliminado($aDades['f_eliminado'],$convert);
		if (array_key_exists('num_reg',$aDades)) $this->setNum_reg($aDades['num_reg']);
		if (array_key_exists('num_ini',$aDades)) $this->setNum_ini($aDades['num_ini']);
		if (array_key_exists('num_fin',$aDades)) $this->setNum_fin($aDades['num_fin']);
		if (array_key_exists('identificador',$aDades)) $this->setIdentificador($aDades['identificador']);
		if (array_key_exists('num_ejemplares',$aDades)) $this->setNum_ejemplares($aDades['num_ejemplares']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_doc('');
		$this->setId_tipo_doc('');
		$this->setId_ubi('');
		$this->setId_lugar('');
		$this->setF_recibido('');
		$this->setF_asignado('');
		$this->setObserv('');
		$this->setF_ult_comprobacion('');
		$this->setEn_busqueda('');
		$this->setPerdido('');
		$this->setF_perdido('');
		$this->setEliminado('');
		$this->setF_eliminado('');
		$this->setNum_reg('');
		$this->setNum_ini('');
		$this->setNum_fin('');
		$this->setIdentificador('');
		$this->setNum_ejemplares('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de Documento en un array
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
	 * Recupera las claus primàries de Documento en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_doc' => $this->iid_doc);
		}
		return $this->aPrimary_key;
	}
	/**
	 * Estableix las claus primàries de Documento en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_doc') && $val_id !== '') $this->iid_doc = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_doc = (integer) $a_id; // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_doc' => $this->iid_doc);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_doc de Documento
	 *
	 * @return integer iid_doc
	 */
	function getId_doc() {
		if (!isset($this->iid_doc) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_doc;
	}
	/**
	 * estableix el valor de l'atribut iid_doc de Documento
	 *
	 * @param integer iid_doc
	 */
	function setId_doc($iid_doc) {
		$this->iid_doc = $iid_doc;
	}
	/**
	 * Recupera l'atribut iid_tipo_doc de Documento
	 *
	 * @return integer iid_tipo_doc
	 */
	function getId_tipo_doc() {
		if (!isset($this->iid_tipo_doc) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_tipo_doc;
	}
	/**
	 * estableix el valor de l'atribut iid_tipo_doc de Documento
	 *
	 * @param integer iid_tipo_doc='' optional
	 */
	function setId_tipo_doc($iid_tipo_doc='') {
		$this->iid_tipo_doc = $iid_tipo_doc;
	}
	/**
	 * Recupera l'atribut iid_ubi de Documento
	 *
	 * @return integer iid_ubi
	 */
	function getId_ubi() {
		if (!isset($this->iid_ubi) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_ubi;
	}
	/**
	 * estableix el valor de l'atribut iid_ubi de Documento
	 *
	 * @param integer iid_ubi='' optional
	 */
	function setId_ubi($iid_ubi='') {
		$this->iid_ubi = $iid_ubi;
	}
	/**
	 * Recupera l'atribut iid_lugar de Documento
	 *
	 * @return integer iid_lugar
	 */
	function getId_lugar() {
		if (!isset($this->iid_lugar) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->iid_lugar;
	}
	/**
	 * estableix el valor de l'atribut iid_lugar de Documento
	 *
	 * @param integer iid_lugar='' optional
	 */
	function setId_lugar($iid_lugar='') {
		$this->iid_lugar = $iid_lugar;
	}
	/**
	 * Recupera l'atribut df_recibido de Documento
	 *
	 * @return web\DateTimeLocal df_recibido
	 */
	function getF_recibido() {
		if (!isset($this->df_recibido) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_recibido)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_recibido);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_recibido de Documento
	 * Si df_recibido es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_recibido debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_recibido='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_recibido($df_recibido='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_recibido)) {
            $oConverter = new core\Converter('date', $df_recibido);
            $this->df_recibido = $oConverter->toPg();
	    } else {
            $this->df_recibido = $df_recibido;
	    }
	}
	/**
	 * Recupera l'atribut df_asignado de Documento
	 *
	 * @return web\DateTimeLocal df_asignado
	 */
	function getF_asignado() {
		if (!isset($this->df_asignado) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_asignado)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_asignado);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_asignado de Documento
	 * Si df_asignado es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_asignado debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_asignado='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_asignado($df_asignado='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_asignado)) {
            $oConverter = new core\Converter('date', $df_asignado);
            $this->df_asignado = $oConverter->toPg();
	    } else {
            $this->df_asignado = $df_asignado;
	    }
	}
	/**
	 * Recupera l'atribut sobserv de Documento
	 *
	 * @return string sobserv
	 */
	function getObserv() {
		if (!isset($this->sobserv) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sobserv;
	}
	/**
	 * estableix el valor de l'atribut sobserv de Documento
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut df_ult_comprobacion de Documento
	 *
	 * @return web\DateTimeLocal df_ult_comprobacion
	 */
	function getF_ult_comprobacion() {
		if (!isset($this->df_ult_comprobacion) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_ult_comprobacion)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_ult_comprobacion);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_ult_comprobacion de Documento
	 * Si df_ult_comprobacion es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_ult_comprobacion debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_ult_comprobacion='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_ult_comprobacion($df_ult_comprobacion='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_ult_comprobacion)) {
            $oConverter = new core\Converter('date', $df_ult_comprobacion);
            $this->df_ult_comprobacion = $oConverter->toPg();
	    } else {
            $this->df_ult_comprobacion = $df_ult_comprobacion;
	    }
	}
	/**
	 * Recupera l'atribut ben_busqueda de Documento
	 *
	 * @return boolean ben_busqueda
	 */
	function getEn_busqueda() {
		if (!isset($this->ben_busqueda) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->ben_busqueda;
	}
	/**
	 * estableix el valor de l'atribut ben_busqueda de Documento
	 *
	 * @param boolean ben_busqueda='f' optional
	 */
	function setEn_busqueda($ben_busqueda='f') {
		$this->ben_busqueda = $ben_busqueda;
	}
	/**
	 * Recupera l'atribut bperdido de Documento
	 *
	 * @return boolean bperdido
	 */
	function getPerdido() {
		if (!isset($this->bperdido) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->bperdido;
	}
	/**
	 * estableix el valor de l'atribut bperdido de Documento
	 *
	 * @param boolean bperdido='f' optional
	 */
	function setPerdido($bperdido='f') {
		$this->bperdido = $bperdido;
	}
	/**
	 * Recupera l'atribut df_perdido de Documento
	 *
	 * @return web\DateTimeLocal df_perdido
	 */
	function getF_perdido() {
		if (!isset($this->df_perdido) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_perdido)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_perdido);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_perdido de Documento
	 * Si df_perdido es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_perdido debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_perdido='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_perdido($df_perdido='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_perdido)) {
            $oConverter = new core\Converter('date', $df_perdido);
            $this->df_perdido = $oConverter->toPg();
	    } else {
            $this->df_perdido = $df_perdido;
	    }
	}
	/**
	 * Recupera l'atribut beliminado de Documento
	 *
	 * @return boolean beliminado
	 */
	function getEliminado() {
		if (!isset($this->beliminado) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->beliminado;
	}
	/**
	 * estableix el valor de l'atribut beliminado de Documento
	 *
	 * @param boolean beliminado='f' optional
	 */
	function setEliminado($beliminado='f') {
		$this->beliminado = $beliminado;
	}
	/**
	 * Recupera l'atribut df_eliminado de Documento
	 *
	 * @return web\DateTimeLocal df_eliminado
	 */
	function getF_eliminado() {
		if (!isset($this->df_eliminado) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->df_eliminado)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_eliminado);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_eliminado de Documento
	 * Si df_eliminado es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_eliminado debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_eliminado='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_eliminado($df_eliminado='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_eliminado)) {
            $oConverter = new core\Converter('date', $df_eliminado);
            $this->df_eliminado = $oConverter->toPg();
	    } else {
            $this->df_eliminado = $df_eliminado;
	    }
	}
	/**
	 * Recupera l'atribut inum_reg de Documento
	 *
	 * @return integer inum_reg
	 */
	function getNum_reg() {
		if (!isset($this->inum_reg) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->inum_reg;
	}
	/**
	 * estableix el valor de l'atribut inum_reg de Documento
	 *
	 * @param integer inum_reg='' optional
	 */
	function setNum_reg($inum_reg='') {
		$this->inum_reg = $inum_reg;
	}
	/**
	 * Recupera l'atribut inum_ini de Documento
	 *
	 * @return integer inum_ini
	 */
	function getNum_ini() {
		if (!isset($this->inum_ini) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->inum_ini;
	}
	/**
	 * estableix el valor de l'atribut inum_ini de Documento
	 *
	 * @param integer inum_ini='' optional
	 */
	function setNum_ini($inum_ini='') {
		$this->inum_ini = $inum_ini;
	}
	/**
	 * Recupera l'atribut inum_fin de Documento
	 *
	 * @return integer inum_fin
	 */
	function getNum_fin() {
		if (!isset($this->inum_fin) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->inum_fin;
	}
	/**
	 * estableix el valor de l'atribut inum_fin de Documento
	 *
	 * @param integer inum_fin='' optional
	 */
	function setNum_fin($inum_fin='') {
		$this->inum_fin = $inum_fin;
	}
	/**
	 * Recupera l'atribut sidentificador de Documento
	 *
	 * @return string sidentificador
	 */
	function getIdentificador() {
		if (!isset($this->sidentificador) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->sidentificador;
	}
	/**
	 * estableix el valor de l'atribut sidentificador de Documento
	 *
	 * @param string sidentificador='' optional
	 */
	function setIdentificador($sidentificador='') {
		$this->sidentificador = $sidentificador;
	}
	/**
	 * Recupera l'atribut inum_ejemplares de Documento
	 *
	 * @return integer inum_ejemplares
	 */
	function getNum_ejemplares() {
		if (!isset($this->inum_ejemplares) && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->inum_ejemplares;
	}
	/**
	 * estableix el valor de l'atribut inum_ejemplares de Documento
	 *
	 * @param integer inum_ejemplares='' optional
	 */
	function setNum_ejemplares($inum_ejemplares='') {
		$this->inum_ejemplares = $inum_ejemplares;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDocumentoSet = new core\Set();

		$oDocumentoSet->add($this->getDatosId_tipo_doc());
		$oDocumentoSet->add($this->getDatosId_ubi());
		$oDocumentoSet->add($this->getDatosId_lugar());
		$oDocumentoSet->add($this->getDatosF_recibido());
		$oDocumentoSet->add($this->getDatosF_asignado());
		$oDocumentoSet->add($this->getDatosObserv());
		$oDocumentoSet->add($this->getDatosF_ult_comprobacion());
		$oDocumentoSet->add($this->getDatosEn_busqueda());
		$oDocumentoSet->add($this->getDatosPerdido());
		$oDocumentoSet->add($this->getDatosF_perdido());
		$oDocumentoSet->add($this->getDatosEliminado());
		$oDocumentoSet->add($this->getDatosF_eliminado());
		$oDocumentoSet->add($this->getDatosNum_reg());
		$oDocumentoSet->add($this->getDatosNum_ini());
		$oDocumentoSet->add($this->getDatosNum_fin());
		$oDocumentoSet->add($this->getDatosIdentificador());
		$oDocumentoSet->add($this->getDatosNum_ejemplares());
		return $oDocumentoSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_tipo_doc de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_doc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_doc'));
		$oDatosCampo->setEtiqueta(_("id_tipo_doc"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_ubi de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_ubi() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_ubi'));
		$oDatosCampo->setEtiqueta(_("id_ubi"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_lugar de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_lugar() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_lugar'));
		$oDatosCampo->setEtiqueta(_("id_lugar"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_recibido de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_recibido() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_recibido'));
		$oDatosCampo->setEtiqueta(_("f_recibido"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_asignado de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_asignado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_asignado'));
		$oDatosCampo->setEtiqueta(_("f_asignado"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosObserv() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'observ'));
		$oDatosCampo->setEtiqueta(_("observ"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ult_comprobacion de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_ult_comprobacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ult_comprobacion'));
		$oDatosCampo->setEtiqueta(_("f_ult_comprobacion"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ben_busqueda de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEn_busqueda() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'en_busqueda'));
		$oDatosCampo->setEtiqueta(_("en_busqueda"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bperdido de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPerdido() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'perdido'));
		$oDatosCampo->setEtiqueta(_("perdido"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_perdido de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_perdido() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_perdido'));
		$oDatosCampo->setEtiqueta(_("f_perdido"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut beliminado de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosEliminado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'eliminado'));
		$oDatosCampo->setEtiqueta(_("eliminado"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_eliminado de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_eliminado() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_eliminado'));
		$oDatosCampo->setEtiqueta(_("f_eliminado"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_reg de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_reg() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_reg'));
		$oDatosCampo->setEtiqueta(_("num_reg"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_ini de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_ini'));
		$oDatosCampo->setEtiqueta(_("num_ini"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_fin de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_fin() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_fin'));
		$oDatosCampo->setEtiqueta(_("num_fin"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sidentificador de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosIdentificador() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'identificador'));
		$oDatosCampo->setEtiqueta(_("identificador"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut inum_ejemplares de Documento
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNum_ejemplares() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'num_ejemplares'));
		$oDatosCampo->setEtiqueta(_("num_ejemplares"));
		return $oDatosCampo;
	}
}
