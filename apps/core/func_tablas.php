<?php
namespace core;
/**
* Esta página sólo contiene funciones. Es para incluir en otras.
*
*
*@package	delegacion
*@subpackage	fichas
*@author	Daniel Serrabou
*@since		15/5/02.
*		
*/

/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
/**
*
* 
*
*/
function urlsafe_b64encode($string) {
    $data = base64_encode($string);
    //$data = str_replace(array('+','/','='),array('-','_',''),$data);
    $data = str_replace(array('+','/','='),array('-','_','.'),$data);
    return $data;
}

function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_','.'),array('+','/','='),$string);
	/*
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
        $data .= substr('====', $mod4);
    }
	*/
    return base64_decode($data);
}


/**
*
* Devuelve el objeto Base de Daros dónde se encuentra la tabla.
*
*/
function que_DB($tabla) {
	// para las tablas que están en otra base de datos:
	if (in_array($tabla,array('u_centros_sf','u_direcciones_sf'))) {
		$oDbl = $GLOBALS['oDBA'];
	} else {
		$oDbl = $GLOBALS['oDB'];
	}
	return $oDbl;
}

/**
*
* creo un array con los lugares posibles para centros...
*	primero el id_lugar, después la sigla y tercero el nombre
*
*/
function array_ctrs() {
	$oDB = $GLOBALS['oDB'];
	// 1º ctr de dl
	$query_ctr="SELECT id_ubi, nombre_ubi, substring(tipo_ctr from 1 for 1) as tipo FROM u_centros_dl
							WHERE dl='".$GLOBALS['dele']."' AND tipo_ctr ~ '^(a|n|s)' AND status='t'
							ORDER BY tipo,nombre_ubi";
	$oDBSt_q_ctr=$oDB->query($query_ctr);
	$i=0;
	foreach ($oDBSt_q_ctr->fetchAll() as $row_ctr) {
		$i++;
		$lugares[]=array ( 'id_ubi' => $row_ctr[0],
							'nombre_ubi' => $row_ctr[1] );
	}
	return $lugares;
} // fin funcion

$a_num_romanos=array('1'=>"I",'2'=>"II",'3'=>"III",'4'=>"IV",'5'=>"V",'6'=>"VI",'7'=>"VII",'8'=>"VIII",'9'=>"IX",'10'=>"X",
'11'=>"XI",'12'=>"XII",'13'=>"XIII",'14'=>"XIV",'15'=>"XV",'16'=>"XVI",'17'=>"XVII",'18'=>"XVIII");

/**
* Para poner null en los valores vacios de un array
*
*
*@author	Daniel Serrabou
*@since		28/10/09.
*		
*/
function poner_null(&$valor) {
	if (!$valor && $valor !== 0) { //admito que sea 0.
		$valor=NULL;
	} 
}

/**
* Para clacular la edad a partir de la fecha de nacimiento
*
*
*@author	Daniel Serrabou
*@since		25/11/2010.
*		
*/
function edad($f_nacimiento) {
	if (!empty($f_nacimiento)) {
		list($d,$m,$a) = preg_split('/[\.\/-]/', $f_nacimiento );	//separo la fecha en dia, mes, año
		$ah=date("Y");
		$mh=date("m");
		$inc_m=0 ;
		$mh >= $m ? 0 : $inc_m=1 ; 
		$edad=$ah - $a - $inc_m;	
	} else {
		$edad ="-";
	}
	return $edad;
}

//definición de variables globales para las funciones de tipo de encargo (tarea):

$t_grupo=array(
				"ctr"=>1,
				"cgi"=>2,
				"igl"=>3,
				"stgr"=>4,
				"estudio/descanso"=>5,
				"otros"=>6,
				"personales"=>7,
				"Zona Misas"=>8
			);
//definición de variables globales para las funciones de tipo de actividad:

$Ga_sfsv=array(
				"sv"=>1,
				"sf"=>2,
				"reservada"=>3
			);
			
$Ga_asistentes=array(
				"n"=>1,
				"nax"=>2,
				"agd"=>3,
				"s"=>4,
				"sg"=>5,
				"sss+" =>6,
				"sr"=>7,
				"sr-nax"=>8,
				"sr-agd"=>9
			);

