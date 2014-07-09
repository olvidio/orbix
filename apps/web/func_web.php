<?php
namespace web;
use core;
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
* Función para dibujar el campo de una tabla. La etiqeta más el contenido.
*  
* Se pone en las celdas de una tabla. 
*	$texto = nombre del campo
*	$size = tamaño del cuadro input donde va el valor del campo
*	$span1 = valor span de la celda de la etiqueta. Si span1=0, no se ponen las etiquetas <td>.
*	$span2 = valor span de la celda del contenido. Si span2=0, la etiqueta y el valor en la misma celda.
*/
function dibujar_campo($obj,$atributo,$size,$span1,$span2) {
	$get = ucfirst($atributo);
	$getdatos = 'getDatos'.ucfirst($atributo);

	$class='';
	$dibujo="";
	$valor = $obj->$get;
	$valor=htmlspecialchars($valor);
	$oDatosCampo = $obj->$getdatos();
	$etiqueta = $oDatosCampo->getEtiqueta();
	// si el campo es fecha, añado la clase=fecha
	if ($oDatosCampo->getTipo() == 'fecha') $class = 'fecha';

	/*
	$help=$a_valores_campo["help"];
	$help_ref=$a_valores_campo["help_ref"];
	*/
	$help='';
	$help_ref='';

	//$name=$texto."_".$a_valores_campo["name"];
	$name=$atributo;
		
	if (!empty($span1)) $dibujo.= "<td colspan='$span1'>";
	$dibujo .= "<span class=\"etiqueta\" ondblclick=\"fnjs_help('$help_ref')\" >".ucfirst($etiqueta)."</span>";
	if (!empty($span2)) { $dibujo.= "</td><td colspan='$span2'>"; } else { $dibujo.="&nbsp"; }
	if ($oDatosCampo->getTipo() == 'check') {
		$chk = ($valor)? 'checked' : '';
		$dibujo .= "<input class=\"$class contenido\" id=\"$name\" name=\"$name\" type=checkbox $chk >";
	} else {
		$dibujo.= "<input class=\"$class contenido\" size=$size id=\"$name\" name=\"$name\" value=\"$valor\" title=\"$help\">";
	}
	if (!empty($span1)) $dibujo.= "</td>";

	return $dibujo;
}


/**
* dibuja un menu desplegable con las opciones en un array.
*/
function desplegable_2($nombre,$opciones,$selected,$blanco) {
	echo "<select id=\"$nombre\" name=\"$nombre\">";
	options_2($opciones,$selected,$blanco);
	echo "</select>";
}

/**
*
* Esta función sirve para hacer el echo en html de las opciones de un input tipo select.
*
* Se pasa un array con las opciones: primera posicion el valor, segunda (si existe) el nombre a poner
* $ selected es el valor para el que se quiere que quede seleccionado por defecto
* $blanco es 0 o 1 para decir si queremos que se ponga la opcion en blanco o no.
* 	
*/
function options_2($opciones,$selected,$blanco) {
	if (!empty($blanco)) { echo "<option></option>"; }
	reset ($opciones);
	while (list ($clave, $valor) = each ($opciones)) {
		list( $clave, $val ) = preg_split('/#/', $valor );
		if ($clave==$selected) { $sel="selected"; } else { $sel=""; }
		echo "<option value=\"$clave\" $sel>$val</option>";
	}
}

function desplegable($nombre,$opciones,$selected,$blanco) {
// dibuja un menu desplegable con las opciones en un array.
	echo "<select id=\"$nombre\" name=\"$nombre\">";
	options($opciones,$selected,$blanco);
	echo "</select>";
}

