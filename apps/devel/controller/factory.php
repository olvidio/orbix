<?php

namespace devel\controller;

use core\ConfigGlobal;
use core\ServerConf;
use RuntimeException;
use web\DateTimeLocal;
use function core\is_true;

/**
 * programa per generar les classes a partir de la taula
 *
 */
/**
 * Para asegurar que inicia la sesión, y poder acceder a los permisos
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************
// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
require_once("apps/devel/controller/func_factory.php");
// FIN de  Cabecera global de URL de controlador ********************************

$Q_db = (string)filter_input(INPUT_POST, 'db');
$Q_tabla = (string)filter_input(INPUT_POST, 'tabla');
$Q_clase = (string)filter_input(INPUT_POST, 'clase');
$Q_clase_plural = (string)filter_input(INPUT_POST, 'clase_plural');
$Q_grupo = (string)filter_input(INPUT_POST, 'grupo');
$Q_aplicacion = (string)filter_input(INPUT_POST, 'aplicacion');

if (empty($Q_tabla)) {
    exit("Ha de dir quina taula");
}
// si la tabla tiene el schema, hay que separalo:
$schema_sql = '';
$tabla = $Q_tabla;
$schema = strtok($tabla, '.');
if ($schema !== $tabla) {
    $tabla = strtok('.');
    $schema_sql = "and n.nspname='$schema' ";
} else {
    $schema = 'public';
}

if (isset($Q_db)) {
    switch ($Q_db) {
        case "tramity":
            $oDbl = $oDBT;
            $oDB_txt = 'oDBT';
            $prefix = '';
            break;
        case "davical":
            $oDbl = $oDBDavical;
            $oDB_txt = 'oDBDavical';
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
        case "sv-e":
            $oDbl = $oDBE;
            $oDB_txt = 'oDBE';
            $prefix = '';
            switch ($schema) {
                case 'publicv':
                case 'publicf':
                    $oDB_txt = 'oDBEP';
                    break;
                case 'restov':
                case 'restof':
                    $oDB_txt = 'oDBER';
                    break;
                case 'H-dlbv':
                case 'H-dlbf':
                    $oDB_txt = 'oDBE';
                    break;
            }
            break;
        default:
            exit("Ha de dir quina base de dades");
    }
} else {
    exit("Ha de dir quina base de dades");
}


$clase = !empty($Q_clase) ? $Q_clase : $tabla;
if (!empty($Q_clase_plural)) {
    $clase_plural = $Q_clase_plural;
} else {
    //plural de la clase
    if (preg_match('/[aeiou]$/', $clase)) {
        $clase_plural = $clase . 's';
    } else {
        $clase_plural = $clase . 'es';
    }
}

$grupo = !empty($Q_grupo) ? $Q_grupo : "actividades";
$aplicacion = !empty($Q_aplicacion) ? $Q_aplicacion : "delegación";

// crear el directorio legacy si no existe
$dir_legacy = ServerConf::DIR . '/apps/' . $grupo . '/legacy';
if (!is_dir($dir_legacy) && !mkdir($dir_legacy, 0777, true) && !is_dir($dir_legacy)) {
    throw new RunTimeException(sprintf('Directory "%s" was not created', $dir_legacy));
}

/* rename file of class to old if exists */
$grupo = !empty($Q_grupo) ? $Q_grupo : "actividades";
$filename = ServerConf::DIR . '/apps/' . $grupo . '/model/entity/' . $Q_clase . '.php';
$filenameOld = ServerConf::DIR . '/apps/' . $grupo . '/legacy/zz' . $Q_clase . 'Old.php';
if (file_exists($filename)) {
    rename($filename, $filenameOld);
    /* rename class if exists */
    $content = file_get_contents($filenameOld);
    $pattern = '/^class\s+' . $Q_clase . '/im';
    $replacement = 'class zzz' . $Q_clase . 'Old';
    $new_content = preg_replace($pattern, $replacement, $content);
    // también el namespace:
    $pattern2 = '/^namespace\s+(.*)/im';
    $replacement2 = "namespace $grupo\\legacy;";
    $new_content2 = preg_replace($pattern2, $replacement2, $new_content);

    if (file_put_contents($filenameOld, $new_content2) === false) {
        echo "No puedo cambiar el nombre de la clase en  ($filenameOld)";
        die();
    }
}
/* rename file of gestor to old if exists */
$gestor = "Gestor" . ucfirst($Q_clase);
$filename = ServerConf::DIR . '/apps/' . $grupo . '/model/entity/Gestor' . $Q_clase . '.php';
$filenameOld = ServerConf::DIR . '/apps/' . $grupo . '/legacy/zzzGestor' . $Q_clase . 'Old.php';
if (file_exists($filename)) {
    rename($filename, $filenameOld);
    /* rename class if exists */
    $content = file_get_contents($filenameOld);
    $pattern = '/^class\s+' . $gestor . '/im';
    $replacement = 'class zzz' . $gestor . 'Old';
    $new_content = preg_replace($pattern, $replacement, $content);
    // también el namespace:
    $pattern2 = '/^namespace\s+(.*)/im';
    $replacement2 = "namespace $grupo\\legacy;";
    $new_content2 = preg_replace($pattern2, $replacement2, $new_content);

    if (file_put_contents($filenameOld, $new_content2) === false) {
        echo "No puedo cambiar el nombre de la clase en  ($filenameOld)";
        die();
    }
}

//busco les claus primaries
$aClaus = primaryKey($oDbl, $Q_tabla);

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


$ATRIBUTOS = '';

$a_use_txt = [];
$c = 0;
$cl = 0;
$id_seq = "";
$id_seq2 = "";
$aClaus2 = [];
$guardar = "";
$update = "";
$campos = "";
$a_add_campos = [];
$valores = "";
$exists = "";
$ToEmpty = "";
$bytea_bind = '';
$bytea_dades = '';
$array_dades = '';
$fechas_dades = '';
$json_dades = '';
$gets = "";
$altres_gets = "";
$altres_gets_set = "";
$query_if = "";
$guardar_array = "";
$guardar_bytea = "";
$guardar_time = "";
$guardar_fechas = "";
$guardar_json = "";
$err_bool = "";
$a_auto = [];
// una primera vuelta para cargar excepciones...
foreach ($oDbl->query($sql) as $row) {
    $nomcamp = $row['field'];
    if ($nomcamp === 'id_schema') {
        continue;
    }
    $tipo = $row['type'];

    switch ($tipo) {
        case '_int8':
        case '_int4':
        case '_int2':
            $a_use_txt['array_pgInteger2php'] = "use function core\array_pgInteger2php";
            $a_use_txt['array_php2pg'] = "use function core\array_php2pg";
            break;
        case 'int8':
        case 'int4':
        case 'int2':
            break;
        case 'float4':
        case 'double':
        case 'numeric':
            break;
        case 'text':
        case 'varchar':
            break;
        case 'date':
        case 'timestamp':
        case 'timestamptz';
            $a_use_txt['DateTimeLocal'] = "use web\DateTimeLocal";
            $a_use_txt['NullDateTimeLocal'] = "use web\NullDateTimeLocal";
            $a_use_txt['ConverterDate'] = "use core\ConverterDate";
            break;
        case 'time':
            $a_use_txt['TimeLocal'] = "use web\TimeLocal";
            $a_use_txt['NullTimeLocal'] = "use web\NullTimeLocal";
            $a_use_txt['ConverterDate'] = "use core\ConverterDate";
            break;
        case 'bool':
            $a_use_txt['is_true'] = "use function core\is_true";
            break;
        case 'json':
        case 'jsonb':
            $a_use_txt['ConverterJson'] = "use core\ConverterJson";
            $a_use_txt['JsonException'] = "use JsonException";
            $a_use_txt['stdClass'] = "use stdClass";
            break;
        case 'bytea':
            break;
    }
}