$Ga_actividad = array (
				"crt"=>1,
				"ca"=>2,
				"cv"=>3,
				"cve"=>4,
				"cv-crt"=>5
			);
// fin de definicion----------------------------------------------------------


/**
* Devuelve los parámetros de un encargo en función del tipo de encargo.
*
* Es la función inversa de "id_tipo_encargo()".
* Se le pasa el id_tipo_encargo, y devuelve un array ($tipo) con los siguientes valores:
*
*		grupo		ctr,cgi,igl,otros,personales
*		nom_tipo	(el encargo en concreto)
*
*@author	Daniel Serrabou
*@since		28/2/06.
*		
*/
function encargo_de_tipo($id_tipo_enc){
	global $t_grupo;

	//transpongo los vectores para buscar por números y no por el texto
	$ft_grupo = array_flip ($t_grupo);
		
	$ta1=substr($id_tipo_enc,0,1);
	$ta2=substr($id_tipo_enc,1,3);

	$oDB = $GLOBALS['oDB'];

	if ($ta1==".") {
		ksort($ft_grupo);
		foreach ($ft_grupo as $key => $value) {
			$grupo[]=$key."#".$value;
		}
	} else {
		$grupo=$ft_grupo[$ta1];
	}

	$query="SELECT * FROM t_tipo_enc where id_tipo_enc::text ~ '".$id_tipo_enc."' order by tipo_enc";
	//echo $query;
	$oDBSt_id=$oDB->query($query);

	if ($ta2=="...") {
		$i=0;
		foreach ($oDBSt_id->fetchAll() as $row) {
			$nom_tipo[] = $row["id_tipo_enc"]."#".$row["tipo_enc"];
			$i++;
		}
	} else {
	   $row=$oDBSt_id->fetch(\PDO::FETCH_ASSOC);
	   $nom_tipo=$row["tipo_enc"];
	}
	$tipo=array(
				"grupo" => $grupo,
				"nom_tipo" => $nom_tipo
				);

	return $tipo;
}


/**
* Devuelve el número del tipo de encargo para hacer una selección SQL.
*
*	 En función de los parámetros que se le pasan:
*		$grupo		ctr,cgi,igl,otros,personales
*		$nom tipo	(el encargo en concreto)
*	Si un parámetro se omite, se pone un punto (.) para que la búsqueda sea qualquier número
*	ejemplo: 12....
*/
function id_tipo_encargo($grupo,$nom_tipo) {
	global $a_grupo;
	
    $condta1='.';
	$condta2='.';
    $condta3='..';
	
    if (!empty($grupo)) { $condta1=$a_grupo[$grupo]; }
	
    $condta=$condta1 . $condta2 . $condta3 ;
	
	if ($nom_tipo and $nom_tipo!="...") {
		   $condicion="id_tipo_enc::text ~ '" . $condta. "'";
			$oDB = $GLOBALS['oDB'];
	       $query="SELECT * FROM t_tipo_enc where tipo_enc='".$tipo_enc."' AND ".$condicion;
			$oDBSt_id=$oDB->query($query);
	       $row= $oDBSt_id->fetch();
		   $id_tipo_enc =$row["id_tipo_enc"];
			$condta=$id_tipo_enc;
	}
	
	
	return $condta;
}
//-----------------------------------------------------------------------------------


