<?php
namespace profesores\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula d_docencia_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */
/**
 * Classe que implementa l'entitat d_docencia_stgr
 *
 * @package delegación
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created 28/10/2014
 */
class ProfesorDocencia Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/**
	 * aPrimary_key de ProfesorDocencia
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ProfesorDocencia
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * Id_schema de ProfesorDocencia
	 *
	 * @var integer
	 */
	 private $iid_schema;
	/**
	 * Id_item de ProfesorDocencia
	 *
	 * @var integer
	 */
	 private $iid_item;
	/**
	 * Id_nom de ProfesorDocencia
	 *
	 * @var integer
	 */
	 private $iid_nom;
	/**
	 * Id_asignatura de ProfesorDocencia
	 *
	 * @var integer
	 */
	 private $iid_asignatura;
	/**
	 * Id_activ de ProfesorDocencia
	 *
	 * @var integer
	 */
	 private $iid_activ;
	/**
	 * Tipo de ProfesorDocencia
	 *
	 * @var string
	 */
	 private $stipo;
	/**
	 * Curso de ProfesorDocencia
	 *
	 * @var string
	 */
	 private $scurso;
	/**
	 * Acta de ProfesorDocencia
	 *
	 * @var string
	 */
	 private $sacta;
	/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */
	/**
	 * oDbl de ProfesorDocencia
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ProfesorDocencia
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
		$this->setNomTabla('d_docencia_stgr');
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
		$aDades['tipo'] = $this->stipo;
		$aDades['curso'] = $this->scurso;
		$aDades['acta'] = $this->sacta;
		array_walk($aDades, 'core\poner_null');

		if ($bInsert === false) {
			//UPDATE
			$update="
					id_asignatura            = :id_asignatura,
					id_activ                 = :id_activ,
					tipo                     = :tipo,
					curso                    = :curso,
					acta                     = :acta";
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE id_item='$this->iid_item' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'ProfesorDocencia.update.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'ProfesorDocencia.update.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT
			array_unshift($aDades, $this->iid_nom);
			$campos="(id_nom,id_asignatura,id_activ,tipo,curso,acta)";
			$valores="(:id_nom,:id_asignatura,:id_activ,:tipo,:curso,:acta)";		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = 'ProfesorDocencia.insertar.prepare';
				$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = 'ProfesorDocencia.insertar.execute';
					$_SESSION['oGestorErrores']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
			$this->id_item = $oDbl->lastInsertId('d_docencia_stgr_id_item_seq');
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
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE id_item='$this->iid_item' AND id_nom='$this->iid_nom'")) === false) {
				$sClauError = 'ProfesorDocencia.carregar';
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
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE id_item='$this->iid_item' AND id_nom='$this->iid_nom'")) === false) {
			$sClauError = 'ProfesorDocencia.eliminar';
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
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;
		if (array_key_exists('id_item',$aDades)) $this->setId_item($aDades['id_item']);
		if (array_key_exists('id_nom',$aDades)) $this->setId_nom($aDades['id_nom']);
		if (array_key_exists('id_asignatura',$aDades)) $this->setId_asignatura($aDades['id_asignatura']);
		if (array_key_exists('id_activ',$aDades)) $this->setId_activ($aDades['id_activ']);
		if (array_key_exists('tipo',$aDades)) $this->setTipo($aDades['tipo']);
		if (array_key_exists('curso',$aDades)) $this->setCurso($aDades['curso']);
		if (array_key_exists('acta',$aDades)) $this->setActa($aDades['acta']);
	}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de ProfesorDocencia en un array
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
	 * Recupera las claus primàries de ProfesorDocencia en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('id_item' => $this->iid_item,'id_nom' => $this->iid_nom);
		}
		return $this->aPrimary_key;
	}

	/**
	 * Recupera l'atribut iid_schema de ProfesorDocencia
	 *
	 * @return integer iid_schema
	 */
	function getId_schema() {
		if (!isset($this->iid_schema)) {
			$this->DBCarregar();
		}
		return $this->iid_schema;
	}
	/**
	 * estableix el valor de l'atribut iid_schema de ProfesorDocencia
	 *
	 * @param integer iid_schema='' optional
	 */
	function setId_schema($iid_schema='') {
		$this->iid_schema = $iid_schema;
	}
	/**
	 * Recupera l'atribut iid_item de ProfesorDocencia
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
	 * estableix el valor de l'atribut iid_item de ProfesorDocencia
	 *
	 * @param integer iid_item
	 */
	function setId_item($iid_item) {
		$this->iid_item = $iid_item;
	}
	/**
	 * Recupera l'atribut iid_nom de ProfesorDocencia
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
	 * estableix el valor de l'atribut iid_nom de ProfesorDocencia
	 *
	 * @param integer iid_nom
	 */
	function setId_nom($iid_nom) {
		$this->iid_nom = $iid_nom;
	}
	/**
	 * Recupera l'atribut iid_asignatura de ProfesorDocencia
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
	 * estableix el valor de l'atribut iid_asignatura de ProfesorDocencia
	 *
	 * @param integer iid_asignatura='' optional
	 */
	function setId_asignatura($iid_asignatura='') {
		$this->iid_asignatura = $iid_asignatura;
	}
	/**
	 * Recupera l'atribut iid_activ de ProfesorDocencia
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
	 * estableix el valor de l'atribut iid_activ de ProfesorDocencia
	 *
	 * @param integer iid_activ='' optional
	 */
	function setId_activ($iid_activ='') {
		$this->iid_activ = $iid_activ;
	}
	/**
	 * Recupera l'atribut stipo de ProfesorDocencia
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
	 * estableix el valor de l'atribut stipo de ProfesorDocencia
	 *
	 * @param string stipo='' optional
	 */
	function setTipo($stipo='') {
		$this->stipo = $stipo;
	}
	/**
	 * Recupera l'atribut scurso de ProfesorDocencia
	 *
	 * @return string scurso
	 */
	function getCurso() {
		if (!isset($this->scurso)) {
			$this->DBCarregar();
		}
		return $this->scurso;
	}
	/**
	 * estableix el valor de l'atribut scurso de ProfesorDocencia
	 *
	 * @param string scurso='' optional
	 */
	function setCurso($scurso='') {
		$this->scurso = $scurso;
	}
	/**
	 * Recupera l'atribut sacta de ProfesorDocencia
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
	 * estableix el valor de l'atribut sacta de ProfesorDocencia
	 *
	 * @param string sacta='' optional
	 */
	function setActa($sacta='') {
		$this->sacta = $sacta;
	}
	/* METODES GET i SET D'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$oProfesorDocenciaSet = new core\Set();

		$oProfesorDocenciaSet->add($this->getDatosId_asignatura());
		$oProfesorDocenciaSet->add($this->getDatosId_activ());
		$oProfesorDocenciaSet->add($this->getDatosTipo());
		$oProfesorDocenciaSet->add($this->getDatosCurso());
		$oProfesorDocenciaSet->add($this->getDatosActa());
		return $oProfesorDocenciaSet->getTot();
	}

	/**
	 * Recupera les propietats de l'atribut iid_asignatura de ProfesorDocencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_asignatura() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_asignatura'));
		$oDatosCampo->setEtiqueta(_("asignatura"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('asignaturas\model\Asignatura'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('nombre_corto'); // clave con la que crear el objeto relacionado
		$oDatosCampo->setArgument3('getListaAsignaturas'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut iid_activ de ProfesorDocencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosId_activ() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'id_activ'));
		$oDatosCampo->setEtiqueta(_("actividad"));
		$oDatosCampo->setTipo('opciones');
		$oDatosCampo->setArgument('actividades\model\Actividad'); // nombre del objeto relacionado
		$oDatosCampo->setArgument2('nom_activ'); // clave con la que crear el objeto relacionado
		$oDatosCampo->setArgument3('getListaActividadesEstudios'); // método con que crear la lista de opciones del Gestor objeto relacionado.
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut stipo de ProfesorDocencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosTipo() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'tipo'));
		$oDatosCampo->setEtiqueta(_("tipo"));
		$oDatosCampo->setTipo('array');
        $oDatosCampo->setArgument(array( 'v'=> _("verano"), 'i'=> _("invierno") , 'p'=> _("preceptor") ));
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut scurso de ProfesorDocencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosCurso() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'curso'));
		$oDatosCampo->setEtiqueta(_("curso"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
	/**
	 * Recupera les propietats de l'atribut sacta de ProfesorDocencia
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatosActa() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\DatosCampo(array('nom_tabla'=>$nom_tabla,'nom_camp'=>'acta'));
		$oDatosCampo->setEtiqueta(_("acta"));
		$oDatosCampo->setTipo('texto');
		$oDatosCampo->setArgument(30);
		return $oDatosCampo;
	}
}
?>
