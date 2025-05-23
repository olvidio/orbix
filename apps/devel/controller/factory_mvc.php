<?php

namespace devel\controller;

use core;
use core\ConfigGlobal;
use web;

/**
 * programa per generar les classes a partir de la taula
 *
 */
/**
 * Para asegurar que inicia la sesion, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
require_once("apps/devel/controller/func_factory.php");
// FIN de  Cabecera global de URL de controlador ********************************

$Qdb = (string)filter_input(INPUT_POST, 'db');
$Qtabla = (string)filter_input(INPUT_POST, 'tabla');
$Qclase = (string)filter_input(INPUT_POST, 'clase');
$Qclase_plural = (string)filter_input(INPUT_POST, 'clase_plural');
$Qgrupo = (string)filter_input(INPUT_POST, 'grupo');
$Qaplicacion = (string)filter_input(INPUT_POST, 'aplicacion');

if (empty($Qtabla)) {
    exit("Ha de dir quina taula");
}
// si la tabla tiene el schema, hay que separalo:
$schema_sql = '';
$tabla = $Qtabla;
$schema = strtok($tabla, '.');
if ($schema !== $tabla) {
    $tabla = strtok('.');
    $schema_sql = "and n.nspname='$schema' ";
} else {
    $schema = 'public';
}


if (isset($Qdb)) {
    switch ($Qdb) {
        case "tramity":
            $oDbl = $oDBT;
            $oDB_txt = 'oDBT';
            $prefix = '';
            break;
        case "comun":
            $oDbl = $oDBC;
            $oDB_txt = 'oDBC';
            $prefix = '';
            switch ($schema) {
                case 'public':
                    $oDB_txt = 'oDBPC';
                    break;
                case 'resto':
                    $oDB_txt = 'oDBRC';
                    break;
                case 'H-dlb':
                    $oDB_txt = 'oDBC';
                    break;
            }
            break;
        case "sv":
        case "sf":
            $oDbl = $oDB;
            $oDB_txt = 'oDB';
            $prefix = '';
            switch ($schema) {
                case 'publicv':
                case 'publicf':
                    $oDB_txt = 'oDBP';
                    break;
                case 'restov':
                case 'restof':
                    $oDB_txt = 'oDBR';
                    break;
                case 'H-dlbv':
                case 'H-dlbf':
                    $oDB_txt = 'oDB';
                    break;
            }
            break;
        case "test-dl_interior":
        case "dl_interior":
            $oDbl = $oDB;
            $oDB_txt = 'oDB';
            $prefix = '';
            switch ($schema) {
                case 'public':
                    $oDB_txt = 'oDBPC';
                    break;
                case 'publicv':
                case 'publicf':
                    $oDB_txt = 'oDBP';
                    break;
                case 'resto':
                    $oDB_txt = 'oDBRC';
                    break;
                case 'restov':
                case 'restof':
                    $oDB_txt = 'oDBR';
                    break;
                case 'H-dlb':
                    $oDB_txt = 'oDBC';
                    break;
                case 'H-dlbv':
                case 'H-dlbf':
                    $oDB_txt = 'oDB';
                    break;
            }
            break;
        case "test-actividades":
        case "actividades":
            $oDbl = $oDBA;
            $oDB_txt = 'oDBA';
            $prefix = '';
            break;
        case "registro":
            $oDbl = $oDBR;
            $oDB_txt = 'oDBR';
            $prefix = '';
            break;
        case "documentos":
            $oDbl = $oDBD;
            $oDB_txt = 'oDBD';
            $prefix = '';
            break;
        case "dlbf":
            $oDbl = $oDBF;
            $oDB_txt = 'oDBF';
            $prefix = '';
            break;
        default:
            exit("Ha de dir quina base de dades");
    }
} else {
    exit("Ha de dir quina base de dades");
}


$clase = !empty($Qclase) ? $Qclase : $tabla;
if (!empty($Qclase_plural)) {
    $clase_plural = $Qclase_plural;
} else {
    //plural de la clase
    if (preg_match('/[aeiou]$/', $clase)) {
        $clase_plural = $clase . 's';
    } else {
        $clase_plural = $clase . 'es';
    }
}

$grupo = !empty($Qgrupo) ? $Qgrupo : "actividades";
$aplicacion = !empty($Qaplicacion) ? $Qaplicacion : "delegación";

//busco les claus primaries
$aClaus = primaryKey($oDbl, $Qtabla);

$sql = "SELECT 
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

$atributs = '
	/**
	 * aPrimary_key de ' . $clase . '
	 *
	 * @var array
	 */
	 private $aPrimary_key;

	/**
	 * aDades de ' . $clase . '
	 *
	 * @var array
	 */
	 private $aDades;

	/**
	 * bLoaded de ' . $clase . '
	 *
	 * @var boolean
	 */
	 private $bLoaded = FALSE;

	/**
	 * Id_schema de ' . $clase . '
	 *
	 * @var integer
	 */
	 private $iid_schema;