/**
* Devuelve las profesiones actuales de una persona
*
*	 En función del parámetro $id_nom 
*		
*	
*/
function profesion($id_nom) {
	$oDB=$GLOBALS['oDB'];
	$sql_prof="SELECT empresa, cargo, actual
					FROM d_profesion
					WHERE actual='t' AND id_nom=$id_nom ";
					
	//echo "qq: $sql_prof<br>";
	$oDBSt_prof=$oDB->query($sql_prof);
	$p=0;
	$profesion="";
	foreach ($oDBSt_prof->fetchAll() as $row_p) {
		$p++;
		$empresa=$row_p["empresa"];
		$cargo=$row_p["cargo"];
		$profesion=$profesion.$cargo." ".$empresa."<br>";
	}
	return $profesion;
}
/**
* Devuelve las profesiones actuales de una persona en una sola línea
*
*	 En función del parámetro $id_nom
*		
*	
*/
function profesion_1_linea($id_nom) {
	$oDB=$GLOBALS['oDB'];
	$sql_prof="SELECT empresa, cargo, actual
					FROM d_profesion
					WHERE actual='t' AND id_nom=$id_nom ";
	//echo "qq: $sql_prof<br>";
	$oDBSt_prof=$oDB->query($sql_prof);
	$p=0;
	$profesion="";
	foreach ($oDBSt_prof->fetchAll() as $row_p) {
		$p++;
		$empresa=$row_p["empresa"];
		$cargo=$row_p["cargo"];
		$profesion=$profesion.$cargo." ".$empresa.",";
		}
	$profesion=substr($profesion,0,strlen($profesion)-1);
	return $profesion;
}
/**
* Devuelve los teleco de una persona especificados por
*
*	 parámetros $id_nom,$tipo_teleco,$desc_teleco,$separador
*		
*	Si $desc_teleco es '*', entonces se añade la descripción entre paréntesis
*      al final del número...
*/
function telecos_persona($id_nom,$tipo_teleco,$desc_teleco='',$separador) {
	require_once('classes/personas-ubis/xd_desc_teleco.class');
	require_once('classes/personas/d_teleco_personas_gestor.class');

	$aWhere['id_nom'] = $id_nom;
	$aWhere['tipo_teleco'] = $tipo_teleco;
	if ($desc_teleco != '*' && !empty($desc_teleco)) {
		$aWhere['desc_teleco'] = $desc_teleco;
	}
	$GesTelecoPersonas = new GestorTelecoPersona();
	$cTelecos = $GesTelecoPersonas->getTelecosPersona($aWhere);
	$tels='';
	$separador=empty($separador)? ".-<br>": $separador;
	foreach ($cTelecos as $oTelecoPersona) {
		$iDescTel = $oTelecoPersona->getDesc_teleco();
		$num_teleco = $oTelecoPersona->getNum_teleco();
		if ($desc_teleco=="*" && !empty($iDescTel)) {
			$oDescTel = new DescTeleco($iDescTel);
			$tels.=$num_teleco."(".$oDescTel->getDesc_teleco().")".$separador;
		} else {
			$tels.=$num_teleco.$separador;
		}
	}
	$tels=substr($tels,0,-(strlen($separador)));
	return $tels;
}

/**
* Devuelve los teleco de un ubi especificados por
*
*	 parámetros $id_ubi,$tipo_teleco,$desc_teleco,$separador
*		
*	Si $desc_teleco es '*', entonces se añade la descripción entre paréntesis
*      al final del número...
*/
function teleco($id_ubi,$tipo_teleco,$desc_teleco,$separador) {
	require_once('classes/personas-ubis/xd_desc_teleco.class');
	require_once('classes/ubis/d_teleco_ubis_gestor.class');

	$aWhere['id_ubi'] = $id_ubi;
	$aWhere['tipo_teleco'] = $tipo_teleco;
	if ($desc_teleco != '*' && !empty($desc_teleco)) {
		$aWhere['desc_teleco'] = $desc_teleco;
	}
	$GesTelecoUbis = new GestorTelecoUbi();
	$cTelecos = $GesTelecoUbis->getTelecosUbi($aWhere);
	$tels='';
	$separador=empty($separador)? ".-<br>": $separador;
	foreach ($cTelecos as $oTelecoUbi) {
		$iDescTel = $oTelecoUbi->getDesc_teleco();
		$num_teleco = trim ($oTelecoUbi->getNum_teleco());
		if ($desc_teleco=="*" && !empty($iDescTel)) {
			//$tels.=$num_teleco." (".$DescTel.")".$separador;
			$oDescTel = new DescTeleco($iDescTel);
			$tels.=$num_teleco."(".$oDescTel->getDesc_teleco().")".$separador;
		} else {
			$tels.=$num_teleco.$separador;
		}
	}
	$tels=substr($tels,0,-(strlen($separador)));
	return $tels;
}

