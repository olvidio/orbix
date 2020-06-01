<?php
namespace personas\model\entity;
use core;
use web;
/**
 * Fitxer amb la Classe que accedeix a la taula d_ultima_asistencia
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 1/6/2020
 */
/**
 * Classe que implementa l'entitat d_ultima_asistencia
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 1/6/2020
 */
class UltimaAsistencia Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de UltimaAsistencia
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de UltimaAsistencia
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de UltimaAsistencia
	 *
	 * @var integer
	 */
	 private $iid_schema;

	/**
	 * Id_item de UltimaAsistencia
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de UltimaAsistencia
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Id_tipo_activ de UltimaAsistencia
	 *
	 * @var integer
	 */
	 private $iid_tipo_activ;
	/**
	 * F_ini de UltimaAsistencia
	 *
	 * @var web\DateTimeLocal
	 */
	 private $df_ini;
	/**
	 * Descripcion de UltimaAsistencia
	 *
	 * @var string
	 */
	 private $sdescripcion;
	/**
	 * Cdr de UltimaAsistencia
	 *
	 * @var boolean
	 */
	 private $bcdr;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de UltimaAsistencia
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de UltimaAsistencia
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
	 * @param integer|array iid_item
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
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla('d_ultima_asistencia');
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
		$aDades['id_tipo_activ'] = $this->iid_tipo_activ;
		$aDades['f_ini'] = $this->df_ini;
		$aDades['descripcion'] = $this->sdescripcion;
		$aDades['cdr'] = $this->bcdr;
		array_walk($aDades, 'core\poner_null');
		//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:
		if ( core\is_true($aDades['cdr']) ) { $aDades['cdr']='true'; } else { $aDades['cdr']='false'; }

		if ($bInsert === FALSE) {
			//UPDATE
			$update="
					id_tipo_activ            = :id_tipo_activ,
					f_ini                    = :f_ini,
					descripcion              = :descripcion,
					cdr                      = :cdr";
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'UltimaAsistencia.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'UltimaAsistencia.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,id_tipo_activ,f_ini,descripcion,cdr)";
			$valores="(:id_nom,:id_tipo_activ,:f_ini,:descripcion,:cdr)";		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = 'UltimaAsistencia.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = 'UltimaAsistencia.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
			$this->id_item = $oDbl->lastInsertId('d_ultima_asistencia_id_item_seq');
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
		if (isset($this->iid_item)) {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
				$sClauError = 'UltimaAsistencia.carregar';
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
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item'")) === FALSE) {
			$sClauError = 'UltimaAsistencia.eliminar';
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
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('id_tipo_activ',$aDades)) $this->setId_tipo_activ($aDades['id_tipo_activ']);
		if (array_key_exists('f_ini',$aDades)) $this->setF_ini($aDades['f_ini'],$convert);
		if (array_key_exists('descripcion',$aDades)) $this->setDescripcion($aDades['descripcion']);
		if (array_key_exists('cdr',$aDades)) $this->setCdr($aDades['cdr']);
	}	
	/**
	 * Estableix a empty el valor de tots els atributs
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema('');
		$this->setId_item('');
		$this->setId_nom('');
		$this->setId_tipo_activ('');
		$this->setF_ini('');
		$this->setDescripcion('');
		$this->setCdr('');
		$this->setPrimary_key($aPK);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de UltimaAsistencia en un array
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
	 * Recupera las claus primàries de UltimaAsistencia en un array
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
	 * Estableix las claus primàries de UltimaAsistencia en un array
	 *
	 */
	public function setPrimary_key($a_id='') {
	    if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
				if (($nom_id == 'id_item') && $val_id !== '') $this->iid_item = (int)$val_id; // evitem SQL injection fent cast a integer
			}
		} else {
			if (isset($a_id) && $a_id !== '') {
				$this->iid_item = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array('iid_item' => $this->iid_item);
			}
		}
	}
	

	/**
	 * Recupera l'atribut iid_item de UltimaAsistencia
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
	 * estableix el valor de l'atribut iid_item de UltimaAsistencia
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de UltimaAsistencia
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
	 * estableix el valor de l'atribut iid_nom de UltimaAsistencia
	 *
	 * @param integer iid_nom='' optional
	 */
	function setId_nom($iid_nom='') {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_tipo_activ de UltimaAsistencia
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
	 * estableix el valor de l'atribut iid_tipo_activ de UltimaAsistencia
	 *
	 * @param integer iid_tipo_activ='' optional
	 */
	function setId_tipo_activ($iid_tipo_activ='') {
		$this->iid_tipo_activ = $iid_tipo_activ;
	}
	/**
	 * Recupera l'atribut df_ini de UltimaAsistencia
	 *
	 * @return web\DateTimeLocal df_ini
	 */
	function getF_ini() {
		if (!isset($this->df_ini)) {
			$this->DBCarregar();
		}
		if (empty($this->df_ini)) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter('date', $this->df_ini);
		return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_ini de UltimaAsistencia
	 * Si df_ini es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, df_ini debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param web\DateTimeLocal|string df_ini='' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_ini($df_ini='',$convert=TRUE) {
        if ($convert === TRUE  && !empty($df_ini)) {
            $oConverter = new core\Converter('date', $df_ini);
            $this->df_ini = $oConverter->toPg();
	    } else {
            $this->df_ini = $df_ini;
	    }
	}
	/**
	 * Recupera l'atribut sdescripcion de UltimaAsistencia
	 *
	 * @return string sdescripcion
	 */
	function getDescripcion() {
		if (!isset($this->sdescripcion)) {
			$this->DBCarregar();
		}
		return $this->sdescripcion;
	}
	/**
	 * estableix el valor de l'atribut sdescripcion de UltimaAsistencia
	 *
	 * @param string sdescripcion='' optional
	 */
	function setDescripcion($sdescripcion='') {
		$this->sdescripcion = $sdescripcion;
	}
	/**
	 * Recupera l'atribut bcdr de UltimaAsistencia
	 *
	 * @return boolean bcdr
	 */
	function getCdr() {
		if (!isset($this->bcdr)) {
			$this->DBCarregar();
		}
		return $this->bcdr;
	}
	/**
	 * estableix el valor de l'atribut bcdr de UltimaAsistencia
	 *
	 * @param boolean bcdr='f' optional
	 */
	function setCdr($bcdr='f') {
		$this->bcdr = $bcdr;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oUltimaAsistenciaSet = new core\Set();

		//$oUltimaAsistenciaSet->add($this->getDatosId_nom());
		$oUltimaAsistenciaSet->add($this->getDatosId_tipo_activ());
		$oUltimaAsistenciaSet->add($this->getDatosF_ini());
		$oUltimaAsistenciaSet->add($this->getDatosDescripcion());
		$oUltimaAsistenciaSet->add($this->getDatosCdr());
		return $oUltimaAsistenciaSet->getTot();
	}



	/**
	 * Recupera les propietats de l'atribut iid_nom de UltimaAsistencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_nom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_nom'));
		$oDatosCampo->setEtiqueta(_("id_nom"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_tipo_activ de UltimaAsistencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosId_tipo_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_tipo_activ'));
		$oDatosCampo->setEtiqueta(_("tipo de actividad"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('actividades\model\entity\TipoDeActividad'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('getNombreCompleto'); // método para obtener el valor a mostrar del objeto relacionado.
		$oDatosCampo->setArgument3('getListaTiposActividad'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_ini de UltimaAsistencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_ini() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_ini'));
		$oDatosCampo->setEtiqueta(_("fecha inicio actividad"));
		$oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sdescripcion de UltimaAsistencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDescripcion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'descripcion'));
		$oDatosCampo->setEtiqueta(_("descripción"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(70);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut bcdr de UltimaAsistencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCdr() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cdr'));
		$oDatosCampo->setEtiqueta(_("cdr"));
		$oDatosCampo->setTipo('check');
		return $oDatosCampo;
	}
}
