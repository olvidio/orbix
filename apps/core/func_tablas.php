<?php
namespace core;
use web;
use personas\model\entity\GestorTelecoPersonaDl;
use ubis\model\entity\DescTeleco;
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



$a_num_romanos=array('1'=>"I",'2'=>"II",'3'=>"III",'4'=>"IV",'5'=>"V",'6'=>"VI",'7'=>"VII",'8'=>"VIII",'9'=>"IX",'10'=>"X",
'11'=>"XI",'12'=>"XII",'13'=>"XIII",'14'=>"XIV",'15'=>"XV",'16'=>"XVI",'17'=>"XVII",'18'=>"XVIII");

/**
* Para unificar los valores true ('t', 'true', 1, 'on...)
*
*
*@author	Daniel Serrabou
*@since		23/3/2020.
*		
*/
function is_true($val) {
    if ( is_string($val) ) {
        $val = ($val=='t')? 'true' : $val;
        $boolval = filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    } else {
        $boolval = $val;
    }
    
    return $boolval;
}

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
* Para poner string empty en los valores null de un array,
 * necesario para la función http_build_query, que no pone
 * los parametros con valor null
*
*@author	Daniel Serrabou
*@since		26/10/18.
*		
*/
function poner_empty_on_null(&$valor) {
	if ($valor === NULL) {
		$valor='';
	} 
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
/*
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
*/

/**
* Devuelve el número del tipo de encargo para hacer una selección SQL.
*
*	 En función de los parámetros que se le pasan:
*		$grupo		ctr,cgi,igl,otros,personales
*		$nom tipo	(el encargo en concreto)
*	Si un parámetro se omite, se pone un punto (.) para que la búsqueda sea qualquier número
*	ejemplo: 12....
*/
/*
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
 * 
 */
//-----------------------------------------------------------------------------------


/**
* Devuelve las profesiones actuales de una persona
*
*	 En función del parámetro $id_nom 
*		
*	
*/
/*
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
 * 
 */
/**
* Devuelve las profesiones actuales de una persona en una sola línea
*
*	 En función del parámetro $id_nom
*		
*	
*/
/*
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
 * 
 */
/**
* Devuelve los teleco de una persona especificados por
*
*	 parámetros $id_nom,$tipo_teleco,$desc_teleco,$separador
*		
*	Si $desc_teleco es '*', entonces se añade la descripción entre paréntesis
*      al final del número...
*/

function telecos_persona($id_nom,$tipo_teleco,$desc_teleco='',$separador) {

    $aWhere = [];
	$aWhere['id_nom'] = $id_nom;
	$aWhere['tipo_teleco'] = $tipo_teleco;
	if ($desc_teleco != '*' && !empty($desc_teleco)) {
		$aWhere['desc_teleco'] = $desc_teleco;
	}
	$GesTelecoPersonas = new GestorTelecoPersonaDl();
	$cTelecos = $GesTelecoPersonas->getTelecos($aWhere);
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
/*
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
 * 
 */

//-----------------------------------------------------------------------------------

//-----------------------------------------------------------------------------------




/**
* Función para corregir la del php strnatcasecmp. Compara sin tener en cuenta los acentos. La uso para ordenar arrays.
*  
*/
function strsinacentocmp($str1,$str2) {
	$acentos = array('Á','É','Í','Ó','Ú','À','È','Ì','Ò','Ù','Ä','Ë','Ï','Ö','Ü','Â','Ê','Î','Ô','Û','Ñ',
					'á','é','í','ó','ú','à','è','ì','ò','ù','ä','ë','ï','ö','ü','â','ê','î','ô','û','ñ'
					);
	$sin = array('a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','nz',
				'a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','a','e','i','o','u','nz'
					);

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
		    $ini_d = $_SESSION['oConfig']->getDiaIniStgr();
		    $ini_m = $_SESSION['oConfig']->getMesIniStgr();
		    $fin_d = $_SESSION['oConfig']->getDiaFinStgr();
		    $fin_m = $_SESSION['oConfig']->getMesFinStgr();
			break;
		case "crt":
		    $ini_d = $_SESSION['oConfig']->getDiaIniCrt();
		    $ini_m = $_SESSION['oConfig']->getMesIniCrt();
		    $fin_d = $_SESSION['oConfig']->getDiaFinCrt();
		    $fin_m = $_SESSION['oConfig']->getMesFinCrt();
			break;
	}
	if (empty($any)) { $any = ConfigGlobal::any_final_curs(); }
	$any0 = $any - 1;
	//ConfigGlobal::mes_actual()=date("m");
	//if (ConfigGlobal::mes_actual()>$fin_m) ConfigGlobal::any_final_curs()++; // debe estar antes de llamar a la función.
	$inicurs= new web\DateTimeLocal("$any0-$ini_m-$ini_d");
	$fincurs= new web\DateTimeLocal("$any-$fin_m-$fin_d");

	switch ($que) {
		case "inicio":
			return $inicurs;
			break;
		case "fin":
			return $fincurs;
			break;
	}

}