';
$add_convert = FALSE;
$c = 0;
$cl = 0;
$id_seq = "";
$id_seq2 = "";
$guardar = "";
$update = "";
$campos = "";
$valores = "";
$exists = "";
$ToEmpty = "";
$gets = "";
$altres_gets = "";
$altres_gets_set = "";
$query_if = "";
$err_bool = "";
$a_auto = [];
foreach ($oDbl->query($sql) as $row) {
    $nomcamp = $row['field'];
    if ($nomcamp === 'id_schema') {
        continue;
    }
    $NomCamp = ucwords($nomcamp);
    $tipo = $row['type'];
    $not_null = $row['notnull'];

    $sql_get_default = "SELECT pg_get_expr(adbin, adrelid) AS rowdefault
				FROM pg_catalog.pg_attrdef d,
					 pg_catalog.pg_class c,
					 pg_catalog.pg_namespace n
				WHERE 
					c.relname = '$tabla'
					and c.oid = d.adrelid
					and n.oid = c.relnamespace
					and n.nspname='$schema'
					and d.adnum =" . $row['attnum'];

    //echo "sql_def: $sql_get_default<br>";
    $default = $oDbl->query($sql_get_default)->fetchColumn();
    $auto = 0;
    if (!empty($default)) { //nomes agafo un. li dono preferencia al id_local
        $matches = [];
        if (preg_match("/id_local\('(\w+)'.*$/", $default, $matches) || preg_match("/id_ubi\('(\w+)'.*$/", $default, $matches)) {
            $id_seq = $matches[1];
            $auto = 1;
            $a_auto[] = $nomcamp;
        } else {
            if (preg_match("/nextval\('(\w+)'.*$/", $default, $matches)) {
                $id_seq2 = $matches[1];
                $auto = 1;
                $a_auto[] = $nomcamp;
            }
        }
    }
    //echo "{$_POST['ficha']}\n$nomcamp_post\n";

    switch ($tipo) {
        case 'int8':
        case 'int4':
        case 'int2':
            $tipo_db = 'integer';
            $tip = 'i';
            $tip_val = '';
            break;
        case 'float4':
        case 'double':
        case 'numeric':
            $tipo_db = 'float';
            $tip = 'i';
            $tip_val = '';
            break;
        case 'text':
        case 'varchar':
            $tipo_db = 'string';
            $tip = 's';
            $tip_val = '';
            break;
        case 'date':
            $tipo_db = 'web\\DateTimeLocal';
            $tip = 'd';
            $tip_val = '';
            break;
        case 'time':
            $tipo_db = 'string time';
            $tip = 't';
            $tip_val = '';
            break;
        case 'bool':
            $tipo_db = 'boolean';
            $tip = 'b';
            $tip_val = 'f';
            break;
    }
    $atributs .= '
	/**
	 * ' . $NomCamp . ' de ' . $clase . '
	 *
	 * @var ' . $tipo_db . '
	 */
	 private $' . $tip . $nomcamp . ';';

    if ($tipo === 'date') {
        $gets .= '
	/**
	 * Recupera l\'atribut ' . $tip . $nomcamp . ' de ' . $clase . '
	 *
	 * @return ' . $tipo_db . ' ' . $tip . $nomcamp . '
	 */
	function get' . $NomCamp . '() {
		if (!isset($this->' . $tip . $nomcamp . ') && !$this->bLoaded) {
			$this->DBCarregar();
		}
		if (empty($this->' . $tip . $nomcamp . ')) {
			return new web\NullDateTimeLocal();
		}
        $oConverter = new core\Converter(\'date\', $this->' . $tip . $nomcamp . ');
		return $oConverter->fromPg();
	}';
    } else {
        $gets .= '
	/**
	 * Recupera l\'atribut ' . $tip . $nomcamp . ' de ' . $clase . '
	 *
	 * @return ' . $tipo_db . ' ' . $tip . $nomcamp . '
	 */
	function get' . $NomCamp . '() {
		if (!isset($this->' . $tip . $nomcamp . ') && !$this->bLoaded) {
			$this->DBCarregar();
		}
		return $this->' . $tip . $nomcamp . ';
	}';
    }

    if (in_array($nomcamp, $aClaus)) {
        $aClaus2[$nomcamp] = $tip . $nomcamp;
        $gets .= '
	/**
	 * estableix el valor de l\'atribut ' . $tip . $nomcamp . ' de ' . $clase . '
	 *
	 * @param ' . $tipo_db . ' ' . $tip . $nomcamp . '
	 */
	function set' . $NomCamp . '($' . $tip . $nomcamp . ') {
		$this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';
    } else {
        if ($tipo === 'date') {
            $gets .= '
	/**
	 * estableix el valor de l\'atribut ' . $tip . $nomcamp . ' de ' . $clase . '
	 * Si ' . $tip . $nomcamp . ' es string, y convert=TRUE se convierte usando el formato web\DateTimeLocal->getForamat().
	 * Si convert es FALSE, ' . $tip . $nomcamp . ' debe ser un string en formato ISO (Y-m-d). Corresponde al pgstyle de la base de datos.
	 * 
	 * @param ' . $tipo_db . '|string ' . $tip . $nomcamp . '=\'' . $tip_val . '\' optional.
     * @param boolean convert=TRUE optional. Si es FALSE, df_ini debe ser un string en formato ISO (Y-m-d).
	 */
	function set' . $NomCamp . '($' . $tip . $nomcamp . '=\'' . $tip_val . '\',$convert=TRUE) {
        if ($convert === TRUE  && !empty($' . $tip . $nomcamp . ')) {
            $oConverter = new core\Converter(\'date\', $' . $tip . $nomcamp . ');
            $this->' . $tip . $nomcamp . ' = $oConverter->toPg();
	    } else {
            $this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	    }
	}';
        } else {
            $gets .= '
	/**
	 * estableix el valor de l\'atribut ' . $tip . $nomcamp . ' de ' . $clase . '
	 *
	 * @param ' . $tipo_db . ' ' . $tip . $nomcamp . '=\'' . $tip_val . '\' optional
	 */
	function set' . $NomCamp . '($' . $tip . $nomcamp . '=\'' . $tip_val . '\') {
		$this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';

        }
        $altres_gets .= '
	/**
	 * Recupera les propietats de l\'atribut ' . $tip . $nomcamp . ' de ' . $clase . '
	 * en una clase del tipus DatosCampo
	 *
	 * @return DatosCampo
	 */
	function getDatos' . $NomCamp . '() {
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new core\\DatosCampo(array(\'nom_tabla\'=>$nom_tabla,\'nom_camp\'=>\'' . $nomcamp . '\'));
		$oDatosCampo->setEtiqueta(_("' . $nomcamp . '"));
		return $oDatosCampo;
	}';
        $altres_gets_set .= "\n\t\t" . '$o' . $clase . 'Set->add($this->getDatos' . $NomCamp . '());';

    }

    if ($tipo === 'date') {
        $add_convert = TRUE;
        $exists .= "\n\t\t" . 'if (array_key_exists(\'' . $nomcamp . '\',$aDades)) $this->set' . $NomCamp . '($aDades[\'' . $nomcamp . '\'],$convert);';
        $ToEmpty .= "\n\t\t" . '$this->set' . $NomCamp . '(\'\');';
    } else {
        $exists .= "\n\t\t" . 'if (array_key_exists(\'' . $nomcamp . '\',$aDades)) $this->set' . $NomCamp . '($aDades[\'' . $nomcamp . '\']);';
        $ToEmpty .= "\n\t\t" . '$this->set' . $NomCamp . '(\'\');';
    }

    if (!in_array($nomcamp, $aClaus)) {
        if ($auto != 1) { // si tiene sequencia no pongo el campo en el update.
            if ($tip === 'b') {
                $err_bool .= "\n\t\t" . 'if ( is_true($aDades[\'' . $nomcamp . '\']) ) { $aDades[\'' . $nomcamp . '\']=\'true\'; } else { $aDades[\'' . $nomcamp . '\']=\'false\'; }';
            }
            $guardar .= "\n\t\t" . '$aDades[\'' . $nomcamp . '\'] = $this->' . $tip . $nomcamp . ';';
            if ($cl > 0) $update .= ",\n";
            $update .= "\t\t\t\t\t" . $nomcamp;
            // para intentar que los = salgan en la misma columna
            $n = strlen($nomcamp);
            for ($s = $n; $s < 25; $s++) {
                $update .= " ";
            }
            $update .= '= :' . $nomcamp;
            $cl++;
        }
    }
    if ($auto != 1) { // si tiene sequencia no pongo el campo en el insert.
        if ($c > 0) $campos .= ",";
        $campos .= $nomcamp;
        if ($c > 0) $valores .= ",";
        $valores .= ':' . $nomcamp;
        $c++;
    }
}
$oHoy = new web\DateTimeLocal();
$hoy = $oHoy->getFromLocal();

$txt = "<?php
namespace $grupo\\model\\entity;
use core;";

if ($add_convert === TRUE) {
    $txt .= "\nuse web;";
}

$txt .= "
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
 * Clase que implementa la entidad $tabla
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 1.0
 * @created $hoy
 */
class $clase extends ClasePropiedades {
	/* ATRIBUTOS ----------------------------------------------------------------- */
";
$txt .= $atributs;
$txt .= "\n\t" . '/* ATRIBUTOS QUE NO SON CAMPOS------------------------------------------------- */';

$txt .= '
	/**
	 * oDbl de ' . $clase . '
	 *
	 * @var object
	 */
	 protected $oDbl;
	/**
	 * NomTabla de ' . $clase . '
	 *
	 * @var string
	 */
	 protected $sNomTabla;';

$i = 0;
$claus_txt = '';
$claus_txt2 = '';
$claus_if = '';
$guardar_if = '';
$where = '';
$claus_isset = '';
$claus_query = "";
$claus_getPrimary = "";
foreach ($aClaus2 as $clau => $nom_clau) {
    //$nom_clau="i".$clau;
    if (!empty($claus_txt)) $claus_txt .= ",";
    $claus_txt .= $nom_clau;
    if ($i > 0) $claus_txt2 .= ",\n\t\t\t\t\t\t\t";
    $claus_txt2 .= "'$clau' => " . '$aDades[\'' . $clau . '\']';
    if ($i > 0) $claus_if .= "\n";
    switch (substr($nom_clau, 0, 1)) {
        case 'i':
            $claus_if .= "\t\t\t\t" . 'if (($nom_id == \'' . $clau . '\') && $val_id !== \'\') $this->' . $nom_clau . ' = (int)$val_id; ';
            break;
        case 's':
            $claus_if .= "\t\t\t\t" . 'if (($nom_id == \'' . $clau . '\') && $val_id !== \'\') $this->' . $nom_clau . ' = (string)$val_id; // evitem SQL injection fent cast a string';
            break;
        case 'b':
            $claus_if .= "\t\t\t\t" . 'if (($nom_id == \'' . $clau . '\') && $val_id !== \'\') $this->' . $nom_clau . ' = (bool)$val_id; // evitem SQL injection fent cast a boolean';
            break;
    }
    // si no es auto
    if (!in_array($clau, $a_auto)) {
        if (!empty($guardar_if)) $guardar_if .= ", ";
        $guardar_if .= '$this->' . $nom_clau;
    }
    if ($i > 0) $where .= " AND ";
    $where .= $clau . '=\'$this->' . $nom_clau . '\'';
    if ($i > 0) $claus_isset .= " && ";
    $claus_isset .= 'isset($this->' . $nom_clau . ')';
    $claus_query .= "\n\t\t\t" . '$' . $nom_clau . ' = $aDades[\'' . $clau . '\'];';
    if (!empty($claus_getPrimary)) $claus_getPrimary .= ",";
    $claus_getPrimary .= '\'' . $clau . '\' => $this->' . $nom_clau;
    $i++;
}
$txt .= '
	/* CONSTRUCTOR -------------------------------------------------------------- */

	/**
	 * Constructor de la classe.
	 * Si només necessita un valor, se li pot passar un integer.
	 * En general se li passa un array amb les claus primàries.
	 *
	 * @param integer|array ' . $claus_txt . '
	 * 						$a_id. Un array con los nombres=>valores de las claves primarias.
	 */';

$sForPrimaryK = 'if (is_array($a_id)) { 
			$this->aPrimary_key = $a_id;
			foreach($a_id as $nom_id=>$val_id) {
' . $claus_if . '
			}';
if (count($aClaus2) > 1) { // per el cas de només una clau.
    $sForPrimaryK .= "\n\t\t}";
} else {
    $sForPrimaryK .= "\n\t\t" . '} else {
			if (isset($a_id) && $a_id !== \'\') {
				$this->' . $claus_txt . ' = (integer) $a_id; 
				$this->aPrimary_key = array(\'' . $claus_txt . '\' => $this->' . $claus_txt . ');
			}
		}';
}

$txt .= "\n\t" . 'function __construct($a_id=\'\') {
		$oDbl = $GLOBALS[\'' . $oDB_txt . '\'];';
$txt .= "\n\t\t" . $sForPrimaryK;

$txt .= "\n\t\t" . '$this->setoDbl($oDbl);
		$this->setNomTabla(\'' . $tabla . '\');
	}';


$txt .= '

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Desa els atributs de l\'objecte a la base de dades.
	 * Si no hi ha el registre, fa el insert, si hi es fa el update.
	 *
	 */
	public function DBGuardar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if ($this->DBCarregar(\'guardar\') === FALSE) { $bInsert=TRUE; } else { $bInsert=FALSE; }
		$aDades=[];';
$txt .= $guardar;
$txt .= '
		array_walk($aDades, \'core\\poner_null\');';
if ($err_bool) {
    $txt .= "\n\t\t//para el caso de los boolean FALSE, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:";
    $txt .= $err_bool;
}
$txt .= "\n\n\t\t" . 'if ($bInsert === FALSE) {
			//UPDATE
			$update="
';
$txt .= $update . '";';
$txt .= '
			if (($oDblSt = $oDbl->prepare("UPDATE $nom_tabla SET $update WHERE ' . $where . '")) === FALSE) {
				$sClauError = \'' . $clase . '.update.prepare\';
				$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = \'' . $clase . '.update.execute\';
					$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}
		} else {
			// INSERT';
if (!empty($guardar_if)) {
    $txt .= "\n\t\t\t" . 'array_unshift($aDades, ' . $guardar_if . ');';
}
$txt .= "\n\t\t\t" . '$campos="(';
$txt .= $campos . ')";' . "\n";
$txt .= "\t\t\t" . '$valores="(';
$txt .= $valores . ')";';
$txt .= '		
			if (($oDblSt = $oDbl->prepare("INSERT INTO $nom_tabla $campos VALUES $valores")) === FALSE) {
				$sClauError = \'' . $clase . '.insertar.prepare\';
				$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			} else {
				try {
					$oDblSt->execute($aDades);
				}
				catch ( \PDOException $e) {
					$err_txt=$e->errorInfo[2];
					$this->setErrorTxt($err_txt);
					$sClauError = \'' . $clase . '.insertar.execute\';
					$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
					return FALSE;
				}
			}';
if ($id_seq || $id_seq2) {
    if (empty($id_seq2)) {
        $id_seq = $id_seq;
        $ccc = 'i' . end($a_auto);
    } else {
        $id_seq = $id_seq2;
        $ccc = end($a_auto);
    }
    $txt .= "\n\t\t\t" . '$this->' . $ccc . ' = $oDbl->lastInsertId(\'' . $id_seq . '\');';
}
$txt .= "\n\t\t" . '}
		$this->setAllAtributes($aDades);
		return TRUE;
	}

	/**
	 * Carrega els camps de la base de dades com atributs de l\'objecte.
	 *
	 */
	public function DBCarregar($que=null) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (' . $claus_isset . ') {
			if (($oDblSt = $oDbl->query("SELECT * FROM $nom_tabla WHERE ' . $where . '")) === FALSE) {
				$sClauError = \'' . $clase . '.carregar\';
				$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
				return FALSE;
			}
			$aDades = $oDblSt->fetch(\\PDO::FETCH_ASSOC);
            // Para evitar posteriores cargas
            $this->bLoaded = TRUE;
			switch ($que) {
				case \'tot\':
					$this->aDades=$aDades;
					break;
				case \'guardar\':
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
	 * Elimina el registre de la base de dades corresponent a l\'objecte.
	 *
	 */
	public function DBEliminar() {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		if (($oDbl->exec("DELETE FROM $nom_tabla WHERE ' . $where . '")) === FALSE) {
			$sClauError = \'' . $clase . '.eliminar\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		return TRUE;
	}
	
	/* OTROS MÉTODOS  ----------------------------------------------------------*/
';

$txt .= '	/* MÉTODOS PRIVADOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDades
	 */';
if ($add_convert === TRUE) {
    $txt .= "\n\t" . 'function setAllAtributes($aDades,$convert=FALSE) {';
} else {
    $txt .= "\n\t" . 'function setAllAtributes($aDades) {';
}
$txt .= "\n\t\t" . 'if (!is_array($aDades)) return;
		if (array_key_exists(\'id_schema\',$aDades)) $this->setId_schema($aDades[\'id_schema\']);';

$txt .= $exists;
$txt .= "\n\t" . '}';

$txt .= '	
	/**
	 * Establece a empty el valor de todos los atributos
	 *
	 */
	function setNullAllAtributes() {
		$aPK = $this->getPrimary_key();
		$this->setId_schema(\'\');';
$txt .= $ToEmpty;
$txt .= "\n\t\t" . '$this->setPrimary_key($aPK);';
$txt .= "\n\t" . '}

	/* MÉTODOS GET y SET --------------------------------------------------------*/

	/**
	 * Recupera todos los atributos de ' . $clase . ' en un array
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
	 * Recupera la clave primaria de ' . $clase . ' en un array
	 *
	 * @return array aPrimary_key
	 */
	function getPrimary_key() {
		if (!isset($this->aPrimary_key )) {
			$this->aPrimary_key = array(' . $claus_getPrimary . ');
		}
		return $this->aPrimary_key;
	}
	/**
	 * Establece la clave primaria de ' . $clase . ' en un array
	 *
	 */
	public function setPrimary_key($a_id=\'\') {
	    ' . $sForPrimaryK . '
	}
	
';

$txt .= $gets;

$txt .= '
	/* MÉTODOS GET y SET D\'ATRIBUTOS QUE NO SON CAMPOS -----------------------------*/

	/**
	 * Retorna una col·lecció d\'objectes del tipus DatosCampo
	 *
	 */
	function getDatosCampos() {
		$o' . $clase . 'Set = new Set();
';
$txt .= $altres_gets_set;
$txt .= '
		return $o' . $clase . 'Set->getTot();
	}


';
$txt .= $altres_gets;
$txt .= '
}
';

/* ESCRIURE LA CLASSSE ------------------------------------------------ */
$filename = ConfigGlobal::DIR . '/apps/' . $grupo . '/model/entity/' . $Qclase . '.php';

if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}

// Write $somecontent to our opened file.
if (fwrite($handle, $txt) === FALSE) {
    echo "Cannot write to file ($filename)";
    die();
}

echo "Success, wrote (somecontent) to file ($filename)";

fclose($handle);

chmod($filename, 0775);  
//chown($filename, 'dani'); No se puede por falta de permisos
chgrp($filename, 'www-data');

/* CONSTRUIR EL GESTOR ------------------------------------------------ */
$gestor = "Gestor" . ucfirst($clase);
$txt2 = "<?php
namespace $grupo\\model\\entity;
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

class $gestor extends ClaseGestor {
	/* ATRIBUTOS ----------------------------------------------------------------- */

	/* CONSTRUCTOR -------------------------------------------------------------- */
";

$txt2 .= '

	/**
	 * Constructor de la classe.
	 *
	 * @return $gestor
	 *
	 */
	function __construct() {
		$oDbl = $GLOBALS[\'' . $oDB_txt . '\'];
		$this->setoDbl($oDbl);
		$this->setNomTabla(\'' . $tabla . '\');
	}


	/* MÉTODOS PÚBLICOS -----------------------------------------------------------*/
';

$txt2 .= '
	/**
	 * retorna l\'array d\'objectes de tipus ' . $clase . '
	 *
	 * @param string sQuery la query a executar.
	 * @return array Una col·lecció d\'objectes de tipus ' . $clase . '
	 */
	function get' . $clase_plural . 'Query($sQuery=\'\') {
		$oDbl = $this->getoDbl();
		$o' . $clase . 'Set = new Set();
		if (($oDbl->query($sQuery)) === FALSE) {
			$sClauError = \'' . $gestor . '.query\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDbl->query($sQuery) as $aDades) {';
$txt2 .= "\n\t\t\t" . '$a_pkey = array(' . $claus_txt2 . ');';
$txt2 .= "\n\t\t\t" . '$o' . $clase . '= new ' . $clase . '($a_pkey);';
$txt2 .= '
			$o' . $clase . '->setAllAtributes($aDades);
			$o' . $clase . 'Set->add($o' . $clase . ');
		}
		return $o' . $clase . 'Set->getTot();
	}
';

$txt2 .= '
	/**
	 * retorna l\'array d\'objectes de tipus ' . $clase . '
	 *
	 * @param array aWhere associatiu amb els valors de les variables amb les quals farem la query
	 * @param array aOperators associatiu amb els valors dels operadors que cal aplicar a cada variable
	 * @return array Una col·lecció d\'objectes de tipus ' . $clase . '
	 */
	function get' . $clase_plural . '($aWhere=[],$aOperators=array()) {
		$oDbl = $this->getoDbl();
		$nom_tabla = $this->getNomTabla();
		$o' . $clase . 'Set = new Set();
		$oCondicion = new Condicion();
		$aCondi = [];';
$txt2 .= '
		foreach ($aWhere as $camp => $val) {
			if ($camp == \'_ordre\') continue;
			$sOperador = isset($aOperators[$camp])? $aOperators[$camp] : \'\';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) $aCondi[]=$a;
			// operadores que no requieren valores
			if ($sOperador == \'BETWEEN\' || $sOperador == \'IS NULL\' || $sOperador == \'IS NOT NULL\' || $sOperador == \'OR\') unset($aWhere[$camp]);
            if ($sOperador == \'IN\' || $sOperador == \'NOT IN\') unset($aWhere[$camp]);
            if ($sOperador == \'TXT\') unset($aWhere[$camp]);
		}';

$txt2 .= "\n\t\t" . '$sCondi = implode(\' AND \',$aCondi);
		if ($sCondi!=\'\') $sCondi = " WHERE ".$sCondi;
		if (isset($GLOBALS[\'oGestorSessioDelegación\'])) {
		   	$sLimit = $GLOBALS[\'oGestorSessioDelegación\']->getLimitPaginador(\'a_actividades\',$sCondi,$aWhere);
		} else {
			$sLimit=\'\';
		}
		if ($sLimit === FALSE) return;
		$sOrdre = \'\';
		if (isset($aWhere[\'_ordre\']) && $aWhere[\'_ordre\']!=\'\') $sOrdre = \' ORDER BY \'.$aWhere[\'_ordre\'];
		if (isset($aWhere[\'_ordre\'])) unset($aWhere[\'_ordre\']);
		$sQry = "SELECT * FROM $nom_tabla ".$sCondi.$sOrdre.$sLimit;
		if (($oDblSt = $oDbl->prepare($sQry)) === FALSE) {
			$sClauError = \'' . $gestor . '.llistar.prepare\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDbl, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		if (($oDblSt->execute($aWhere)) === FALSE) {
			$sClauError = \'' . $gestor . '.llistar.execute\';
			$_SESSION[\'oGestorErrores\']->addErrorAppLastError($oDblSt, $sClauError, __LINE__, __FILE__);
			return FALSE;
		}
		foreach ($oDblSt as $aDades) {';
$txt2 .= "\n\t\t\t" . '$a_pkey = array(' . $claus_txt2 . ');';
$txt2 .= "\n\t\t\t" . '$o' . $clase . '= new ' . $clase . '($a_pkey);';
$txt2 .= '
			$o' . $clase . '->setAllAtributes($aDades);
			$o' . $clase . 'Set->add($o' . $clase . ');
		}
		return $o' . $clase . 'Set->getTot();
	}
';
$txt2 .= '
	/* MÉTODOS PROTECTED --------------------------------------------------------*/

	/* MÉTODOS GET y SET --------------------------------------------------------*/
}
';
/* ESCRIURE LA CLASSSE ------------------------------------------------ */
$filename = ConfigGlobal::DIR . '/apps/' . $grupo . '/model/entity/Gestor' . $Qclase . '.php';


if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}

// Write $somecontent to our opened file.
if (fwrite($handle, $txt2) === FALSE) {
    echo "Cannot write to file ($filename)";
    die();
}

echo "<br>Success, wrote gestor to file ($filename)";

fclose($handle);

chmod($filename, 0775);  
//chown($filename, 'dani'); No se puede por falta de permisos
chgrp($filename, 'www-data');
