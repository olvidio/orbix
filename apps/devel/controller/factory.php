<?php
namespace devel\controller;
use core;
/**
  * programa per generar les classes a partir de la taula
  *
  */
/**
* Para asegurar que inicia la sesion, y poder acceder a los permisos
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

if (!isset($_POST['tabla'])) exit("Ha de dir quina taula");
// si la tabla tiene el schema, hay que separalo:
$schema_sql = '';
$tabla = $_POST['tabla'];
$schema = strtok($tabla,'.');
if ($schema !== $tabla) {
	$tabla = strtok('.');
	$schema_sql = "and n.nspname='$schema' ";
} else {
	$schema = 'public';
}


if (isset($_POST['db'])) {
	switch($_POST['db']) {
		case "filmoteca":
			$oDbl=$oDBT;
			$oDB_txt='oDBT';
			$prefix='';
		break;
		case "comun":
			$oDbl=$oDBC;
			$oDB_txt='oDBC';
			$prefix='';
			switch ($schema) {
				case 'public':
					$oDB_txt='oDBPC';
					break;
				case 'resto':
					$oDB_txt='oDBRC';
					break;
				case 'H-dlb':
					$oDB_txt='oDBC';
					break;
			}
			break;
		case "sv":
		case "sf":
			$oDbl=$oDB;
			$oDB_txt='oDB';
			$prefix='';
			switch ($schema) {
				case 'publicv':
				case 'publicf':
					$oDB_txt='oDBP';
					break;
				case 'restov':
				case 'restof':
					$oDB_txt='oDBR';
					break;
				case 'H-dlbv':
				case 'H-dlbf':
					$oDB_txt='oDB';
					break;
			}
		break;
		case "test-dl_interior":
		case "dl_interior":
			$oDbl=$oDB;
			$oDB_txt='oDB';
			$prefix='';
			switch ($schema) {
				case 'public':
					$oDB_txt='oDBPC';
					break;
				case 'publicv':
				case 'publicf':
					$oDB_txt='oDBP';
					break;
				case 'resto':
					$oDB_txt='oDBRC';
					break;
				case 'restov':
				case 'restof':
					$oDB_txt='oDBR';
					break;
				case 'H-dlb':
					$oDB_txt='oDBC';
					break;
				case 'H-dlbv':
				case 'H-dlbf':
					$oDB_txt='oDB';
					break;
			}
		break;
		case "test-actividades":
		case "actividades":
			$oDbl=$oDBA;
			$oDB_txt='oDBA';
			$prefix='';
			break;
		case "registro":
			$oDbl=$oDBR;
			$oDB_txt='oDBR';
			$prefix='';
			break;
		case "documentos":
			$oDbl=$oDBD;
			$oDB_txt='oDBD';
			$prefix='';
			break;
		case "dlbf":
			$oDbl=$oDBF;
			$oDB_txt='oDBF';
			$prefix='';
			break;
		default:
			exit("Ha de dir quina base de dades");
	}
} else {
	exit("Ha de dir quina base de dades");
}


$clase= isset($_POST['clase'])? $_POST['clase'] : $tabla;
if (isset($_POST['clase_plural']) && $_POST['clase_plural']!='') {
	$clase_plural=$_POST['clase_plural'];
} else {
	//plural de la clase
	if (preg_match('/[aeiou]$/',$clase)) {
		$clase_plural=$clase.'s';
	} else {
		$clase_plural=$clase.'es';
	}
}

$grupo= isset($_POST['grupo'])? $_POST['grupo'] : "actividades";
$aplicacion= isset($_POST['aplicacion'])? $_POST['aplicacion'] : "delegación";

//busco les claus primaries
$aClaus=core\primaryKey($oDbl,$_POST['tabla']);

$sql="SELECT 
				a.attnum,
				a.attname AS field, 
				t.typname AS type, 
				a.attlen AS length,
				a.atttypmod AS lengthvar,
				a.attnotnull AS notnull
			FROM 
				pg_catalog.pg_class c,
				pg_catalog.pg_attribute a,
				pg_catalog.pg_type t,
				pg_catalog.pg_namespace n
			WHERE 
				c.relname = '$tabla'
				and a.attnum > 0
				and a.attrelid = c.oid
				and a.atttypid = t.oid
				and n.oid = c.relnamespace
				and n.nspname='$schema'
			ORDER BY a.attnum
";

$atributs='
	/**
	 * aPrimary_key de '.$clase.'
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de '.$clase.'
	 *
	 * @var array
	 */
	 private $aDades;