//-----------------------------------------------------------------------------------

/**
* Es para dibujar los cuadros de checbox para el tipo de labor de los ubis.
*
* En $nomcamp esta el nombre del campo
* En $bin está un valor del tipo: B'000100100000' (12 dígitos)
*/
function cuadroslabor($nomcamp,$bin){
$camp=$nomcamp."[]";
//si $bin es nulo, le pongo todo 0
if (empty($bin)) { $bin=0; }
for ($i=0;$i<12;$i++) {
	switch ($i) {
			case "11":
				if ($bin & 1) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"1\" $chk>mayores";
				break;
			case "10":
				if ($bin & 2) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"2\" $chk>jóvenes";
				break;
			case "9":
				if ($bin & 4) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"4\" $chk>univ";
				break;
			case "8":
				if ($bin & 8) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"8\" $chk>bachilleres";
				break;
			case "7":
				if ($bin & 16) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"16\" $chk>club";
				break;
			case "6":
				if ($bin & 32) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"32\" $chk>sss+";
				break;
			case "5":
				if ($bin & 64) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"64\" $chk>sg";
				break;
			case "4":
				if ($bin & 128) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"128\" $chk>agd";
				break;
			case "3":
				if ($bin & 256) {$chk="checked";} else {$chk="";}
				echo "   <input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"256\" $chk>n";
				break;
			case "2":
				if ($bin & 512) {$chk="checked";} else {$chk="";}
				echo "<input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"512\" $chk>sr";
				break;
	}	
}

}
//-----------------------------------------------------------------------------------

/**
* Es para no volver a escribir todo en la función select.
*
* Sirve para crear el nombre más los nexos más los apellidos.
*
*/
function na(){		
	$nom="case when trato isnull or trato = '' then '' else trato||' ' end 
	||COALESCE(apel_fam, nom)||
	case when nx1 = '' or nx1 isnull then ' ' else ' '||nx1||' ' end 
	||apellido1||
	case when nx2 = '' or nx2 isnull then ' ' else ' '||nx2||' ' end 
	||
	case when apellido2 = '' or apellido2 isnull then '' else ' '||apellido2||' ' end 
	";
	return $nom;
}
// idem sin el tratamiento (para los listados de des)
function na_cr_sin(){		
	$nom="nom||
	case when nx1 = '' or nx1 isnull then ' ' else ' '||nx1||' ' end 
	||apellido1||
	case when nx2 = '' or nx2 isnull then ' ' else ' '||nx2||' ' end 
	||
	case when apellido2 = '' or apellido2 isnull then '' else ' '||apellido2||' ' end 
	";
	return $nom;
}
//-----------------------------------------------------------------------------------

/**
* Es para no volver a escribir todo en la función select.
*
* Sirve para crear el apellidos, nombre.
*/
	
function ap_nom(){
	
$nom="apellido1||
	case when nx2 = '' or nx2 isnull then ' ' else ' '||nx2||' ' end 
	||
	case when apellido2 = '' or apellido2 isnull then '' else ''||apellido2||'' end 
	||', '||
	case when trato isnull or trato = '' then '' else trato||' ' end 
	||COALESCE(apel_fam, nom)||
	case when nx1 = '' or nx1 isnull then '' else ' '||nx1||' ' end 
	";
	return $nom;
}
/**
* Es para no volver a escribir todo en la función select.
*
* Sirve para crear el apellidos, nombre. En el caso especial de enviar datos a cr sin guiones
*/
	
function ap_nom_cr_1_05(){
	
$nom="case when p.nx1 = '' or p.nx1 isnull then '' else ''||p.nx1||' ' end 
	||p.apellido1|| 
	case when p.nx2 isnull or p.nx2 = '' then '' else ' '||p.nx2||'' end 
	||
	case when p.apellido2 = '' or p.apellido2 isnull then '' else ' '||p.apellido2||'' end 
	||', '||p.nom 
	";
	return $nom;
}