/**
*  
* Esta función sirve para hacer el echo en html de las opciones de un input tipo select
* Se pasa un array con las opciones: primera posicion el valor, segunda (si existe) el nombre a poner
* $ selected es el valor para el que se quiere que quede seleccionado por defecto
* $blanco es 0 o 1 para decir si queremos que se ponga la opcion en blanco o no.
*/ 	
function options($opciones,$selected,$blanco) {
	if (!empty($blanco)) { echo "<option></option>"; }
	if (is_array($opciones)) {
		reset ($opciones);
		while (list ($clave, $val) = each ($opciones)) {
			if ($clave==$selected) { $sel="selected"; } else { $sel=""; }
			echo "<option value=\"$clave\" $sel>$val</option>";
		}
	}
}
/* variación para poder meterlo en una variable de la que posteriormente se hará un echo. */
function options_var($opciones,$selected,$blanco) {
	$txt="";
	if (!empty($blanco)) { $txt.="<option></option>"; }
	reset ($opciones);
	while (list ($clave, $val) = each ($opciones)) {
		if ($clave==$selected) { $sel="selected"; } else { $sel=""; }
		$txt.="<option value=\"$clave\" $sel>$val</option>";
	}
	return $txt;
}


/**
* dibuja un menu desplegable con las opciones en un array de la base de datos.
*/
function db_desplegable($nombre,$opciones,$selected,$blanco) {
	echo "<select id=\"$nombre\" name=\"$nombre\">";
	pdo_options($opciones,$selected,$blanco);
	echo "</select>";
}