foreach ($oDbl->query($sql) as $row) {
    $nomcamp = $row['field'];
    if ($nomcamp === 'id_schema') {
        continue;
    }
    $NomCamp = ucwords($nomcamp);
    $tipo = $row['type'];
    $null = (is_true($row['notnull'])) ? 'null' : '';

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
            if (preg_match("/nextval\(\'(.*)\'.*$/", $default, $matches)) {
                $id_seq_con_esquema = $matches[1];
                // quitar el esquema (si existe)
                if (preg_match("/(.*)\.(.*)$/", $id_seq_con_esquema, $matches2)) {
                    $id_seq2 = $matches2[2];
                } else {
                    $id_seq2 = $matches[1];
                }
                $auto = 1;
                $a_auto[] = $nomcamp;
            }
        }
    }
    //echo "{$_POST['ficha']}\n$nomcamp_post\n";

    switch ($tipo) {
        case '_int8':
        case '_int4':
        case '_int2':
            $tipo_db = 'array';
            $tip = 'a_';
            $tip_val = '';
            $array_dades .= "\n\t\t\t";
            $array_dades .= '$aDatos[\'' . $nomcamp . '\'] = array_pgInteger2php($aDatos[\'' . $nomcamp . '\']);';
            break;
        case 'int8':
        case 'int4':
        case 'int2':
            $tipo_db = 'int';
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
        case 'timestamp':
        case 'timestamptz';
            $tipo_db = 'DateTimeLocal';
            $tip = 'd';
            $tip_val = '';
            $fechas_dades .= "\n\t\t\t";
            $fechas_dades .= '$aDatos[\'' . $nomcamp . '\'] = (new ConverterDate(\'' . $tipo . '\', $aDatos[\'' . $nomcamp . '\']))->fromPg();';
            break;
        case 'time':
            $tipo_db = 'TimeLocal';
            $tip = 't';
            $tip_val = '';
            $fechas_dades .= "\n\t\t\t";
            $fechas_dades .= '$aDatos[\'' . $nomcamp . '\'] = (new ConverterDate(\'' . $tipo . '\', $aDatos[\'' . $nomcamp . '\']))->fromPg();';
            break;
        case 'bool':
            $tipo_db = 'bool';
            $tip = 'b';
            $tip_val = 'false';
            break;
        case 'json':
        case 'jsonb':
            $tipo_db = 'array|stdClass';
            $tip = '';
            $tip_val = '';
            $json_dades .= "\n\t\t\t";
            $json_dades .= '$aDatos[\'' . $nomcamp . '\'] = (new ConverterJson($aDatos[\'' . $nomcamp . '\']))->fromPg();';
            break;
        case 'bytea':
            $tipo_db = 'string';
            $tip = 's';
            $tip_val = '';
            $bytea_dades .= "\n\t\t\t";
            $bytea_dades .= '$handle = $aDatos[\'' . $nomcamp . '\'];';
            $bytea_dades .= "\n\t\t";
            $bytea_dades .= 'if ($handle !== null) {';
            $bytea_dades .= "\n\t\t\t";
            $bytea_dades .= '$contents = stream_get_contents($handle);';
            $bytea_dades .= "\n\t\t\t";
            $bytea_dades .= 'fclose($handle);';
            $bytea_dades .= "\n\t\t\t";
            $bytea_dades .= '$' . $nomcamp . ' = $contents;';
            $bytea_dades .= "\n\t\t\t";
            $bytea_dades .= '$aDatos[\'' . $nomcamp . '\'] = $' . $nomcamp . ';';
            $bytea_dades .= "\n\t\t\t";
            $bytea_dades .= "}";

            $bytea_bind .= "\n\t\t";
            $bytea_bind .= '$' . $tip . $nomcamp . " = '';";
            $bytea_bind .= "\n\t\t";
            $bytea_bind .= '$stmt->bindColumn(\'' . $nomcamp . '\', $' . $tip . $nomcamp . ', PDO::PARAM_STR);';
            $bytea_bind .= "\n\t\t";
            $bytea_bind .= '$aDatos = $stmt->fetch(PDO::FETCH_ASSOC);';
            $bytea_bind .= "\n\t\t";
            $bytea_bind .= 'if ($aDatos !== false) {';
            $bytea_bind .= "\n\t\t\t";
            $bytea_bind .= '$aDatos[\'' . $nomcamp . '\'] = hex2bin($' . $tip . $nomcamp . ' ?? \'\');';
            $bytea_bind .= "\n\t\t";
            $bytea_bind .= '}';
            break;
    }
    if (empty($null)) {
        $tipo_db_txt = $tipo_db . "|null";
        $tip_txt = "?" . $tipo_db;
        $val_default = ' = null';
    } else {
        $tipo_db_txt = $tipo_db;
        $tip_txt = $tipo_db;
        $val_default = '';
    }
    $ATRIBUTOS .= '
	/**
	 * ' . $NomCamp . ' de ' . $clase . '
	 *
	 * @var ' . $tipo_db_txt . '
	 */
	 private ' . $tipo_db_txt . ' $' . $tip . $nomcamp . $val_default . ';';

    switch ($tipo) {
        case 'bool':
            $metodo_get = 'is' . $NomCamp . '()';
            $gets .= '
	/**
	 *
	 * @return ' . $tipo_db_txt . ' $' . $tip . $nomcamp;
            $gets .= "\n\t" . ' */
	public function is' . $NomCamp . '(): ' . $tip_txt . '
	{
		return $this->' . $tip . $nomcamp . ';
	}';
            break;
        case '_int8':
        case '_int4':
        case '_int2':
            $metodo_get = 'get' . $NomCamp . '()';
            $gets .= '
	/**
	 *
	 * @return ' . $tipo_db_txt . ' $' . $tip . $nomcamp;
            $gets .= "\n\t" . ' */
	public function get' . $NomCamp . '(): ' . $tipo_db_txt . '
	{
        return $this->' . $tip . $nomcamp . ';
	}';
            break;
        case 'json':
        case 'jsonb':
            $metodo_get = 'get' . $NomCamp . '()';
            $gets .= '
	/**
	 *
	 * @return array|stdClass|null $' . $tip . $nomcamp . '
	 */
	public function get' . $NomCamp . '(): array|stdClass|null
	{
		return $this->' . $tip . $nomcamp . ';
	}';
            break;
        case 'date':
        case 'timestamp':
        case 'timestamptz';
            $metodo_get = 'get' . $NomCamp . '()';
            $gets .= '
	/**
	 *
	 * @return DateTimeLocal|NullDateTimeLocal|null' . ' $' . $tip . $nomcamp;
            $gets .= "\n\t" . ' */
	public function get' . $NomCamp . '(): DateTimeLocal|NullDateTimeLocal|null
	{
        return $this->' . $tip . $nomcamp . '?? new NullDateTimeLocal;
	}';
            break;
        case 'time':
            $metodo_get = 'get' . $NomCamp . '()';
            $gets .= '
	/**
	 *
	 * @return TimeLocal|NullTimeLocal|null' . ' $' . $tip . $nomcamp;
            $gets .= "\n\t" . ' */
	public function get' . $NomCamp . '(): TimeLocal|NullTimeLocal|null
	{
        return $this->' . $tip . $nomcamp . '?? new NullTimeLocal;
	}';
            break;
        default:
            $metodo_get = 'get' . $NomCamp . '()';
            $gets .= '
	/**
	 *
	 * @return ' . $tipo_db_txt . ' $' . $tip . $nomcamp;
            $gets .= "\n\t" . ' */
	public function get' . $NomCamp . '(): ' . $tip_txt . '
	{
		return $this->' . $tip . $nomcamp . ';
	}';
    }

    if (in_array($nomcamp, $aClaus)) {
        $a_add_campos[$nomcamp] = '$aDatos[\'' . $nomcamp . '\'] = $' . $Q_clase . '->' . $metodo_get . ';';
        $aClaus2[$nomcamp] = ['tip_nomcamp' => $tip . $nomcamp, 'tip_txt' => $tip_txt];
        $gets .= '
	/**
	 *
	 * @param ' . $tipo_db_txt . ' $' . $tip . $nomcamp . '
	 */
	public function set' . $NomCamp . '(' . $tip_txt . ' $' . $tip . $nomcamp . '): void
	{
		$this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';
    } else {
        switch ($tipo) {
            case '_int8':
            case '_int4':
            case '_int2':
                $gets .= '
	/**
	 * 
	 * @param array|null $' . $tip . $nomcamp . '
	 */
	public function set' . $NomCamp . '(array $' . $tip . $nomcamp . '= null): void
	{
        $this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';
                break;
            case 'json':
            case 'jsonb':
                $gets .= '
	/**
	 * 
	 * @param stdClass|array|null $' . $tip . $nomcamp . '
	 */
	public function set' . $NomCamp . '(stdClass|array|null $' . $tip . $nomcamp . ' = null): void
	{
        $this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';
                break;
            case 'date':
            case 'timestamp':
            case 'timestamptz';
                $gets .= '
	/**
	 * 
	 * @param DateTimeLocal|null $' . $tip . $nomcamp . '
	 */
	public function set' . $NomCamp . '(DateTimeLocal|null $' . $tip . $nomcamp . ' = null): void
	{
        $this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';
                break;
            case 'time':
                $gets .= '
	/**
	 * 
	 * @param TimeLocal|null $' . $tip . $nomcamp . '
	 */
	public function set' . $NomCamp . '(TimeLocal|null $' . $tip . $nomcamp . ' = null): void
	{
        $this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';
                break;
            default:
                $gets .= '
	/**
	 *
	 * @param ' . $tipo_db_txt . ' $' . $tip . $nomcamp . '
	 */
	public function set' . $NomCamp . '(' . $tip_txt . ' $' . $tip . $nomcamp . $val_default . '): void
	{
		$this->' . $tip . $nomcamp . ' = $' . $tip . $nomcamp . ';
	}';

        }

        $altres_gets .= '
	/**
	 *
	 * @return DatosCampo
	 */
	public function getDatos' . $NomCamp . '(): DatosCampo
	{
		$nom_tabla = $this->getNomTabla();
		$oDatosCampo = new DatosCampo(array(\'nom_tabla\'=>$nom_tabla,\'nom_camp\'=>\'' . $nomcamp . '\'));
		$oDatosCampo->setEtiqueta(_("' . $nomcamp . '"));
		return $oDatosCampo;
	}';
        $altres_gets_set .= "\n\t\t" . '$o' . $clase . 'Set->add($this->getDatos' . $NomCamp . '());';

    }

    switch ($tipo) {
        case 'bool':
            $exists .= "\n\t\t" . 'if (array_key_exists(\'' . $nomcamp . '\',$aDatos))';
            $exists .= "\n\t\t{";
            $exists .= "\n\t\t\t" . '$this->set' . $NomCamp . '(is_true($aDatos[\'' . $nomcamp . '\']));';
            $exists .= "\n\t\t}";
            $ToEmpty .= "\n\t\t" . '$this->set' . $NomCamp . '(\'\');';
            break;
        case '_int8':
        case '_int4':
        case '_int2':
        case 'json':
        case 'jsonb':
            $exists .= "\n\t\t" . 'if (array_key_exists(\'' . $nomcamp . '\',$aDatos))';
            $exists .= "\n\t\t{";
            $exists .= "\n\t\t\t" . '$this->set' . $NomCamp . '($aDatos[\'' . $nomcamp . '\']);';
            $exists .= "\n\t\t}";
            $ToEmpty .= "\n\t\t" . '$this->set' . $NomCamp . '(\'\');';
            break;
        case 'date':
        case 'timestamp':
        case 'timestamptz';
        case 'time';
            $exists .= "\n\t\t" . 'if (array_key_exists(\'' . $nomcamp . '\',$aDatos))';
            $exists .= "\n\t\t{";
            $exists .= "\n\t\t\t" . '$this->set' . $NomCamp . '($aDatos[\'' . $nomcamp . '\']);';
            $exists .= "\n\t\t}";
            $ToEmpty .= "\n\t\t" . '$this->set' . $NomCamp . '(\'\');';
            break;
        default:
            $exists .= "\n\t\t" . 'if (array_key_exists(\'' . $nomcamp . '\',$aDatos))';
            $exists .= "\n\t\t{";
            $exists .= "\n\t\t\t" . '$this->set' . $NomCamp . '($aDatos[\'' . $nomcamp . '\']);';
            $exists .= "\n\t\t}";
            $ToEmpty .= "\n\t\t" . '$this->set' . $NomCamp . '(\'\');';
    }

    if (!in_array($nomcamp, $aClaus)) {
        if ($auto != 1) { // si tiene secuencia no pongo el campo en el update.
            if ($tip === 'b') {
                $err_bool .= "\n\t\t" . 'if ( is_true($aDatos[\'' . $nomcamp . '\']) ) { $aDatos[\'' . $nomcamp . '\']=\'true\'; } else { $aDatos[\'' . $nomcamp . '\']=\'false\'; }';
            }
            if ($tipo_db === 'array') {
                $guardar_array .= "\n\t\t" . '$aDatos[\'' . $nomcamp . '\'] = array_php2pg($' . $Q_clase . '->' . $metodo_get . ');';
            }
            if ($tipo === 'bytea') {
                $guardar_bytea .= "\n\t\t" . '$aDatos[\'' . $nomcamp . '\'] = bin2hex($' . $Q_clase . '->' . $metodo_get . ');';
            }
            if ($tipo_db === 'TimeLocal') {
                $guardar_time .= "\n\t\t" . '$aDatos[\'' . $nomcamp . '\'] = (new ConverterDate(\'' . $tipo . '\', $' . $Q_clase . '->' . $metodo_get . '))->toPg();';
            }
            if ($tipo_db === 'DateTimeLocal') {
                $guardar_fechas .= "\n\t\t" . '$aDatos[\'' . $nomcamp . '\'] = (new ConverterDate(\'' . $tipo . '\', $' . $Q_clase . '->' . $metodo_get . '))->toPg();';
            }

            if ($tipo === 'jsonb' || $tipo === 'json') {
                $guardar_json .= "\n\t\t" . '$aDatos[\'' . $nomcamp . '\'] = (new ConverterJson($' . $Q_clase . '->' . $metodo_get . '))->toPg();';
            }

            if ($tipo_db !== 'array' && $tipo !== 'bytea' && $tipo_db !== 'DateTimeLocal' && $tipo !== 'jsonb' && $tipo !== 'json') {
                $guardar .= "\n\t\t" . '$aDatos[\'' . $nomcamp . '\'] = $' . $Q_clase . '->' . $metodo_get . ';';
            }

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
    if ($c > 0) $campos .= ",";
    $campos .= $nomcamp;
    if ($c > 0) $valores .= ",";
    $valores .= ':' . $nomcamp;
    $c++;
}
$oHoy = new DateTimeLocal();
$hoy = $oHoy->getFromLocal();

//------------------------------------ CLASE ENTIDAD -----------------------------------------------
$txt_entidad = "<?php

namespace src\\$grupo\\domain\\entity;";
if (!empty($a_use_txt['is_true'])) {
    $txt_entidad .= "\n\t" . 'use function core\is_true;';
}
if (!empty($a_use_txt['DateTimeLocal'])) {
    $txt_entidad .= "\n\t" . 'use web\DateTimeLocal;';
}
if (!empty($a_use_txt['NullDateTimeLocal'])) {
    $txt_entidad .= "\n\t" . 'use web\NullDateTimeLocal;';
}
if (!empty($a_use_txt['TimeLocal'])) {
    $txt_entidad .= "\n\t" . 'use web\TimeLocal;';
}
if (!empty($a_use_txt['NullTimeLocal'])) {
    $txt_entidad .= "\n\t" . 'use web\NullTimeLocal;';
}
if (!empty($a_use_txt['stdClass'])) {
    $txt_entidad .= "\n\t" . 'use stdClass;';
}

$txt_entidad .= "
/**
 * Clase que implementa la entidad $tabla
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created $hoy
 */
class $clase {

	/* ATRIBUTOS ----------------------------------------------------------------- */
";
$txt_entidad .= $ATRIBUTOS;
$txt_entidad .= "\n";
$txt_entidad .= "\n\t";
$txt_entidad .= '/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos';
$txt_entidad .= "\n\t" . ' * @return ' . $Q_clase;
$txt_entidad .= "\n\t" . ' */';
$txt_entidad .= "\n\t" . 'public function setAllAttributes(array $aDatos): ' . $Q_clase . "\n\t" . '{';

$txt_entidad .= $exists;
$txt_entidad .= "\n\t\t" . 'return $this;';
$txt_entidad .= "\n\t" . '}';
$txt_entidad .= $gets;
$txt_entidad .= "\n" . '}';

// ESCRIBIR LA CLASE ---------  ENTIDAD

// crear el directorio domain/entity si no existe
$dir_entity = ServerConf::DIR . '/src/' . $grupo . '/domain/entity';
if (!is_dir($dir_entity)) {
    if (!mkdir($dir_entity, 0777, TRUE) && !is_dir($dir_entity)) {
        throw new RuntimeException(sprintf('Directory "%s" was not created', $dir_entity));
    }
}
$filename = ConfigGlobal::DIR . '/src/' . $grupo . '/domain/entity/' . $Q_clase . '.php';
if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}
// Write $somecontent to our opened file.
if (fwrite($handle, $txt_entidad) === false) {
    echo "Cannot write to file ($filename)";
    die();
}
echo "<br>Success, wrote entitie to file ($filename)";
fclose($handle);

// ---------------------- REPOSITORIO ------------------------------------------------

$pg_clase = "Pg" . $Q_clase . "Repository";
$clase_interface = $Q_clase . "RepositoryInterface";
$clase_repository = $Q_clase . "Repository";

$where = '';
$claus_getPrimary = '';
$getClau = '';
$claus_txt = '';
$claus_txt2 = '';
if (count($aClaus2) === 1) {
    $a_nom_clau = current($aClaus2);
    $nom_clau = $a_nom_clau['tip_nomcamp'];
    $clau_tip_txt = $a_nom_clau['tip_txt'];
    $clau = key($aClaus2);
    // si es integer quito las comillas del where
    if ($nom_clau[0] === 'i') {
        $where .= $clau . ' = $' . $clau;
    } else {
        $where .= $clau . ' = \'$' . $clau . '\'';
    }

    $claus_getPrimary .= '\'' . $clau . '\' => $this->' . $nom_clau;

    $getClau .= '$' . $clau . ' = $' . $Q_clase . '->get' . ucfirst($clau) . '();';

} else {
    // si n'hi ha més d'una
    $i = 0;
    foreach ($aClaus2 as $clau => $nom_clau) {
        //$nom_clau="i".$clau;
        $i++;
        if ($i > 0) {
            $where .= " AND ";
        }
        // si es integer quito las comillas del where
        if ($nom_clau[0] === 'i') {
            $where .= $clau . ' = $' . $clau;
        } else {
            $where .= $clau . ' = \'$' . $clau . '\'';
        }

        if (!empty($claus_txt)) $claus_txt .= ",";
        $claus_txt .= $nom_clau;
        if ($i > 0) $claus_txt2 .= ",\n\t\t\t\t\t\t\t";
        $claus_txt2 .= "'$clau' => " . '$aDatos[\'' . $clau . '\']';
    }
}

$use_txt = '';
foreach ($a_use_txt as $use) {
    $use_txt .= "\n" . $use . ";";
}
$txt_repository = "<?php

namespace src\\$grupo\\application\\repositories;

use PDO;
use src\\$grupo\\domain\\entity\\$Q_clase;
use src\\$grupo\\domain\\contracts\\$clase_interface;
use src\\$grupo\\infrastructure\\repositories\\$pg_clase;
";
$txt_repository .= "\n" . $use_txt;
$txt_repository .= "
/**
 *
 * Clase para gestionar la lista de objetos tipo $Q_clase
 * 
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created $hoy
 */
class $clase_repository implements $clase_interface
{

    /**$
     * @var $clase_interface
     */
    private $clase_interface \$repository;

    public function __construct()
    {
        \$this->repository = new $pg_clase();
    }
";

$txt_interface = "<?php

namespace src\\$grupo\\domain\\contracts;

use PDO;
use src\\$grupo\\domain\\entity\\$Q_clase;
";
$txt_interface .= "\n" . $use_txt;
$txt_interface .= "
/**
 * Interfaz de la clase $Q_clase y su Repositorio
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created $hoy
 */
interface $clase_interface
{
";

$txt_pgRepositorio = "<?php

namespace src\\$grupo\\infrastructure\\repositories;

use core\\ClaseRepository;
use core\\Condicion;
use core\\Set;
use PDO;
use PDOException;
";

$txt_pgRepositorio .= "
use src\\$grupo\\domain\\entity\\$Q_clase;
use src\\$grupo\\domain\\contracts\\$clase_interface;
use src\\shared\\traits\\HandlesPdoErrors;
";

$use_txt = '';
foreach ($a_use_txt as $use) {
    $use_txt .= "\n" . $use . ";";
}
$txt_pgRepositorio .= "\n" . $use_txt;

$txt_pgRepositorio .= "
/**
 * Clase que adapta la tabla $tabla a la interfaz del repositorio
 *
 * @package $aplicacion
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created $hoy
 */
class $pg_clase extends ClaseRepository implements $clase_interface
{";

$txt_pgRepositorio .= '
    use HandlesPdoErrors;
    public function __construct()
    {
        $oDbl = $GLOBALS[\'' . $oDB_txt . '\'];
        $this->setoDbl($oDbl); ';
if ($Q_db === 'sv-e' || $Q_db === 'comun') {
    $oDB_txt2 = $oDB_txt.'_Select';
    $txt_pgRepositorio .= '
        $oDbl_Select = $GLOBALS[\'' . $oDB_txt2 . '\'];
        $this->setoDbl_select($oDbl_Select); ';
}
$txt_pgRepositorio .= '
        $this->setNomTabla(\'' . $tabla . '\');
    }
';

$txt_repository .= "\n";
$txt_repository .= '/* -------------------- GESTOR BASE ---------------------------------------- */';
$txt_repository .= "\n";
$txt_repository .= '
	/**
	 * devuelve una colección (array) de objetos de tipo ' . $Q_clase . '
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ' . $Q_clase;
if (!empty($a_use_txt['JsonException'])) {
    $txt_repository .= "\n\t" . ' * @throws JsonException';
}
$txt_repository .= "\n\t" . '
	 */
	public function get' . $clase_plural . '(array $aWhere=[], array $aOperators=[]): array|false
	{
	    return $this->repository->get' . $clase_plural . '($aWhere, $aOperators);
	}
	';

$txt_interface .= "\n";
$txt_interface .= '/* -------------------- GESTOR BASE ---------------------------------------- */';
$txt_interface .= "\n";
$txt_interface .= '
	/**
	 * devuelve una colección (array) de objetos de tipo ' . $Q_clase . '
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ' . $Q_clase;
if (!empty($a_use_txt['JsonException'])) {
    $txt_interface .= "\n\t" . ' * @throws JsonException';
}
$txt_interface .= "\n\t" . '
	 */
	public function get' . $clase_plural . '(array $aWhere=[], array $aOperators=[]): array|false;
	';

$txt_pgRepositorio .= "\n";
$txt_pgRepositorio .= '/* -------------------- GESTOR BASE ---------------------------------------- */';
$txt_pgRepositorio .= "\n";

$txt_pgRepositorio .= '
	/**
	 * devuelve una colección (array) de objetos de tipo ' . $Q_clase . '
	 *
	 * @param array $aWhere asociativo con los valores para cada campo de la BD.
	 * @param array $aOperators asociativo con los operadores que hay que aplicar a cada campo
	 * @return array|false Una colección de objetos de tipo ' . $Q_clase;
if (!empty($a_use_txt['JsonException'])) {
    $txt_pgRepositorio .= "\n\t" . ' * @throws JsonException';
}
$txt_pgRepositorio .= "\n\t" . '
	 */
	public function get' . $clase_plural . '(array $aWhere=[], array $aOperators=[]): array|false
	{';
if ($Q_db === 'sv-e' || $Q_db === 'comun') {
    $txt_pgRepositorio .= '
        $oDbl = $this->getoDbl_Select();';
} else {
    $txt_pgRepositorio .= '
        $oDbl = $this->getoDbl();';
}
$txt_pgRepositorio .= '
		$nom_tabla = $this->getNomTabla();
		$' . $Q_clase . 'Set = new Set();
		$oCondicion = new Condicion();
		$aCondicion = [];';
$txt_pgRepositorio .= '
		foreach ($aWhere as $camp => $val) {
			if ($camp === \'_ordre\') { continue; }
			if ($camp === \'_limit\') { continue; }
			$sOperador = $aOperators[$camp] ?? \'\';
			if ($a = $oCondicion->getCondicion($camp,$sOperador,$val)) { $aCondicion[]=$a; }
			// operadores que no requieren valores
			if ($sOperador === \'BETWEEN\' || $sOperador === \'IS NULL\' || $sOperador === \'IS NOT NULL\' || $sOperador === \'OR\') { unset($aWhere[$camp]); }
            if ($sOperador === \'IN\' || $sOperador === \'NOT IN\') { unset($aWhere[$camp]); }
            if ($sOperador === \'TXT\') { unset($aWhere[$camp]); }
		}';

$txt_pgRepositorio .= "\n\t\t" . '$sCondicion = implode(\' AND \',$aCondicion);
		if ($sCondicion !==\'\') { $sCondicion = " WHERE ".$sCondicion; }
		$sOrdre = \'\';
        $sLimit = \'\';
		if (isset($aWhere[\'_ordre\']) && $aWhere[\'_ordre\'] !== \'\') { $sOrdre = \' ORDER BY \'.$aWhere[\'_ordre\']; }
		if (isset($aWhere[\'_ordre\'])) { unset($aWhere[\'_ordre\']); }
		if (isset($aWhere[\'_limit\']) && $aWhere[\'_limit\'] !== \'\') { $sLimit = \' LIMIT \'.$aWhere[\'_limit\']; }
		if (isset($aWhere[\'_limit\'])) { unset($aWhere[\'_limit\']); }
		$sQry = "SELECT * FROM $nom_tabla ".$sCondicion.$sOrdre.$sLimit;
		$stmt = $this->prepareAndExecute( $oDbl, $sQry, $aWhere,__METHOD__, __FILE__, __LINE__);
		
		$filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filas as $aDatos) {';

if (!empty($bytea_dades)) {
    $txt_pgRepositorio .= "\n\t\t\t// para los bytea: (resources)";
    $txt_pgRepositorio .= $bytea_dades;
}
if (!empty($array_dades)) {
    $txt_pgRepositorio .= "\n\t\t\t// para los array del postgres";
    $txt_pgRepositorio .= $array_dades;
}
if (!empty($fechas_dades)) {
    $txt_pgRepositorio .= "\n\t\t\t// para las fechas del postgres (texto iso)";
    $txt_pgRepositorio .= $fechas_dades;
}
if (!empty($json_dades)) {
    $txt_pgRepositorio .= "\n\t\t\t// para los json";
    $txt_pgRepositorio .= $json_dades;
}

$txt_pgRepositorio .= '
            $' . $Q_clase . ' = new ' . $Q_clase . '();
            $' . $Q_clase . '->setAllAttributes($aDatos);
			$' . $Q_clase . 'Set->add($' . $Q_clase . ');
		}
		return $' . $Q_clase . 'Set->getTot();
	}
';


$txt_repository .= "\n";
$txt_repository .= '/* -------------------- ENTIDAD --------------------------------------------- */';
$txt_repository .= "\n";
$txt_repository .= "\n\t";
$txt_repository .= 'public function Eliminar(' . $Q_clase . ' $' . $Q_clase . '): bool
    {
        return $this->repository->Eliminar($' . $Q_clase . ');
    }';

$txt_interface .= "\n";
$txt_interface .= '/* -------------------- ENTIDAD --------------------------------------------- */';
$txt_interface .= "\n";
$txt_interface .= "\n\t";
$txt_interface .= 'public function Eliminar(' . $Q_clase . ' $' . $Q_clase . '): bool;';

$txt_pgRepositorio .= "\n";
$txt_pgRepositorio .= '/* -------------------- ENTIDAD --------------------------------------------- */';
$txt_pgRepositorio .= "\n";

$txt_pgRepositorio .= "\n\t";
$txt_pgRepositorio .= 'public function Eliminar(' . $Q_clase . ' $' . $Q_clase . '): bool
    {
        ' . $getClau . '
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "DELETE FROM $nom_tabla WHERE ' . $where . '";
        return $this->pdoExec( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
    }
';

$txt_repository .= "\n";
$txt_repository .= "\n\t";
$txt_repository .= 'public function Guardar(' . $Q_clase . ' $' . $Q_clase . '): bool
    {
        return $this->repository->Guardar($' . $Q_clase . ');
    }';

$txt_repository .= "\n";
$txt_repository .= "\n\t";
$txt_repository .= 'public function getErrorTxt(): string
    {
        return $this->repository->getErrorTxt();
    }';

$txt_repository .= "\n";
$txt_repository .= "\n\t";
$txt_repository .= 'public function getoDbl(): PDO
    {
        return $this->repository->getoDbl();
    }';

$txt_repository .= "\n";
$txt_repository .= "\n\t";
$txt_repository .= 'public function setoDbl(PDO $oDbl): void
    {
        $this->repository->setoDbl($oDbl);
    }';

$txt_repository .= "\n";
$txt_repository .= "\n\t";
$txt_repository .= 'public function getNomTabla(): string
    {
        return $this->repository->getNomTabla();
    }';

$txt_interface .= "\n";
$txt_interface .= "\n\t";
$txt_interface .= 'public function Guardar(' . $Q_clase . ' $' . $Q_clase . '): bool;';

$txt_interface .= "\n";
$txt_interface .= "\n\t";
$txt_interface .= 'public function getErrorTxt(): string;';

$txt_interface .= "\n";
$txt_interface .= "\n\t";
$txt_interface .= 'public function getoDbl(): PDO;';

$txt_interface .= "\n";
$txt_interface .= "\n\t";
$txt_interface .= 'public function setoDbl(PDO $oDbl): void;';

$txt_interface .= "\n";
$txt_interface .= "\n\t";
$txt_interface .= 'public function getNomTabla(): string;';

$txt_pgRepositorio .= "\n\t";
$txt_pgRepositorio .= '
	/**
	 * Si no existe el registro, hace un insert, si existe, se hace el update.';
if (!empty($a_use_txt['JsonException'])) {
    $txt_pgRepositorio .= "\n\t" . ' * @throws JsonException';
}
$txt_pgRepositorio .= "\n\t";
$txt_pgRepositorio .= '
	 */
	public function Guardar(' . $Q_clase . ' $' . $Q_clase . '): bool
    {
        ' . $getClau . '
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $bInsert = $this->isNew($' . $clau . ');

		$aDatos = [];';

$txt_pgRepositorio .= $guardar;
if ($guardar_array) {
    $txt_pgRepositorio .= "\n\t\t// para los array";
    $txt_pgRepositorio .= $guardar_array;
}
if ($guardar_bytea) {
    $txt_pgRepositorio .= "\n\t\t// para los bytea";
    $txt_pgRepositorio .= $guardar_bytea;
}
if ($guardar_time) {
    $txt_pgRepositorio .= "\n\t\t// para las horas";
    $txt_pgRepositorio .= $guardar_time;
}
if ($guardar_fechas) {
    $txt_pgRepositorio .= "\n\t\t// para las fechas";
    $txt_pgRepositorio .= $guardar_fechas;
}
if ($guardar_json) {
    $txt_pgRepositorio .= "\n\t\t// para los json";
    $txt_pgRepositorio .= $guardar_json;
}
$txt_pgRepositorio .= '
		array_walk($aDatos, \'core\\poner_null\');';
if ($err_bool) {
    $txt_pgRepositorio .= "\n\t\t//para el caso de los boolean false, el pdo(+postgresql) pone string '' en vez de 0. Lo arreglo:";
    $txt_pgRepositorio .= $err_bool;
}
$txt_pgRepositorio .= "\n\n\t\t" . 'if ($bInsert === false) {
			//UPDATE
			$update="
';
$txt_pgRepositorio .= $update . '";';
$txt_pgRepositorio .= '
			$sql = "UPDATE $nom_tabla SET $update WHERE ' . $where . '";
            stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
		} else {
			// INSERT';
foreach ($a_add_campos as $add_campo) {
    $txt_pgRepositorio .= "\n\t\t\t" . $add_campo;
}
$txt_pgRepositorio .= "\n\t\t\t" . '$campos="(';
$txt_pgRepositorio .= $campos . ')";' . "\n";
$txt_pgRepositorio .= "\t\t\t" . '$valores="(';
$txt_pgRepositorio .= $valores . ')";';
$txt_pgRepositorio .= '
            $sql = "INSERT INTO $nom_tabla $campos VALUES $valores";
            stmt = $this->pdoPrepare( $oDbl, $sql, __METHOD__, __FILE__, __LINE__);
            }';
$txt_pgRepositorio .= "\n\t\t" . '}
        return $this->PdoExecute($stmt, $aDatos, __METHOD__, __FILE__, __LINE__);
	}';

$txt_pgRepositorio .= "\n\t";
$txt_pgRepositorio .= '
    private function isNew(' . $clau_tip_txt . ' $' . $clau . '): bool
    {
        $oDbl = $this->getoDbl();
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE ' . $where . '";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        if (!$stmt->rowCount()) { 
            return TRUE;
        }
        return false;
    }';

if ($nom_clau[0] === 'i') {
    $tip_txt = 'int';
} else {
    $tip_txt = 'string';
}
$txt_repository .= "\n\t";
$txt_repository .= '
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param ' . $clau_tip_txt . ' $' . $clau . '
     * @return array|bool';
if (!empty($a_use_txt['JsonException'])) {
    $txt_repository .= "\n\t" . ' * @throws JsonException';
}
$txt_repository .= "\n\t" . '
     */
    public function datosById(' . $clau_tip_txt . ' $' . $clau . '): array|bool
    {
        return $this->repository->datosById($' . $clau . ');
    }';

$txt_interface .= "\n\t";
$txt_interface .= '
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param ' . $clau_tip_txt . ' $' . $clau . '
     * @return array|bool';
if (!empty($a_use_txt['JsonException'])) {
    $txt_interface .= "\n\t" . ' * @throws JsonException';
}
$txt_interface .= "\n\t" . '
     */
    public function datosById(' . $clau_tip_txt . ' $' . $clau . '): array|bool;';

$txt_pgRepositorio .= "\n\t";
$txt_pgRepositorio .= '
    /**
     * Devuelve los campos de la base de datos en un array asociativo.
     * Devuelve false si no existe la fila en la base de datos
     * 
     * @param ' . $clau_tip_txt . ' $' . $clau . '
     * @return array|bool';
if (!empty($a_use_txt['JsonException'])) {
    $txt_pgRepositorio .= "\n\t" . ' * @throws JsonException';
}
$txt_pgRepositorio .= "\n\t" . '
     */
    public function datosById(' . $clau_tip_txt . ' $' . $clau . '): array|bool
    {';
if ($Q_db === 'sv-e' || $Q_db === 'comun') {
    $txt_pgRepositorio .= '
        $oDbl = $this->getoDbl_Select();';
} else {
    $txt_pgRepositorio .= '
        $oDbl = $this->getoDbl();';
}
$txt_pgRepositorio .= '
        $nom_tabla = $this->getNomTabla();
        $sql = "SELECT * FROM $nom_tabla WHERE ' . $where . '";
        $stmt = $this->PdoQuery($oDbl, $sql, __METHOD__, __FILE__, __LINE__);
        ';

if (!empty($bytea_bind)) {
    $txt_pgRepositorio .= "\n\t\t" . '// para los bytea, sobre escribo los valores:';
    $txt_pgRepositorio .= $bytea_bind;
} else {
    $txt_pgRepositorio .= "\n\t\t" . '$aDatos = $stmt->fetch(PDO::FETCH_ASSOC);';
}

if (!empty($array_dades)) {
    $txt_pgRepositorio .= "\n\t\t// para los array del postgres";
    $txt_pgRepositorio .= "\n\t\t" . 'if ($aDatos !== false) {';
    $txt_pgRepositorio .= $array_dades;
    $txt_pgRepositorio .= "\n\t\t}";
}

if (!empty($fechas_dades)) {
    $txt_pgRepositorio .= "\n\t\t// para las fechas del postgres (texto iso)";
    $txt_pgRepositorio .= "\n\t\t" . 'if ($aDatos !== false) {';
    $txt_pgRepositorio .= $fechas_dades;
    $txt_pgRepositorio .= "\n\t\t}";
}

if (!empty($json_dades)) {
    $txt_pgRepositorio .= "\n\t\t// para los json";
    $txt_pgRepositorio .= "\n\t\t" . 'if ($aDatos !== false) {';
    $txt_pgRepositorio .= $json_dades;
    $txt_pgRepositorio .= "\n\t\t}";
}


$txt_pgRepositorio .= '
        return $aDatos;
    }
    ';

$txt_repository .= "\n\t";
$txt_repository .= '
    /**
     * Busca la clase con ' . $clau . ' en el repositorio.';
if (!empty($a_use_txt['JsonException'])) {
    $txt_repository .= "\n\t" . ' * @throws JsonException';
}
$txt_repository .= "\n\t" . '
     */
    public function findById(' . $clau_tip_txt . ' $' . $clau . '): ?' . $Q_clase . '
    {
        return $this->repository->findById($' . $clau . ');
    }';

$txt_interface .= "\n\t";
$txt_interface .= '
    /**
     * Busca la clase con ' . $clau . ' en el repositorio.';
if (!empty($a_use_txt['JsonException'])) {
    $txt_interface .= "\n\t" . ' * @throws JsonException';
}
$txt_interface .= "\n\t" . '
     */
    public function findById(' . $clau_tip_txt . ' $' . $clau . '): ?' . $Q_clase . ';';

$txt_pgRepositorio .= "\n\t";
$txt_pgRepositorio .= '
    /**
     * Busca la clase con ' . $clau . ' en la base de datos .';
if (!empty($a_use_txt['JsonException'])) {
    $txt_pgRepositorio .= "\n\t" . ' * @throws JsonException';
}
$txt_pgRepositorio .= "\n\t" . '
     */
    public function findById(' . $clau_tip_txt . ' $' . $clau . '): ?' . $Q_clase . '
    {
        $aDatos = $this->datosById($' . $clau . ');
        if (empty($aDatos)) {
            return null;
        }
        return (new ' . $Q_clase . '())->setAllAttributes($aDatos);
    }';

if ($id_seq || $id_seq2) {
    if (!empty($id_seq2)) {
        $id_seq = $id_seq2;
    }
    $nomcamp = $a_auto[0];

    $txt_repository .= "\n\t";
    $txt_repository .= '
    public function getNewId()
    {
        return $this->repository->getNewId();
    }';

    $txt_interface .= "\n\t";
    $txt_interface .= '
    public function getNewId();';

    $txt_pgRepositorio .= "\n\t";
    $txt_pgRepositorio .= '
    public function getNewId()
    {
        $oDbl = $this->getoDbl();
        $sQuery = "select nextval(\'' . $id_seq . '\'::regclass)";
        return $oDbl->query($sQuery)->fetchColumn();
    }';

}

$txt_repository .= "\n}";
$txt_interface .= "\n}";
$txt_pgRepositorio .= "\n}";

/* ESCRIURE LA CLASSSE  PG REPOSITORY  --------------------------------- */
// crear el directorio infrastructure si no existe
$dir_infra = ServerConf::DIR . '/src/' . $grupo . '/infrastructure/repositories';
if ( !is_dir($dir_infra) && !mkdir($dir_infra, 0777, true) && !is_dir($dir_infra)) {
    throw new RunTimeException(sprintf('Directory "%s" was not created', $dir_infra));
}
$filename = $dir_infra . '/' . $pg_clase . '.php';
if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}
// Write $somecontent to our opened file.
if (fwrite($handle, $txt_pgRepositorio) === false) {
    echo "Cannot write to file ($filename)";
    die();
}
echo "<br>Success, wrote (somecontent) to file ($filename)";
fclose($handle);
/* ESCRIURE EL DIRECTORI CONTROLLERS  --------------------------------- */
// crear el directorio infrastructure si no existe
$dir_infra = ServerConf::DIR . '/src/' . $grupo . '/infrastructure/controllers';
if ( !is_dir($dir_infra) && !mkdir($dir_infra, 0777, true) && !is_dir($dir_infra)) {
    throw new RunTimeException(sprintf('Directory "%s" was not created', $dir_infra));
}

/* ESCRIURE LA CLASSE  REPOSITORYINTERFACE  --------------------------------- */
$dir_contracts = ServerConf::DIR . '/src/' . $grupo . '/domain/contracts';
if (!is_dir($dir_contracts) && !mkdir($dir_contracts, 0777, true) && !is_dir($dir_contracts)) {
    throw new RunTimeException(sprintf('Directory "%s" was not created', $dir_contracts));
}
$filename = $dir_contracts . '/' . $clase_interface . '.php';
if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}
// Write $somecontent to our opened file.
if (fwrite($handle, $txt_interface) === false) {
    echo "Cannot write to file ($filename)";
    die();
}
echo "<br>Success, wrote (somecontent) to file ($filename)";
fclose($handle);
/* ESCRIURE VALUE OBJECTS  --------------------------------- */
$dir_value = ServerConf::DIR . '/src/' . $grupo . '/domain/value_objects';
if (!is_dir($dir_value) && !mkdir($dir_value, 0777, true) && !is_dir($dir_value)) {
    throw new RunTimeException(sprintf('Directory "%s" was not created', $dir_value));
}
echo "<br>Success, create directory ($dir_value)";
/* ESCRIURE UN FITXER D'EXEMPLE  --------------------------------- */
$dir_domain = ServerConf::DIR . '/src/' . $grupo . '/domain';
if (!is_dir($dir_domain)) {
    if (!mkdir($dir_domain, 0777, true) && !is_dir($dir_domain)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir_domain));
    }
}
$filename = $dir_domain . '/example.php';
if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}
// Write $somecontent to our opened file.
if (fwrite($handle, '<?php') === false) {
    echo "Cannot write to file ($filename)";
    die();
}
echo "<br>Success, wrote (somecontent) to file ($filename)";
fclose($handle);

/* ESCRIURE LA CLASSE  REPOSITORY  --------------------------------- */
$dir_repositories = ServerConf::DIR . '/src/' . $grupo . '/application/repositories';
if (!is_dir($dir_repositories)) {
    if (!mkdir($dir_repositories, 0777, true) && !is_dir($dir_repositories)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir_repositories));
    }
}
$filename = $dir_repositories . '/' . $clase_repository . '.php';
if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}
// Write $somecontent to our opened file.
/*
if (fwrite($handle, $txt_repository) === false) {
    echo "Cannot write to file ($filename)";
    die();
}
echo "<br>Success, wrote (somecontent) to file ($filename)";
fclose($handle);
*/

/* ESCRIURE UN FITXER D'EXEMPLE  --------------------------------- */
/*
$dir_application = ServerConf::DIR . '/src/' . $grupo . '/application';
if (!is_dir($dir_application)) {
    if (!mkdir($dir_application, 0777, true) && !is_dir($dir_application)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir_application));
    }
}
$filename = $dir_application . '/example.php';
if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    die();
}
// Write $somecontent to our opened file.
if (fwrite($handle, '<?php') === false) {
    echo "Cannot write to file ($filename)";
    die();
}
echo "<br>Success, wrote (somecontent) to file ($filename)";
fclose($handle);
*/

/*AFEGIR DEPENDENCIA  --------------------------------- */

$dir_config = ServerConf::DIR . '/src/' . $grupo . '/config';
if (!is_dir($dir_config)) {
    if (!mkdir($dir_config, 0777, true) && !is_dir($dir_config)) {
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir_config));
    }
}
$filename = $dir_config . '/dependencies.php';

// Definir los strings a insertar
// Asumimos una estructura estándar src\entidad\...
$useInterface = "use src\\$grupo\\domain\\contracts\\{$clase_interface};";
$useImplementation = "use src\\$grupo\\infrastructure\\repositories\\{$pg_clase};";
$mappingLine = "    {$clase_interface}::class => autowire({$pg_clase}::class),";

if (file_exists($filename)) {
    $content = file_get_contents($filename);
    // Insertar los 'use'
    // Buscamos el último 'use' para insertar los nuevos después, o antes de la función autowire si es el final.
    // Estrategia: Buscar la línea "use function DI\autowire;" que suele ser la última importación.
    if (strpos($content, $useInterface) !== false) {
        die("El repositorio para $clase_interface ya parece existir en el archivo.\n");
    }
    // Insertar los 'use' antes de "use function DI\autowire;"
    $searchMarker = "use function DI\autowire;";
    $replacement = "$useInterface\n$useImplementation\n$searchMarker";

    $newContent = str_replace($searchMarker, $replacement, $content);

    // Insertar el mapeo en el array
    // Buscamos el cierre del array "];" y lo reemplazamos por la nueva línea + el cierre.
    $arrayEndMarker = "];";
    // Usamos strrpos para encontrar la última ocurrencia (el cierre del return array)
    $pos = strrpos($newContent, $arrayEndMarker);

    if ($pos !== false) {
        $newContent = substr_replace($newContent, $mappingLine . "\n" . $arrayEndMarker, $pos, strlen($arrayEndMarker));
    } else {
        die("No se pudo encontrar el cierre del array '];' en $filename.\n");
    }
} else {
    $newContent = "<?php\n\n";
    $newContent .= $useInterface . "\n";
    $newContent .= $useImplementation . "\n";
    $newContent .= "use function DI\autowire;\n";
    $newContent .= "\nreturn [\n";
    $newContent .= "// Mapeos de Interfaces a Implementaciones\n";
    $newContent .= $mappingLine . "\n";
    $newContent .= "];\n";
}
// Guardar el archivo
file_put_contents($filename, $newContent);
echo "Repositorio '$clase_interface' añadido correctamente a $filename.\n";