/**
* Es para no volver a escribir todo en la función select.
*
* Sirve para crear el apellidos, nombre. En el caso especial de enviar datos a cr con guiones
*/
/*	
function ap_nom_cr(){
	// translate, cambia todos los espacios de apellido1 por guiones
$nom="translate(apellido1, ' ', '-')||
	case when nx2 = '' or nx2 isnull then ' ' else '-'||translate(nx2, ' ', '-')||' ' end 
	||
	case when apellido2 = '' or apellido2 isnull then '' else ''||apellido2||'' end 
	||', '||
	nom||
	case when nx1 = '' or nx1 isnull then '' else ' '||nx1||' ' end 
	";
	return $nom;
}
*/
//-----------------------------------------------------------------------------------


/**
*
* Esta función devuelve datos sobre el campo de una tabla
*
* $oDB es la conexión al Postgresql
* $tabla es el nombre de la tabla
* $camp es el nombre del campo
* $que es el dato que queremos saber:
*		"longitud"	longitud del campo
*		"nulo"		si es permite nulo o no
*		"tipo"		int, varchar, bool...
*		"valor"		valor por defecto
*/
function datos_campo($oDB,$tabla,$camp,$que){
	if ($tabla && $camp) {
		//tipo de campos
		$sql_get_fields = "
			SELECT 
				a.attnum,
				a.attname AS field, 
				t.typname AS type, 
				a.attlen AS length,
				a.atttypmod AS lengthvar,
				a.attnotnull AS notnull
			FROM 
				pg_class c, 
				pg_attribute a, 
				pg_type t
			WHERE 
				c.relname = '$tabla'
				and a.attnum > 0
				and a.attrelid = c.oid
				and a.atttypid = t.oid
				and a.attname = '$camp'
			ORDER BY a.attnum
		";
		//echo "sql: $sql_get_fields<br>";
		$oDBSt_res_fields=$oDB->query($sql_get_fields);
		$row = $oDBSt_res_fields->fetch(\PDO::FETCH_ASSOC);
		if ($row['length'] > 0) {
			$llarg = $row['length'];
		} else if($row['lengthvar'] > 0) {
			$llarg = $row['lengthvar'] - 4;
		} else {
			$llarg = "var";
		}
		if ($row['type'] == "date") {
			$llarg = 8;
		}
		$null=$row['notnull'];

		//respuesta a lo que se pide
		switch ($que){
			case "longitud" :
			  return $llarg;
			  break;
			case "nulo":
				return $null;
				break;
			case "tipo":
				return $row['type'];
				break;
			case "valor":
				/*  valores por defecto
				/ creo  que las posibilidades son:
					número
					'txto'::character...
					true, false
					nextval(),idlocal()... -> funcion
				*/
				$sql_get_default = "
					SELECT d.adsrc AS rowdefault
					FROM pg_attrdef d, pg_class c 
					WHERE 
						c.relname = '$tabla' AND 
						c.oid = d.adrelid AND
						d.adnum =". $row['attnum'];
				
				//echo "sql_def: $sql_get_default<br>";
				$oDBSt_def_res=$oDB->query($sql_get_default);
				if (!$oDBSt_def_res->rowCount()) {
					$rowdefault = "";
				} else {
					$rowdefault = $oDBSt_def_res->fetchColumn();
					$rta=preg_match_all("/^'([\w]+)'::(.*)/", $rowdefault, $matches, PREG_SET_ORDER);
					/*
					foreach ($matches as $val) {
						echo "matched: " . $val[0] . "\n";
						echo "part 1: " . $val[1] . "\n";
						echo "part 2: " . $val[3] . "\n";
						echo "part 3: " . $val[4] . "\n\n";
					}
					*/
					if (!empty($rta)) {
						$rowdefault=$matches[0][1];
					} elseif (strstr($rowdefault,'(')) {
						$rowdefault="function";
					}
				}
				//echo "$rta; default: $rowdefault<br>";
				return $rowdefault;
				break;
		} 
	}
}
//-----------------------------------------------------------------------------------

