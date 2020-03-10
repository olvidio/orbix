<?php
/**
* Esta página actualiza la base de datos de los encargos.
*
* Se le puede pasar la varaible $mod.
*	Si es 1 >> inserta un nuevo encargo.
*	Si es 2 >> sólo cambia el tipo de encargo. Antes utiliza la función comprobar_cambio_tipo($id_activ,$valor)
* que está en func_tablas.
*   Si es 3 >> elimina.
* Existe otra variable: $que. Si vale "actualizar", el $go_to se cambia para volver a ver_ficha_cos.
*
*@package	delegacion
*@subpackage	encargos
*@author	Daniel Serrabou
*@since		3/1/06.
*		
*/

/**
* Funciones más comunes de la aplicación
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/*
include_once(ConfigGlobal::$dir_programas.'/func_web.php'); 


//echo "nn: $mod, $mas_menos<br>";
if (empty($_POST['mas_menos'])) $_POST['dia_ref']=$_POST['dia'];

switch ($_POST['mod']) {
case "1": //nuevo
	$mod=0;
	if (!empty($_POST['id_enc'])) {
	    empty($_POST['id_tipo_enc'])? $valor="" : $valor=$_POST['id_tipo_enc'];
	} else {
	    echo _("Error")."<br>";
	    exit(0);
	}
		
	//Compruebo que estén todos los campos necesasrios
	if (!$_POST['f_ini'] || !$_POST['dia']) {
		echo _("Debe llenar todos los campos que tengan un (*)")."<br>";
		exit;
	}
	
	$insert = "id_nom,id_enc,f_ini,f_fin,dia_ref,dia_num,mas_menos,dia_inc,h_ini,h_fin";
	$values = "'".$_POST['id_nom']','$_POST['id_enc']','$_POST['f_ini']','$_POST['f_fin']','$_POST['dia_ref']','$_POST['dia_num']','$_POST['mas_menos']','$_POST['dia_inc']','$_POST['h_ini']','$_POST['h_fin']."'";

	$values = str_replace("''","NULL",$values);
	
	$sql_insert="INSERT INTO t_horario_sacd (" . $insert . ") VALUES (" . $values . ")";
	$oDBSt_q=$oDB->query($sql_insert);
	//echo "$sql_insert<br>";
	$go_to="sacd_ausencias_get.php?filtro_sacd=".$_POST['filtro_sacd']."&id_nom=$_POST['id_nom']|ficha";
	
	//vuelve a la presentacion de la ficha.
	//echo "go_to: $go_to<br>";
	$r=ir_a($go_to);
	break;
case "2": 
	//Compruebo que estén todos los campos necesasrios
	if (!$_POST['f_ini'] || !$_POST['dia']) {
		echo _("Debe llenar todos los campos que tengan un (*)")."<br>";
		exit;
	}
	
	$update = "f_ini='".$_POST['f_ini']."'";
	$update .= ",f_fin='".$_POST['f_fin']."'";
	$update .= ",dia_ref='".$_POST['dia_ref']."'";
	$update .= ",dia_num='".$_POST['dia_num']."'";
	$update .= ",mas_menos='".$_POST['mas_menos']."'";
	$update .= ",dia_inc='".$_POST['dia_inc']."'";
	$update .= ",h_ini='".$_POST['h_ini']."'";
	$update .= ",h_fin='".$_POST['h_fin']."'";
	
	$update = str_replace("''","NULL",$update);
	
	$sql_update="UPDATE t_horario_sacd SET ".$update." WHERE id_item='".$_POST['id_item']."' ";
	$oDBSt_q=$oDB->query($sql_update);
	echo "$sql_update<br>";

	$go_to="sacd_ausencias_get.php?filtro_sacd=".$_POST['filtro_sacd']."&id_nom=$_POST['id_nom']|ficha";
	
	//vuelve a la presentacion de la ficha.
	//echo "go_to: $go_to<br>";
	$r=ir_a($go_to);
	break;

    
case "eliminar":
	if (!empty($_POST['id_item'])) {
	    $sql="DELETE FROM t_horario_sacd WHERE id_item=".$_POST['id_item']." ;";
	    // también elimino las excepciones:
	    $sql.="DELETE FROM t_horario_sacd_excepcion WHERE id_item_h=".$_POST['id_item']."";
	    //echo "sql: $sql<br>";
	    $oDBSt_q=$oDB->query($sql);
	}
	$go_to="sacd_ausencias_get.php?filtro_sacd=".$_POST['filtro_sacd']."&id_nom=$_POST['id_nom']|ficha";
	
	//vuelve a la presentacion de la ficha.
	//echo "go_to: $go_to<br>";
	$r=ir_a($go_to);
	break;
} // fin del switch de mod.
*/