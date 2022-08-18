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
    $data = str_replace(array('+','/','='),array('-','_','.'),$data);
    return $data;
}

function urlsafe_b64decode($string) {
    $data = str_replace(array('-','_','.'),array('+','/','='),$string);
    return base64_decode($data);
}

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
* 18-8-2022 corregido con la función mb_strtoupper. Ignoro porque no estaba así?¿
*  
*/
function strtoupper_dlb($texto) {
	//$texto=strtoupper($texto);
	$texto = mb_strtoupper($texto, 'UTF-8');
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
		default:
		    $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
		    exit ($err_switch);
	}
	if (empty($any)) { $any = $_SESSION['oConfig']->any_final_curs(); }
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
		default:
		    $err_switch = sprintf(_("opción no definida en switch en %s, linea %s"), __FILE__, __LINE__);
		    exit ($err_switch);
	}

}