/**
* Devuelve un array con los nombres de los campos que forman la clave primaria de la tabla
*
* Si no existe clave primaria, devuelve la primera clave única que encuentra. Si hay más de una, no 
* sé que puede pasar.
*
*/
function primaryKey($oDB,$tabla) {
	// si la tabla tiene el schema, hay que separalo:
	$schema_sql = '';
	$schema = strtok($tabla,'.');
	if ($schema !== $tabla) {
		$tabla = strtok('.');
		$schema_sql = "and n.nspname='$schema' ";
	}
	//miro si existe clave primaria, sino cojo la unica
	$query_primaria=" select  i.indkey, c.oid
			from pg_catalog.pg_index i, pg_catalog.pg_class c, pg_catalog.pg_namespace n
			where i.indisprimary='t' and i.indisunique='t' and i.indrelid=c.oid and n.oid = c.relnamespace and c.relname='$tabla' $schema_sql";
	$oDBSt_resultado=$oDB->query($query_primaria);
	if (!$oDBSt_resultado->rowCount()) {
		$query_unica=" select  i.indkey, c.oid
				from pg_catalog.pg_index i, pg_catalog.pg_class c, pg_catalog.pg_namespace n
				where i.indisunique='t' and i.indrelid=c.oid and n.oid = c.relnamespace and c.relname='$tabla' $schema_sql";
		$oDBSt_resultado=$oDB->query($query_unica);
	}
	//buscar el nombre
	$row = $oDBSt_resultado->fetch(\PDO::FETCH_ASSOC);
	$claves = explode (" ",$row['indkey']);
	$oid_tabla = $row['oid'];
	if (empty($oid_tabla)) exit ('Quizà falta definir la clave primaria');
	foreach ($claves as $clave) {
		$query_nom="select attname
					from pg_attribute
					where attrelid='$oid_tabla' and attnum='$clave'";
		$oDBSt_resultado=$oDB->query($query_nom);
		$row = $oDBSt_resultado->fetch(\PDO::FETCH_ASSOC);
		$campo[]=$row['attname'];
	}
return $campo;
}

// --------------------------------------------------------------------------------------------------
function mostrar_tabla_print($a_cabeceras,$a_valores,$id_tabla="uno") {
	echo "función desactivada. Probar con la clase Lista.<br>";
	exit;
	if (empty($a_valores)) {
		return	_("No hay ninguna fila");
	}
	$cab=1;
	foreach ($a_cabeceras as $name) {
		if (!empty($name)) {
		   $cabecera .= "<th class=cabecera>$name</th>\n";
		} else {
		   $cabecera .= "<th class=cabecera tipo='notext' ></th>\n"; 
		}
		   $cab++;
	}
	$cabecera.= "</tr>\n"; 
	// Para generar un id único
	$ahora=date("Hms");
	$f=1;
	foreach($a_valores as $num_fila=>$fila) {
		$f++;
		$id_fila=$f.$ahora;
		$tbody.="<tr id='$id_fila'>";
		ksort($fila);
		foreach ($fila as $col=>$valor) {
			if ($col=="clase") { continue; }
			if ($col=="order") { continue; }
			if(is_array($valor)) {
				$val=$valor['valor'];
				if ( $ira=$valor['ira'] ) {
					$tbody.="<td><span class=link onclick=fnjs_update_div('#main','$ira') >$val</span></td>";
				}
				if ( $colspan=$valor['span'] ) {
					$tbody.="<td colspan='$colspan'>$val</td>";
				}
			} else {
				// si es una fecha, pongo la clase fecha, para exportar a excel...
				if (preg_match("/^(\d)+[\/-]\d\d[\/-](\d\d)+$/",$valor)) {
					list( $d,$m,$y) = preg_split('/[:\/\.-]/',$valor);
					$fecha_iso=date("Y-m-d",mktime(0,0,0,$m,$d,$y));
					$tbody.="<td class='fecha' fecha_iso='$fecha_iso'>$valor</td>";
				} else {
					$tbody.="<td>$valor</td>";
				}
			}
		}
		$tbody.="</tr>\n";
	}
	$tt.="<table cellspacing=0 nowrap rules=all border=1 id='$id_tabla'>\n";
	$tt.="<thead><tr>";
	if (!empty($b)) $tt.="<th class='unsortable' tipo='notext'></th>";
	$tt.="$cabecera</thead><tbody>";
	$tt.= $tbody;
	$tt.="</tbody></table>\n";

	return $tt;
}

