<?php
namespace ubis\model\entity;
use core;
use web;
/**
 * Classe que implementa l'entitat u_direcciones_global
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 01/10/2010
 */

Abstract class DireccionGlobal Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */
	/**
	 * aPrimary_key de Direccion
	 *
	 * @var array
	 */
	 protected $aPrimary_key;

	/**
	 * aDades de Direccion
	 *
	 * @var array
	 */
	 protected $aDades;

	/**
	 * Id_direccion de Direccion
	 *
	 * @var integer
	 */
	 protected $iid_direccion;
	/**
	 * Direccion de Direccion
	 *
	 * @var string
	 */
	 protected $sdireccion;
	/**
	 * C_p de Direccion
	 *
	 * @var string
	 */
	 protected $sc_p;
	/**
	 * Poblacion de Direccion
	 *
	 * @var string
	 */
	 protected $spoblacion;
	/**
	 * Provincia de Direccion
	 *
	 * @var string
	 */
	 protected $sprovincia;
	/**
	 * A_p de Direccion
	 *
	 * @var string
	 */
	 protected $sa_p;
	/**
	 * Pais de Direccion
	 *
	 * @var string
	 */
	 protected $spais;
	/**
	 * F_direccion de Direccion
	 *
	 * @var web\DateTimeLocal
	 */
	 protected $df_direccion;
	/**
	 * Observ de Direccion
	 *
	 * @var string
	 */
	 protected $sobserv;
	/**
	 * Cp_dcha de Direccion
	 *
	 * @var boolean
	 */
	 protected $bcp_dcha;
	/**
	 * Latitud de Direccion
	 *
	 * @var integer
	 */
	 protected $ilatitud;
	/**
	 * Longitud de Direccion
	 *
	 * @var integer
	 */
	 protected $ilongitud;
	/**
	 * Plano_doc de Direccion
	 *
	 * @var string bytea
	 */
	 protected $iplano_doc;
	/**
	 * Plano_extension de Direccion
	 *
	 * @var string
	 */
	 protected $splano_extension;
	/**
	 * Plano_nom de Direccion
	 *
	 * @var string
	 */
	 protected $splano_nom;
	/**
	 * Nom_sede de Direccion
	 *
	 * @var string
	 */
	 protected $snom_sede;

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
	}

	/* METODES PUBLICS ----------------------------------------------------------*/
	/* METODES ALTRES  ----------------------------------------------------------*/
	/* METODES PRIVATS ----------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
	/**
	* Devuelve los ubis de una direccion
	*
	* @return array de objetos Ubis
	*
	*/
	function getUbis() {
		$aClassName = explode('\\',get_called_class());
		$childClassName = end($aClassName);
		switch ($childClassName) {
			case 'DireccionCtr':
				$obj = 'ubis\model\entity\Centro';
			break;
			case 'DireccionCtrDl':
				$obj = 'ubis\model\entity\CentroDl';
			break;
			case 'DireccionCtrEx':
				$obj = 'ubis\model\entity\CentroEx';
			break;
			case 'DireccionCdc':
				$obj = 'ubis\model\entity\Casa';
			break;
			case 'DireccionCdcDl':
				$obj = 'ubis\model\entity\CasaDl';
			break;
			case 'DireccionCdcEx':
				$obj = 'ubis\model\entity\CasaEx';
			break;
		}

		$aWhere['id_direccion'] = $this->getId_direccion();
		$GesUbixDireccion = new GestorUbixDireccion();
		$cUbixDireccion = $GesUbixDireccion->getUbixDirecciones($aWhere);
		$ubis = array();
		if ($cUbixDireccion !== false) {
			foreach ($cUbixDireccion as $oUbixDireccion) {
				$id_ubi = $oUbixDireccion->getId_ubi();
				$propietario = $oUbixDireccion->getPropietario();
				$oUbi = new $obj($id_ubi);
				$ubis[] = $oUbi;
			}
		}
		return $ubis;
	}


	/**
	 * Recupera tots els atributs de Direccion en un array
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
	 * Recupera las claus primàries de Direccion en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('iid_direccion' => $this->iid_direccion);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_direccion de Direccion
	 *
	 * @return integer iid_direccion
	 */
	function getId_direccion() {
		if (!isset($this->iid_direccion)) {
			$this->DBCarregar();
		}
		return $this->iid_direccion;
	}
	/**
	 * estableix el valor de l'atribut iid_direccion de Direccion
	 *
	 * @param integer iid_direccion
	 */
	function setId_direccion($iid_direccion) {
		$this->iid_direccion = $iid_direccion;
	}
	/**
	 * Recupera l'atribut sdireccion de Direccion
	 *
	 * @return string sdireccion
	 */
	function getDireccion() {
		if (!isset($this->sdireccion)) {
			$this->DBCarregar();
		}
		return $this->sdireccion;
	}
	/**
	 * estableix el valor de l'atribut sdireccion de Direccion
	 *
	 * @param string sdireccion='' optional
	 */
	function setDireccion($sdireccion='') {
		$this->sdireccion = $sdireccion;
	}
	/**
	 * Recupera l'atribut sc_p de Direccion
	 *
	 * @return string sc_p
	 */
	function getC_p() {
		if (!isset($this->sc_p)) {
			$this->DBCarregar();
		}
		return $this->sc_p;
	}
	/**
	 * estableix el valor de l'atribut sc_p de Direccion
	 *
	 * @param string sc_p='' optional
	 */
	function setC_p($sc_p='') {
		$this->sc_p = $sc_p;
	}
	/**
	 * Recupera l'atribut spoblacion de Direccion
	 *
	 * @return string spoblacion
	 */
	function getPoblacion() {
		if (!isset($this->spoblacion)) {
			$this->DBCarregar();
		}
		return $this->spoblacion;
	}
	/**
	 * estableix el valor de l'atribut spoblacion de Direccion
	 *
	 * @param string spoblacion='' optional
	 */
	function setPoblacion($spoblacion='') {
		$this->spoblacion = $spoblacion;
	}
	/**
	 * Recupera l'atribut sprovincia de Direccion
	 *
	 * @return string sprovincia
	 */
	function getProvincia() {
		if (!isset($this->sprovincia)) {
			$this->DBCarregar();
		}
		return $this->sprovincia;
	}
	/**
	 * estableix el valor de l'atribut sprovincia de Direccion
	 *
	 * @param string sprovincia='' optional
	 */
	function setProvincia($sprovincia='') {
		$this->sprovincia = $sprovincia;
	}
	/**
	 * Recupera l'atribut sa_p de Direccion
	 *
	 * @return string sa_p
	 */
	function getA_p() {
		if (!isset($this->sa_p)) {
			$this->DBCarregar();
		}
		return $this->sa_p;
	}
	/**
	 * estableix el valor de l'atribut sa_p de Direccion
	 *
	 * @param string sa_p='' optional
	 */
	function setA_p($sa_p='') {
		$this->sa_p = $sa_p;
	}
	/**
	 * Recupera l'atribut spais de Direccion
	 *
	 * @return string spais
	 */
	function getPais() {
		if (!isset($this->spais)) {
			$this->DBCarregar();
		}
		return $this->spais;
	}
	/**
	 * estableix el valor de l'atribut spais de Direccion
	 *
	 * @param string spais='' optional
	 */
	function setPais($spais='') {
		$this->spais = $spais;
	}
	/**
	 * Recupera l'atribut df_direccion de Direccion
	 *
	 * @return web\DateTimeLocal df_direccion
	 */
	function getF_direccion() {
	    if (!isset($this->df_direccion)) {
	        $this->DBCarregar();
	    }
	    if (empty($this->df_direccion)) {
	    	return new web\NullDateTimeLocal();
	    }
	    $oConverter = new core\Converter('date', $this->df_direccion);
	    return $oConverter->fromPg();
	}
	/**
	 * estableix el valor de l'atribut df_direccion de Direccion
	* Si df_direccion es string, y convert=true se convierte usando el formato webDateTimeLocal->getFormat().
	* Si convert es false, df_direccion debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	*
	* @param date|string df_direccion='' optional.
	* @param boolean convert=true optional. Si es false, df_direccion debe ser un string en formato ISO (Y-m-d).
	 */
	function setF_direccion($df_direccion='',$convert=true) {
		if ($convert === true && !empty($df_direccion)) {
	        $oConverter = new core\Converter('date', $df_direccion);
	        $this->df_direccion =$oConverter->toPg();
	    } else {
	        $this->df_direccion = $df_direccion;
	    }
	}
	/**
	 * Recupera l'atribut sobserv de Direccion
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
	 * estableix el valor de l'atribut sobserv de Direccion
	 *
	 * @param string sobserv='' optional
	 */
	function setObserv($sobserv='') {
		$this->sobserv = $sobserv;
	}
	/**
	 * Recupera l'atribut bcp_dcha de Direccion
	 *
	 * @return boolean bcp_dcha
	 */
	function getCp_dcha() {
		if (!isset($this->bcp_dcha)) {
			$this->DBCarregar();
		}
		return $this->bcp_dcha;
	}
	/**
	 * estableix el valor de l'atribut bcp_dcha de Direccion
	 *
	 * @param boolean bcp_dcha='f' optional
	 */
	function setCp_dcha($bcp_dcha='f') {
		$this->bcp_dcha = $bcp_dcha;
	}
	/**
	 * Recupera l'atribut ilatitud de Direccion
	 *
	 * @return string ilatitud
	 */
	function getLatitud() {
		if (!isset($this->ilatitud)) {
			$this->DBCarregar();
		}
		return $this->ilatitud;
	}
	/**
	 * estableix el valor de l'atribut ilatitud de Direccion
	 *
	 * @param string ilatitud='' optional
	 */
	function setLatitud($ilatitud='') {
		$this->ilatitud = $ilatitud;
	}
	/**
	 * Recupera l'atribut ilongitud de Direccion
	 *
	 * @return string ilongitud
	 */
	function getLongitud() {
		if (!isset($this->ilongitud)) {
			$this->DBCarregar();
		}
		return $this->ilongitud;
	}
	/**
	 * estableix el valor de l'atribut ilongitud de Direccion
	 *
	 * @param string ilongitud='' optional
	 */
	function setLongitud($ilongitud='') {
		$this->ilongitud = $ilongitud;
	}
	/**
	 * Recupera l'atribut iplano_doc de Direccion
	 *
	 * @return string iplano_doc
	 */
	function getPlano_doc() {
		if (!isset($this->iplano_doc)) {
			$this->DBCarregar();
		}
		return $this->iplano_doc;
	}
	/**
	 * estableix el valor de l'atribut iplano_doc de Direccion
	 *
	 * @param string iplano_doc='' optional
	 */
	function setPlano_doc($iplano_doc='') {
		$this->iplano_doc = $iplano_doc;
	}
	/**
	 * Recupera l'atribut splano_extension de Direccion
	 *
	 * @return string splano_extension
	 */
	function getPlano_extension() {
		if (!isset($this->splano_extension)) {
			$this->DBCarregar();
		}
		return $this->splano_extension;
	}
	/**
	 * estableix el valor de l'atribut splano_extension de Direccion
	 *
	 * @param string splano_extension='' optional
	 */
	function setPlano_extension($splano_extension='') {
		$this->splano_extension = $splano_extension;
	}
	/**
	 * Recupera l'atribut splano_nom de Direccion
	 *
	 * @return string splano_nom
	 */
	function getPlano_nom() {
		if (!isset($this->splano_nom)) {
			$this->DBCarregar();
		}
		return $this->splano_nom;
	}
	/**
	 * estableix el valor de l'atribut splano_nom de Direccion
	 *
	 * @param string splano_nom='' optional
	 */
	function setPlano_nom($splano_nom='') {
		$this->splano_nom = $splano_nom;
	}
	/**
	 * Recupera l'atribut snom_sede de Direccion
	 *
	 * @return string snom_sede
	 */
	function getNom_sede() {
		if (!isset($this->snom_sede)) {
			$this->DBCarregar();
		}
		return $this->snom_sede;
	}
	/**
	 * estableix el valor de l'atribut snom_sede de Direccion
	 *
	 * @param string snom_sede='' optional
	 */
	function setNom_sede($snom_sede='') {
		$this->snom_sede = $snom_sede;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	public function planoDownload() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$id_direccion = $this->getId_direccion();
		
		$sql="SELECT plano_nom,plano_extension,plano_doc FROM $nom_tabla WHERE id_direccion=?";
		//echo "sql: $sql_update<br>";
		$stmt = $oDbl->prepare($sql);
		$stmt->execute(array($id_direccion));
		$stmt->bindColumn(1, $plano_nom, \PDO::PARAM_STR, 256);
		$stmt->bindColumn(2, $plano_extension, \PDO::PARAM_STR, 256);
		$stmt->bindColumn(3, $plano_doc, \PDO::PARAM_LOB);
		$stmt->fetch(\PDO::FETCH_BOUND);

		return [
			'plano_nom' => $plano_nom,
			'plano_extension' => $plano_extension,
			'plano_doc' => $plano_doc,
		];
	}
	
	public function planoUpload($nom,$extension,$fichero) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$id_direccion = $this->getId_direccion();
	
		$nom=empty($nom)? '' : $nom;
		$extension=empty($extension)? '' : $extension;
		$fichero=empty($fichero)? '' : $fichero;

		$sql_update="UPDATE $nom_tabla SET plano_nom=:plano_nom,plano_extension=:plano_extension,plano_doc=:plano_doc WHERE id_direccion=$id_direccion";

		$oDBSt_a=$oDbl->prepare($sql_update);
		$oDBSt_a->bindParam(":plano_nom", $nom, \PDO::PARAM_STR);
		$oDBSt_a->bindParam(":plano_extension", $extension, \PDO::PARAM_STR);
		$oDBSt_a->bindParam(":plano_doc", $fichero, \PDO::PARAM_LOB);

		$oDBSt_a->execute();
	}

	public function planoBorrar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$id_direccion = $this->getId_direccion();
		
		$nom=NULL;
		$extension=NULL;
		$fichero=NULL;

		$sql_update="UPDATE $nom_tabla SET plano_nom=:plano_nom,plano_extension=:plano_extension,plano_doc=:plano_doc WHERE id_direccion=$id_direccion";

		$oDBSt_a=$oDbl->prepare($sql_update);
		$oDBSt_a->bindParam(":plano_nom", $nom, \PDO::PARAM_STR);
		$oDBSt_a->bindParam(":plano_extension", $extension, \PDO::PARAM_STR);
		$oDBSt_a->bindParam(":plano_doc", $fichero, \PDO::PARAM_LOB);

		$oDBSt_a->execute();
	}
	
	/**
	 * texte amb l'adreça formatejada
	 *
	 */
	public function getDireccionPostal($salto_linea='<br>',$espacio=' ') {
	    $this->DBCarregar();
		$txt = '';
		$rtn = $salto_linea;
		$spc = $espacio;
		if (isset($this->sdireccion)) $txt .= $this->sdireccion.$rtn;
		if (!empty($this->scp_dcha) && $this->scp_dcha == 't') {
			if (!empty($this->spoblacion)) $txt .= $this->spoblacion.$spc;
			if (!empty($this->sc_p)) $txt .= $this->sc_p;
		} else {
			if (!empty($this->sc_p)) $txt .= $this->sc_p.$spc;
			if (!empty($this->spoblacion)) $txt .= $this->spoblacion;
		}
		$txt .= $rtn;
		if (!empty($this->sa_p)) $txt .= $this->sa_p.$rtn;
		
		return $txt;
	}

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oDireccionSet = new core\Set();

		$oDireccionSet->add($this->getDatosDireccion());
		$oDireccionSet->add($this->getDatosC_p());
		$oDireccionSet->add($this->getDatosPoblacion());
		$oDireccionSet->add($this->getDatosProvincia());
		$oDireccionSet->add($this->getDatosA_p());
		$oDireccionSet->add($this->getDatosPais());
		$oDireccionSet->add($this->getDatosF_direccion());
		$oDireccionSet->add($this->getDatosObserv());
		$oDireccionSet->add($this->getDatosCp_dcha());
		$oDireccionSet->add($this->getDatosLatitud());
		$oDireccionSet->add($this->getDatosLongitud());
		$oDireccionSet->add($this->getDatosPlano_doc());
		$oDireccionSet->add($this->getDatosPlano_extension());
		$oDireccionSet->add($this->getDatosPlano_nom());
		$oDireccionSet->add($this->getDatosNom_sede());
		return $oDireccionSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut sdireccion de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosDireccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'direccion'));
		$oDatosCampo->setEtiqueta(_("dirección"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sc_p de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosC_p() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'c_p'));
		$oDatosCampo->setEtiqueta(_("código postal"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spoblacion de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPoblacion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'poblacion'));
		$oDatosCampo->setEtiqueta(_("población"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sprovincia de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosProvincia() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'provincia'));
		$oDatosCampo->setEtiqueta(_("provincia"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sa_p de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosA_p() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'a_p'));
		$oDatosCampo->setEtiqueta(_("ap. correos"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut spais de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPais() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'pais'));
		$oDatosCampo->setEtiqueta(_("país"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut df_direccion de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosF_direccion() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'f_direccion'));
		$oDatosCampo->setEtiqueta(_("fecha dirección"));
        $oDatosCampo->setTipo('fecha');
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sobserv de Direccion
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
	/**
	 * Recupera les propietats de l'atribut bcp_dcha de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosCp_dcha() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'cp_dcha'));
		$oDatosCampo->setEtiqueta(_("cp dcha"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ilatitud de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLatitud() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'latitud'));
		$oDatosCampo->setEtiqueta(_("latitud"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut ilongitud de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosLongitud() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'longitud'));
		$oDatosCampo->setEtiqueta(_("longitud"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iplano_doc de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPlano_doc() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'plano_doc'));
		$oDatosCampo->setEtiqueta(_("plano documento"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut splano_extension de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPlano_extension() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'plano_extension'));
		$oDatosCampo->setEtiqueta(_("plano extensión"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut splano_nom de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosPlano_nom() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'plano_nom'));
		$oDatosCampo->setEtiqueta(_("plano nombre"));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut snom_sede de Direccion
	 * en una clase del tipus DatosCampo
	 *
	 * @return core\DatosCampo
	 */
	function getDatosNom_sede() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'nom_sede'));
		$oDatosCampo->setEtiqueta(_("nombre de la sede"));
		return $oDatosCampo;
	}
}
?>