';
$c=0;
$cl=0;
$id_seq="";
$id_seq2="";
$guardar="";
$update="";
$campos="";
$valores="";
$exists="";
$gets="";
$altres_gets="";
$altres_gets_set="";
$query_if="";
$err_bool="";
$a_auto=array();
foreach($oDbl->query($sql) as $row) {
	$nomcamp=$row['field'];
	$NomCamp=ucwords($nomcamp);
	$tipo=$row['type'];	
	$not_null=$row['notnull'];

	$sql_get_default = "SELECT d.adsrc AS rowdefault
				FROM pg_catalog.pg_attrdef d,
					 pg_catalog.pg_class c,
					 pg_catalog.pg_namespace n
				WHERE 
					c.relname = '$tabla'
					and c.oid = d.adrelid
					and n.oid = c.relnamespace
					and n.nspname='$schema'
					and d.adnum =". $row['attnum'];

	//echo "sql_def: $sql_get_default<br>";
	$default=$oDbl->query($sql_get_default)->fetchColumn();
	$auto=0;
	if (!empty($default)) { //nomes agafo un. li dono preferencia al id_local
		if(preg_match("/id_local\('(\w+)'.*$/",$default,$matches) || preg_match("/id_ubi\('(\w+)'.*$/",$default,$matches)) {
			$id_seq=$matches[1];
			$auto=1;
			$a_auto[]=$nomcamp;
		} else {
			if(preg_match("/nextval\('(\w+)'.*$/",$default,$matches)) {
				$id_seq2=$matches[1];
				$auto=1;
				$a_auto[]=$nomcamp;
			}
		}
	}
	//echo "${_POST['ficha']}\n$nomcamp_post\n";

	switch($tipo) {
		case 'int4':
		case 'int2':
			$tipo_db='integer';
			$tip='i';
			$tip_val='';
			break;
		case 'text':
		case 'varchar':
			$tipo_db='string';
			$tip='s';
			$tip_val='';
			break;
		case 'date':
			$tipo_db='date';
			$tip='d';
			$tip_val='';
			break;
		case 'time':
			$tipo_db='time';
			$tip='t';
			$tip_val='';
			break;
		case 'bool':
			$tipo_db='boolean';
			$tip='b';
			$tip_val='f';
			break;
	}
	$atributs.='
	/**
	 * '.$NomCamp.' de '.$clase.'
	 *
	 * @var '.$tipo_db.'
	 */
	 private $'.$tip.$nomcamp.';';

	$gets.='
	/**
	 * Recupera l\'atribut '.$tip.$nomcamp.' de '.$clase.'
	 *
	 * @return '.$tipo_db.' '.$tip.$nomcamp.'
	 */
	function get'.$NomCamp.'() {
		if (!isset($this->'.$tip.$nomcamp.')) {
			$this->DBCarregar();
		}
		return $this->'.$tip.$nomcamp.';
	}';

	if (in_array($nomcamp,$aClaus)) {
		$aClaus2[$nomcamp]=$tip.$nomcamp;
		$gets.='
	/**
	 * estableix el valor de l\'atribut '.$tip.$nomcamp.' de '.$clase.'
	 *
	 * @param '.$tipo_db.' '.$tip.$nomcamp.'
	 */
	function set'.$NomCamp.'($'.$tip.$nomcamp.') {
		$this->'.$tip.$nomcamp.' = $'.$tip.$nomcamp.';
	}';
	} else {
		$gets.='
	/**
	 * estableix el valor de l\'atribut '.$tip.$nomcamp.' de '.$clase.'
	 *
	 * @param '.$tipo_db.' '.$tip.$nomcamp.'=\''.$tip_val.'\' optional
	 */
	function set'.$NomCamp.'($'.$tip.$nomcamp.'=\''.$tip_val.'\') {
		$this->'.$tip.$nomcamp.' = $'.$tip.$nomcamp.';
	}';
		$altres_gets.='
	/**
	 * Recupera les propietats de l\'atribut '.$tip.$nomcamp.' de '.$clase.'
	 * en una clase del tipus DatosCampo
	 *
	 * @return oject DatosCampo
	 */
	function getDatos'.$NomCamp.'() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\\DatosCampo(array(\'nom_tabla\'=>$nom_tabla,\'nom_camp\'=>\''.$nomcamp.'\'));
		$oDatosCampo->setEtiqueta(_("'.$nomcamp.'"));
		return $oDatosCampo;
	}';
		$altres_gets_set.="\n\t\t".'$o'.$clase.'Set->add($this->getDatos'.$NomCamp.'());';

	}

	$exists.="\n\t\t".'if (array_key_exists(\''.$nomcamp.'\',$aDades)) $this->set'.$NomCamp.'($aDades[\''.$nomcamp.'\']);';

	if (!in_array($nomcamp,$aClaus)) {
		if ($auto != 1) { // si tiene sequencia no pongo el campo en el update.
			if ($tip=='b') {
				$err_bool.="\n\t\t".'if (empty($aDades[\''.$nomcamp.'\']) || ($aDades[\''.$nomcamp.'\'] === \'off\') || ($aDades[\''.$nomcamp.'\'] === false) || ($aDades[\''.$nomcamp.'\'] === \'f\')) { $aDades[\''.$nomcamp.'\']=\'f\'; } else { $aDades[\''.$nomcamp.'\']=\'t\'; }';
			}
			$guardar.="\n\t\t".'$aDades[\''.$nomcamp.'\'] = $this->'.$tip.$nomcamp.';';
			if ($cl>0) $update.=",\n";
			$update.="\t\t\t\t\t".$nomcamp;
			// para intentar que los = salgan en la misma columna
			$n=strlen($nomcamp);
			for ($s=$n; $s<25; $s++) {
				$update.=" ";
			}
			$update.='= :'.$nomcamp;
			$cl++;
		}
	}
	if ($auto != 1) { // si tiene sequencia no pongo el campo en el insert.
		if ($c>0) $campos.=",";
		$campos.=$nomcamp;
		if ($c>0) $valores.=",";
		$valores.=':'.$nomcamp;
		$c++;
	}
}
$hoy=date("d/m/Y");


$txt="<?php
namespace $grupo\\model;
use core;
/**
 * Fitxer amb la Classe que accedeix a la taula $tabla
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created $hoy
 */
/**
 * Classe que implementa l'entitat $tabla
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created $hoy
 */
class $clase Extends core\ClasePropiedades {
	/* ATRIBUTS ----------------------------------------------------------------- */
";
$txt.=$atributs;
$txt.="\n\t".'/* ATRIBUTS QUE NO SÓN CAMPS------------------------------------------------- */';

$txt.='
	/**
	 * oDbl de '.$clase.'
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de '.$clase.'
	 *
	 * @var string
	 */
	 protected $sNomTabla;';

$i=0;
$claus_txt='';
$claus_txt2='';
$claus_if='';
$guardar_if='';
$where='';
$claus_isset='';
$claus_query="";
$claus_getPrimary="";
foreach($aClaus2 as $clau=>$nom_clau) {
	//$nom_clau="i".$clau;
	if (!empty($claus_txt)) $claus_txt.=",";
	$claus_txt.=$nom_clau;
	if ($i>0) $claus_txt2.=",\n\t\t\t\t\t\t\t";
	$claus_txt2.="'$clau' => ".'$aDades[\''.$clau.'\']';
	if ($i>0) $claus_if.="\n";
	switch (substr($nom_clau,0,1)) {
		case 'i':
			$claus_if.="\t\t\t\t".'if (($nom_id == \''.$clau.'\') && $val_id !== \'\') $this->'.$nom_clau.' = (int)$val_id; // evitem SQL injection fent cast a integer';
		break;
		case 's':
			$claus_if.="\t\t\t\t".'if (($nom_id == \''.$clau.'\') && $val_id !== \'\') $this->'.$nom_clau.' = (string)$val_id; // evitem SQL injection fent cast a string';
		break;
		case 'b':
			$claus_if.="\t\t\t\t".'if (($nom_id == \''.$clau.'\') && $val_id !== \'\') $this->'.$nom_clau.' = (bool)$val_id; // evitem SQL injection fent cast a boolean';
		break;
	}
	// si no es auto
	if (!in_array($clau,$a_auto)) {
		if (!empty($guardar_if)) $guardar_if.=", ";
 		$guardar_if.='$this->'.$nom_clau;
	}
	if ($i>0) $where.=" AND ";
	$where.=$clau.'=\'$this->'.$nom_clau.'\'';
	if ($i>0) $claus_isset.=" && ";
	$claus_isset.='isset($this->'.$nom_clau.')';
	$claus_query.="\n\t\t\t".'$'.$nom_clau.' = $aDades[\''.$clau.'\'];';
	if (!empty($claus_getPrimary)) $claus_getPrimary.=",";
	$claus_getPrimary.='\''.$clau.'\' => $this->'.$nom_clau;
	$i++;
}
$txt.='
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array '.$claus_txt.'
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */';

$txt.="\n\t".'function __construct($a_id=\'\') {
		$oDbl = $GLOBALS[\''.$oDB_txt.'\'];
		if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
'.$claus_if.'
			}';
if (count($aClaus2) > 1) { // per el cas de només una clau.
$txt.="\n\t\t}";
$txt.="\n\t\t";
$txt.='$this->setoDbl($oDbl);
		$this->setNomTabla(\''.$tabla.'\');';
$txt.="\n\t}";
} else {
$txt.="\t".'} else {
			if (isset($a_id) && $a_id !== \'\') {
				$this->'.$claus_txt.' = intval($a_id); // evitem SQL injection fent cast a integer
				$this->aPrimary_key = array(\''.$claus_txt.'\' => $this->'.$claus_txt.');
			}
		}
		$this->setoDbl($oDbl);
		$this->setNomTabla(\''.$tabla.'\');
	}';
}

$txt.='

	/* METODES PUBLICS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l\'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar(\'guardar\') === false) { $bInsert=true; } else { $bInsert=false; }
		$aDades=array();';
$txt.=$guardar;
$txt.='
		array_walk($aDades, \'core\\poner_null\');';
if ($err_bool) {
	$txt.="\n\t\t//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:";
	$txt.=$err_bool;
}
$txt.="\n\n\t\t".'if ($bInsert === false) {
			//UPDATE
			$update="
';
$txt.=$update.'";';
$txt.='
			if (($qRs = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE '.$where.'")) === false) {
				$sClauError = \''.$clase.'.update.prepare\';
				$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = \''.$clase.'.update.execute\';
					$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}
		} else {
			// INSERT';
if (!empty($guardar_if)) {
	$txt.="\n\t\t\t".'array_unshift($aDades, '.$guardar_if.');';
}
$txt.="\n\t\t\t".'$campos="(';
$txt.=$campos.')";'."\n";
$txt.="\t\t\t".'$valores="(';
$txt.=$valores.')";';
$txt.='		
			if (($qRs = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === false) {
				$sClauError = \''.$clase.'.insertar.prepare\';
				$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			} else {
				if ($qRs->execute($aDades) === false) {
					$sClauError = \''.$clase.'.insertar.execute\';
					$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
					return false;
				}
			}';
if ($id_seq || $id_seq2) {
	if (empty($id_seq2)) {
		$id_seq=$id_seq;
		$ccc= 'i'.end($a_auto);
	} else {
		$id_seq= $id_seq2;
		$ccc= end($a_auto);
	}
	$txt.="\n\t\t\t".'$this->'.$ccc.' = $oDbl->lastInsertId(\''.$id_seq.'\');';
}
$txt.="\n\t\t".'}
		$this->setAllAtributes($aDades);
		return true;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l\'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ('.$claus_isset.') {
			if (($qRs = $oDbl->query("SELECT * FROM $nom_tabla WHERE '.$where.'")) === false) {
				$sClauError = \''.$clase.'.carregar\';
				$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return false;
			}
			$aDades = $qRs->fetch(\\PDO::FETCH_ASSOC);
			switch ($que) {
				case \'tot\':
					$this->aDades=$aDades;
					break;
				case \'guardar\':
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
	 * Elimina el registre de la base de dades corresponent a l\'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($qRs = $oDbl->exec("DELETE FROM $nom_tabla WHERE '.$where.'")) === false) {
			$sClauError = \''.$clase.'.eliminar\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		return true;
	}
	
	/* METODES ALTRES  ----------------------------------------------------------*/
';

$txt.='	/* METODES PRIVATS ----------------------------------------------------------*/

	/**
	 * Estableix el valor de tots els atributs
	 *
	 * @param array $aDades
	 */
	function setAllAtributes($aDades) {
		if (!is_array($aDades)) return;';
$txt.=$exists;
$txt.="\n\t".'}

	/* METODES GET i SET --------------------------------------------------------*/

	/**
	 * Recupera tots els atributs de '.$clase.' en un array
	 *
	 * @return array aDades
	 */
	function getTot() {
		if (!is_array($this->aDades)) {
			$this->DBCarregar(\'tot\');
		}
		return $this->aDades;
	}

	/**
	 * Recupera las claus primàries de '.$clase.' en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array('.$claus_getPrimary.');
		}
		return $this->aPrimary_key;
	}
';

$txt.=$gets;

$txt.='
	/* METODES GET i SET D\'ATRIBUTS QUE NO SÓN CAMPS -----------------------------*/

	/**
	 * Retorna una col·lecció d\'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$o'.$clase.'Set = new core\Set();
';
$txt.=$altres_gets_set;
$txt.='
		return $o'.$clase.'Set->getTot();
	}


';
$txt.=$altres_gets;
$txt.='
}
?>';

/* ESCRIURE LA CLASSSE ------------------------------------------------ */
$filename = '/var/www/orbix/apps/'.$grupo.'/model/'.strtolower($_POST['clase']).'.class.php';


// In our example we're opening $filename in append mode.
// The file pointer is at the bottom of the file hence
// that's where $somecontent will go when we fwrite() it.
if (!$handle = fopen($filename, 'a')) {
	 echo "Cannot open file ($filename)";
	 exit;
}

// Write $somecontent to our opened file.
if (fwrite($handle, $txt) === FALSE) {
	echo "Cannot write to file ($filename)";
	exit;
}

echo "Success, wrote (somecontent) to file ($filename)";

fclose($handle);

/* CONSTRUIR EL GESTOR ------------------------------------------------ */
$gestor="Gestor".ucfirst($clase);
$txt2="<?php
namespace $grupo\\model;
use core;
/**
 * $gestor
 *
 * Classe per gestionar la llista d'objectes de la clase $clase
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created $hoy
 */

class $gestor Extends core\ClaseGestor {
	/* ATRIBUTS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */
";

$txt2.='

	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS[\''.$oDB_txt.'\'];
		$this->setoDbl($oDbl);
		$this->setNomTabla(\''.$tabla.'\');
	}


	/* METODES PUBLICS -----------------------------------------------------------*/
';

$txt2.='
	/**
	 * retorna l\'array d\'objectes de tipus '.$clase.'
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d\'objectes de tipus '.$clase.'
	 */
	function get'.$clase_plural.'Query($sQuery=\'\') {
		$oDbl = $this->getoDbl();
		$o'.$clase.'Set = new core\Set();
		if (($oDblSt = $oDbl->query($sQuery)) === false) {
			$sClauError = \''.$gestor.'.query\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {';
$txt2.="\n\t\t\t".'$a_pkey = array('.$claus_txt2.');';
$txt2.="\n\t\t\t".'$o'.$clase.'= new '.$clase.'($a_pkey);';
$txt2.='
			$o'.$clase.'->setAllAtributes($aDades);
			$o'.$clase.'Set->add($o'.$clase.');
		}
		return $o'.$clase.'Set->getTot();
	}
';

$txt2.='
	/**
	 * retorna l\'array d\'objectes de tipus '.$clase.'
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d\'objectes de tipus '.$clase.'
	 */
	function get'.$clase_plural.'($aWhere=array(),$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$o'.$clase.'Set = new core\Set();
		$oCondicion = new core\Condicion();
		$aCondi = array();';
$txt2.='
		foreach ($aWhere as $camp => $val) {
			if ($camp == \'_ordre\') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : \'\';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == \'BETWEEN\' || $sOperador == \'IS NULL\' || $sOperador == \'IS NOT NULL\') unset($aWhere[$camp]);
		}';

$txt2.="\n\t\t".'$sCondi = implode(\' AND \',$aCondi);
		if ($sCondi!=\'\') $sCondi = " WHERE ".$sCondi;
		if (isset($GLOBALS[\'oGestorSessioDelegación\'])) {
		   	$sLimit = $GLOBALS[\'oGestorSessioDelegación\']->getLimitPaginador(\'a_actividades\',$sCondi,$aWhere);
		} else {
			$sLimit=\'\';
		}
		if ($sLimit===false) return;
		$sOrdre = \'\';
		if (isset($aWhere[\'_ordre\']) && $aWhere[\'_ordre\']!=\'\') $sOrdre = \' ORDER BY \'.$aWhere[\'_ordre\'];
		if (isset($aWhere[\'_ordre\'])) unset($aWhere[\'_ordre\']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === false) {
			$sClauError = \''.$gestor.'.llistar.prepare\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		if (($oDblSt->execute($aWhere)) === false) {
			$sClauError = \''.$gestor.'.llistar.execute\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return false;
		}
		foreach ($oDblSt as $aDades) {';
$txt2.="\n\t\t\t".'$a_pkey = array('.$claus_txt2.');';
$txt2.="\n\t\t\t".'$o'.$clase.'= new '.$clase.'($a_pkey);';
$txt2.='
			$o'.$clase.'->setAllAtributes($aDades);
			$o'.$clase.'Set->add($o'.$clase.');
		}
		return $o'.$clase.'Set->getTot();
	}
';
$txt2.='
	/* METODES PROTECTED --------------------------------------------------------*/

	/* METODES GET i SET --------------------------------------------------------*/
}
?>';
/* ESCRIURE LA CLASSSE ------------------------------------------------ */
$filename = '/var/www/orbix/apps/'.$grupo.'/model/gestor'.strtolower($_POST['clase']).'.class.php';


// In our example we're opening $filename in append mode.
// The file pointer is at the bottom of the file hence
// that's where $somecontent will go when we fwrite() it.
if (!$handle = fopen($filename, 'a')) {
	 echo "Cannot open file ($filename)";
	 exit;
}

// Write $somecontent to our opened file.
if (fwrite($handle, $txt2) === FALSE) {
	echo "Cannot write to file ($filename)";
	exit;
}

echo "<br>Success, wrote gestor to file ($filename)";

fclose($handle);
?>