// Es del phpPgAdmin

function bool_YesNo($boolVal) {
	$strNo=_("no");
	$strYes=_("sí");
	if (preg_match("/^t|1|^y/i", $boolVal)) {
		return $strYes;
	} elseif (preg_match("/^f|0|^n/i", $boolVal)) {
		return $strNo;
	}
}


function comprobar_oficina($depende,$tabla) {
	$rta="f";
	if ($depende=="t") {
		switch ($tabla) {
			case "n":
				if ($GLOBALS['oPerm']->have_perm("sm")) { $rta="t"; } else { $rta="f"; }
				break;
			case "a":
				if ($GLOBALS['oPerm']->have_perm("agd")) { $rta="t"; } else { $rta="f"; }
				break;
			case "s":
				if ($GLOBALS['oPerm']->have_perm("sg")) { $rta="t"; } else { $rta="f"; }
				break;
			case "pn":
				if ($GLOBALS['oPerm']->have_perm("sm")) { $rta="t"; } else { $rta="f"; }
				break;
			case "pa":
				if ($GLOBALS['oPerm']->have_perm("agd")) { $rta="t"; } else { $rta="f"; }
				break;
		}
	}
	return $rta;
}

/**
* Función para corregir la del php strnatcasecmp. Compara sin tener en cuenta los acentos. La uso para ordenar arrays.
*  
*/
function strsinacentocmp($str1,$str2) {
	$acentos = array('Á','É','Í','Ó','Ú','À','È','Ò','Ñ','á','é','í','ó','ú','à','è','ò','ñ');
	//$sin = array('a','e','i','o','u','ñ');
	$sin = array('a','e','i','o','u','a','e','o','ñ','a','e','i','o','u','a','e','o','ñ');

	$str1 = str_replace($acentos,$sin,$str1);
	$str2 = str_replace($acentos,$sin,$str2);
	return strnatcasecmp  ($str1,$str2);
}

/**
* Función para corregir la del php strtoupper. No pone en mayúsculas las vocales acentuadas
*  
*/
function strtoupper_dlb($texto) {
	$texto=strtoupper($texto);
	$minusculas = array("á","é","í","ó","ú","à","è","ò","ñ");
	$mayusculas = array("Á","É","Í","Ó","Ú","À","È","Ò","Ñ");

	return str_replace($minusculas,$mayusculas,$texto);
}

/**
* Función para saber la fecha de inicio y fin de curso según el año.
*  
*/
function curso_est($que,$any,$tipo="est") {
	switch ($tipo) {
		case "est":
			list($ini_d,$ini_m) = preg_split('/[:\/\.-]/', ConfigGlobal::$est_inicio ); //los delimitadores pueden ser /, ., -, :	
			list($fin_d,$fin_m) = preg_split('/[:\/\.-]/', ConfigGlobal::$est_fin ); //los delimitadores pueden ser /, ., -, :	
			break;
		case "crt":
			list($ini_d,$ini_m) = preg_split('/[:\/\.-]/', ConfigGlobal::$crt_inicio ); //los delimitadores pueden ser /, ., -, :	
			list($fin_d,$fin_m) = preg_split('/[:\/\.-]/', ConfigGlobal::$crt_fin ); //los delimitadores pueden ser /, ., -, :	
			break;
	}
	if (empty($any)) { $any = ConfigGlobal::any_final_curs(); }
	//ConfigGlobal::mes_actual()=date("m");
	//if (ConfigGlobal::mes_actual()>$fin_m) ConfigGlobal::any_final_curs()++; // debe estar antes de llamar a la función.
	$inicurs= date("d/m/Y", mktime(0,0,0,$ini_m,$ini_d,$any-1)) ;
	$fincurs= date("d/m/Y", mktime(0,0,0,$fin_m,$fin_d,$any)) ;

	switch ($que) {
		case "inicio":
			return $inicurs;
			break;
		case "fin":
			return $fincurs;
			break;
	}

}
?>