/**
*
* Esta función sirve para hacer el echo en html de las opciones de un input tipo select
*
* Se pasa un array de la base de datos con las opciones: primera posicion el valor, segunda (si existe) el nombre a poner
* $ selected es el valor para el que se quiere que quede seleccionado por defecto
* $blanco es 0 o 1 para decir si queremos que se ponga la opcion en blanco o no.
*/
/** 
* 
* idem pero con la aplicacion PDO
*/
function pdo_options($opciones,$selected,$blanco) {
	if (!empty($blanco)) { echo "<option />"; }
	foreach($opciones as $row){
		if (empty($row[1])) {$a=0;} else {$a=1;} // para el caso de solo tener un valor
		if ($row[0]==$selected) { $sel="selected"; } else { $sel=""; }
		echo "<option value=\"$row[0]\" $sel>$row[$a]</option>";
	}
}
/* variación para poder meterlo en una variable de la que posteriormente se hará un echo. */
/* idem para DPO.    variación para poder meterlo en una variable de la que posteriormente se hará un echo. */
function pdo_options_var($opciones,$selected,$blanco) {
	$txt="";
	if (!empty($blanco)) { $txt.="<option></option>"; }
	foreach($opciones as $row){
		if (!isset($row[1])) {$a=0;} else {$a=1;} // para el caso de solo tener un valor
		if ($row[0]==$selected) { $sel="selected"; } else { $sel=""; }
		$txt.="<option value=\"$row[0]\" $sel>$row[$a]</option>";
	}
	return $txt;
}
/**
*
* Esta función sirve para ir a una página. desde un link, a traves de java: location.
* El parámetro $form sirve para indicar si se pone una direccion absoluta (http:...) o una relativa al $web (es para el caso del action de un formulario).
*
*/
function link_a($go_to,$form) {
	$go=strtok($go_to,"@");
	if ($go=="session" && isset($_SESSION['session_go_to']) ) {
		$g=strtok("@");
		empty($_SESSION['session_go_to'][$g]['pag'])? $pagina='' : $pagina=$_SESSION['session_go_to'][$g]['pag']."?go_to=$go_to";
		empty($_SESSION['session_go_to'][$g]['dir_pag'])? $dir_pag='' : $dir_pag=$_SESSION['session_go_to'][$g]['dir_pag'];
		empty( $_SESSION['session_go_to'][$g]['target'])? $frame ='' : $frame = $_SESSION['session_go_to'][$g]['target'];
		if (!empty($dir_pag)) {$dire=$dir_pag; } else { $dire=getcwd(); }
		$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
		// quito la barra inicial del path (si existe).
		if (substr($path,0,1)=='/'){$path=substr($path,1);}
		if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
		//para mantener el id de la session
		if (!empty($form)) {
			$location="$path/$pagina";
		} else {
			if (strstr($pagina,"?")){
				$pagina.='&PHPSESSID='.session_id();
			} else {
				$pagina.='?PHPSESSID='.session_id();
			}
			$location="'http:".core\ConfigGlobal::getWeb()."/".$path."/".$pagina."'";
		}
		return $location;
	} else {
		$pagina=urldecode($go_to);
		$pagina=str_replace (core\ConfigGlobal::getWeb(),"",$pagina); //si la dirección ya es absoluta, la quito
		$dire=getcwd();
		$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
		if (substr($path,-1)!='/'){$path.='/';} // me aseguro de que acabe en "/"
		$pagina=str_replace ($path,"",$pagina); //si la dirección ya es absoluta, la quito
		//echo "directorio: $dire, path: $path<br>";
		//echo "pagina: $pagina<br>";
		if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
		if (substr($pagina,0,2)=='./'){$pagina=substr($pagina,1);}
		if (substr($pagina,0,3)=='../') { //quito un directorio de $path
			$path = preg_replace('/\w+\/?$/', '', $path);
			$pagina=substr($pagina,3);
		}
		//echo "pagina2: $pagina<br>";
		// separo la url de los parametros
		if ($p=strpos($pagina,'?') ) { //"%3F" es "?" cuando está encode) 
			$pag=substr($pagina,0,$p);
			// no arrastro el goto, no sé porque estba aqui.
			$parametros=substr($pagina,$p+1);
			//$parametros=substr($pagina,$p+1)."&go_to=$go_to";
			//echo "param: $parametros<br>";
		} else {
			$pag=$pagina;
			//$parametros="go_to=$go_to";
		}
		$posi=strpos($parametros,"condicion=");
		if ($posi===false) {
			//$cond=substr($parametros,$posi);
		} else {
			$cond1=substr($parametros,$posi+10);
			//para asegurar que no tiene barras \
			$cond1=stripslashes($cond1);	
			//echo "cond1: $cond1<br>";
			$cond2=urlencode($cond1);
			//echo "cond2: $cond2<br>";
			$parametros=str_replace($cond1,$cond2,$parametros);
		}
		$url=core\ConfigGlobal::getWeb().$path.$pag;
		return "'".$url."?".$parametros."'";
	}
}
/**
*
* Esta función sirve para ir a una página. Típico al acabar un procedimiento.
*
* versión Ajax.
*
* la variable $go_to puede contener sólo el nombre de la página o también el <div> 
* donde se quiere la página. (separado por un '|'): pagina|div. ¡OJO!, cuando se pasa
* una consulta con concatenaciones (||) es un lio. 
*
* busca la página en el directorio actual. Para usar una referencia absoluta a una página,
* el $go_to deberia empezar por '#'.
*/
function ir_a($go_to) {
	$url='';
	$parametros='';
	$frame='';
	$go=strtok($go_to,"@");
	if ($go=="session" && isset($_SESSION['session_go_to']) ) {
		$g=strtok("@");

		empty($_SESSION['session_go_to'][$g]['pag'])? $pagina='' : $pagina=$_SESSION['session_go_to'][$g]['pag'];
		empty($_SESSION['session_go_to'][$g]['dir_pag'])? $dir_pag='' : $dir_pag=$_SESSION['session_go_to'][$g]['dir_pag'];
		empty($_SESSION['session_go_to'][$g]['target'])? $frame ='' : $frame = $_SESSION['session_go_to'][$g]['target'];
		// separo la url de los parametros
		if ($p=strpos($pagina,"?") || $p=strpos($pagina,"%3F") ) { //"%3F" es "?" cuando está encode) 
			$pagina=substr($pagina,0,$p);
			$parametros=substr($pagina,$p+1)."&go_to=$go_to";
		} else {
			$parametros="go_to=$go_to";
		}
		if (!empty($dir_pag)) {$dire=$dir_pag; } else {$dire=system("pwd"); }
		$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
		if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
		
		$url=core\ConfigGlobal::getWeb().$path."/".$pagina;
		
		// mas parámetros que pueden estar registrados en la session:
		foreach($_SESSION['session_go_to'][$g] as $clave => $valor) {
			if ($clave!="dir_pag" && $clave!="pag" && $clave!="target" ) {
				$parametros.="&$clave=$valor";
			}
		}

	} else {
		$pag_sin_param = '';
		$pagina=urldecode($go_to);
		$pagina=strtok($pagina,"|");
		$frame = strtok("|");
		//echo "frame: $frame<br>";

		$_error_txt = "pagina1: $pagina<br>";
		// separo la url de los parametros
		$p=strpos($pagina,'?');
		if ($p !== false ) {
			$pag_sin_param=substr($pagina,0,$p);
			$parametros=substr($pagina,$p+1);
			$_error_txt .= "pag sin param: $pag_sin_param<br>";
			$_error_txt .= "param: $parametros<br>";
		} else {
			$pag_sin_param=$pagina;
		}
		$posi=strpos($parametros,"condicion=");
		if ($posi===false) {
			//$cond=substr($parametros,$posi);
		} else {
			$cond1=substr($parametros,$posi+10);
			//para asegurar que no tiene barras \
			$cond1=stripslashes($cond1);	
			$_error_txt .= "cond1: $cond1<br>";
			$cond2=urlencode($cond1);
			$_error_txt .= "cond2: $cond2<br>";
			$parametros=str_replace($cond1,$cond2,$parametros);
		}
		
		$pagina=$pag_sin_param; // quito la doble barra
		if (strpos($pagina,core\ConfigGlobal::getWeb()) !== false ) { // Si es una referencia absoluta
			$url=$pagina;
		} else {
			$_error_txt .= "pagina2: $pagina<br>";
			$dire=getcwd();
			$_error_txt .= "dire: $dire<br>";
			$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
			$_error_txt .= "path: $path<br>";
			if (substr($path,-1)!='/'){$path.='/';} // me aseguro de que acabe en "/"
			$_error_txt .= "path2: $path<br>";
			$pagina=str_replace ($path,"",$pagina); //si la dirección ya es absoluta, la quito
			$_error_txt .= "pagina3: $pagina<br>";
			//echo "directorio: $dire, path: $path<br>";
			//echo "pagina: $pagina<br>";
			if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
			if (substr($pagina,0,2)=='./'){$pagina=substr($pagina,1);}
			if (substr($pagina,0,3)=='../') { //quito un directorio de $path
				$path = preg_replace('/\w+\/?$/', '', $path);
				$pagina=substr($pagina,3);
			}
			$_error_txt .= "pagina4: $pagina<br>";
			//echo "pagina2: $pagina<br>";
			$url=core\ConfigGlobal::getWeb().$path.$pagina;
		}
		/*
		if (core\ConfigGlobal::mi_id_usuario() == 443) {
			echo "hola dani<br>";
			echo $_error_txt;
		}
		*/
	}
	if (empty($frame)) $frame="main";

	// passarlo a array para usar la funcion add_hash
	$aParam = array();
	foreach (explode('&',$parametros) as $param) {
		$aa = explode('=',$param);
		$aParam[$aa[0]] = empty($aa[1])? '' : $aa[1];
	}
	$parametros = Hash::add_hash($aParam,$url);
	?>
	<div id="ir_a">
		<form id="go">
		url: <input id="url" type="text" value="<?= $url ?>" size=70><br>
		parametros: <input id="parametros" type="text" value="<?= $parametros ?>" size=70><br>
		bloque: <input id="id_div" type="text" value="<?= $frame ?>" size=70>
		</form>
	</div>
	<?php
}
/**
* 
*    Versión Ajax
*
* Es como la función ir_a, pero los parametros de la url se pasan como pares
* clave-valor de un vector. El primer par es el nombre de la pagina. El último debe ser el
* de la sessión de la página.
*
*/
function go_array($gg) {
	reset ($gg);
	$parametros = '';
	$url = '';
	$frame = '';
	while (list ($clave, $val) = each ($gg)) {
		switch ($clave) {
			case "pagina":
				$url = $val;
				break;
			case "frame":
				$frame=$val;
				break;
			default:
				$parametros .= $clave ."=". urlencode($val)."&";
		}
	}
	?>
	<div id="ir_a">
		<form id="go">
		url: <input id="url" type="text" value="<?= $url ?>" size=70><br>
		parametros: <input id="parametros" type="text" value="<?= $parametros ?>" size=70><br>
		bloque: <input id="id_div" type="text" value="<?= $frame ?>" size=70>
		</form>
	</div>
	<?php
}
/**
*
* Esta función sirve para ir a una página. Típico al acabar un procedimiento.
*
* la variable $go_to puede contener sólo el nombre de la página o también el frame 
* donde se quiere la página. (separado por un '|'): pagina|frame. ¡OJO!, cuando se pasa
* una consulta con concatenaciones (||) es un lio. 
*
* busca la página en el directorio actual. Para usar una referencia absoluta a una página,
* el $go_to deberia empezar por '#'.
*/
function ir_a_sin_ajax($go_to) {
$go=strtok($go_to,"@");
if ($go=="session" && isset($SESSION['session_go_to']) ) {
	$g=strtok("@");
	if (strstr($_SESSION['session_go_to'][$g]['pag'],"?")) {
		$pagina=$_SESSION['session_go_to'][$g]['pag']."&go_to=$go_to";
	} else {
		$pagina=$_SESSION['session_go_to'][$g]['pag']."?go_to=$go_to";
	}
	$dir_pag=$_SESSION['session_go_to'][$g]['dir_pag'];
	if (!empty($dir_pag)) {$dire=$dir_pag; } else {$dire=system("pwd"); }
	$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
	if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
	$frame = $_SESSION['session_go_to'][$g]['target'];
	
	$loc_1=$frame.".location.href=";
	$loc_2=core\ConfigGlobal::getWeb().$path."/".$pagina;

} else {
	$pagina=urldecode($go_to);
	$pagina=strtok($pagina,"|");
	$frame = strtok("|");
	//echo "frame: $frame<br>";
	$pagina=str_replace (core\ConfigGlobal::getWeb(),"",$pagina); //si la dirección ya es absoluta, la quito
	$dire=system("pwd");
	$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
	$pagina=str_replace ($path,"",$pagina); //si la dirección ya es absoluta, la quito
	//echo "directorio: $dire, path: $path<br>";
	echo "pagina: $pagina<br>";
	if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
	//vuelve a la presentacion de la ficha.
		if (substr($pagina,0,1)=='/'){$pagina=substr($pagina,1);}
		if (empty($frame)) {$frame="window"; }
		
		$posi=strpos($pagina,"condicion=");
		if ($posi===false) {
			//$cond=substr($pagina,$posi);
		} else {
			$cond1=substr($pagina,$posi+10);
			//para asegurar que no tiene barras \
			$cond1=stripslashes($cond1);	
			//echo "cond1: $cond1<br>";
			$cond2=urlencode($cond1);
			//echo "cond2: $cond2<br>";
			$pagina=str_replace($cond1,$cond2,$pagina);
		}
		$location=$frame.".location.href='".core\ConfigGlobal::getWeb().$path."/".$pagina;
		$loc_1=$frame.".location.href=";
		$loc_2=core\ConfigGlobal::getWeb().$path."/".$pagina;
	}
	//para mantener el id de la session
	if (strstr($loc_2,"?")) { 
		$loc_2.= "&PHPSESSID=".session_id();
	} else {
		if (strstr($loc_2,"%3F")) { //"%3F" es "?" cuando está encode
			$loc_2 .= urlencode("&PHPSESSID=") .session_id();
		} else {
			$loc_2 .= "?PHPSESSID=".session_id(); //?
		} 
	}
	?>
	<script LANGUAGE="JavaScript">
		function go_now () {
		    <?php echo $loc_1; ?>"http:<?php echo $loc_2; ?>";
		}
	</script>
	<body onload=go_now() >
		algo
	</body>
	<?php
}


?>
