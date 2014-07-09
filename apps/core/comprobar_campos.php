<?php
namespace core;
/**
* Esta página sirve para comprobar que los valores de los campos de
* un formulario, se corresponden con el tipo de datos de la base de datos.
*
* Se le debe pasar el parametro 'tabla'
*
*@package	delegacion
*@subpackage	actividades
*@author	Daniel Serrabou
*@since		17/9/2010.
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


$oDbl = $oDB;
$tabla = empty($_POST['cc_tabla'])? '' : $_POST['cc_tabla'];


if (!empty($_POST['cc_obj'])) {
	$Object = new $_POST['cc_obj'];
	$oDbl = $Object->getoDbl();
	$tabla = $Object->getNomTabla();
}


$sql="SELECT 
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
			ORDER BY a.attnum
";
$error="";
foreach($oDbl->query($sql) as $row) {
	$nomcamp=$row['field'];
	if (($nomcamp == 'id_nom' || $nomcamp == 'id_activ' || $nomcamp == 'id_ubi') && $_POST['cc_pau'] == 1) {
		$nomcamp='id_pau';
	}
	// caso especial.
	if ($nomcamp == 'sfsv') {
		$nomcamp='isfsv';
	}

	$tipo=$row['type'];	
	$not_null=$row['notnull'];
	$attnum=$row['attnum'];
	//print_r($_POST);
	// lo tengo en el POST?
	$nomcamp_post=$nomcamp;
	// No compruebo los que son array (tipo_labor...)
	if (array_key_exists($nomcamp_post,$_POST) && !is_array($_POST[$nomcamp_post])) {
		$valor=trim($_POST[$nomcamp_post]);
		if (!empty($valor)) {
			$vacio=0;
			switch($tipo) {
				case 'float4':
				case 'double':
					if (!is_numeric($valor)) {
					   $errores[]=array('txt'=>_('El campo "%1$s" debe ser un número real, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
					}
					break;
				case 'numeric':
					if (!is_numeric($valor)) {
					   $errores[]=array('txt'=>_('El campo "%1$s" debe ser un número, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
					}
					break;
				case 'int4':
				case 'int2':
					//$valor=(int)$valor;
					if (is_numeric($valor)) {
						if ((int)$valor != $valor) {
						   $errores[]=array('txt'=>_('El campo "%1$s" debe ser un número entero, y es: "%2$s"'),
									   'camp'=>$nomcamp,
									   'val'=>array($valor));
						}
					} else {
					   $errores[]=array('txt'=>_('El campo "%1$s" debe ser un número entero, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
					}
					break;
				case 'text':
					if (!is_string($valor)) {
					   $errores[]=array('txt'=>_('El campo "%1$s" debe ser un texto, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
					}
					break;
				case 'varchar':
					if (!is_string($valor)) {
					   $errores[]=array('txt'=>_('El campo "%1$s" debe ser un texto, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
					} else {
						if (strlen($valor) > ($row['lengthvar']-4)) {
						   $errores[]=array('txt'=>_('El campo "%1$s" sólo puede tener %2$d cararcteres y tiene %3$d'),
								   'camp'=>$nomcamp,
								   'val'=> array(($row['lengthvar']-4),strlen($valor))
								   );
						}
					}
					break;
				case 'date':
					if (preg_match ("/^([0-9]{1,2})[\/-]([0-9]{1,2})[\/-]([0-9]{2,4})+$/", $valor, $parts)==1) {
						//check weather the date is valid of not
						if(checkdate($parts[2],$parts[1],$parts[3])) {
						} else {
						   $errores[]=array('txt'=>_('El campo "%1$s" debe ser una fecha, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
						}
					} else {
					   $errores[]=array('txt'=>_('El campo "%1$s" debe ser una fecha, y es: "%2$s"'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
					}
					break;
				case 'time':
					$err=0;
					if (!empty($valor)) {
						if (preg_match ("/^([0-9]{2}):([0-9]{2})(:([0-9]{2}))?$/", $valor, $parts)==1) {
							if ($parts[1]>24) $err=1;
							if ($parts[2]>60) $err=1;
							if (!empty($parts[4]) && $parts[4]>60) $err=1;
							if ($err==1) {
							   $errores[]=array('txt'=>_('El campo "%1$s" debe ser una hora. Debe tener el formato hh:mm:ss. [%2$s]'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
							}
						} else {
						   $errores[]=array('txt'=>_('El campo "%1$s" debe ser una hora. Debe tener el formato hh:mm:ss. [%2$s]'),
								   'camp'=>$nomcamp,
								   'val'=>array($valor));
						}
					}
					break;
			}
		} else {
			$vacio=1;
		}
	} else {
		$vacio= ($tipo=='bool')? 0 : 1; // en el caso de checkbox entiendo que no valor = false.
	}
	if ($vacio==1) {
		// Es necesario?
		if ($not_null) {
			//tiene un valor por defecto?
			$sql_get_default = "
					SELECT d.adsrc AS rowdefault
					FROM pg_attrdef d, pg_class c 
					WHERE 
						c.relname = '$tabla' AND 
						c.oid = d.adrelid AND
						d.adnum =$attnum";
			//echo "sql_def: $sql_get_default<br>";
			$default=$oDbl->query($sql_get_default)->fetchColumn();
			if (empty($default)) {
			   $errores[]=array('txt'=>_('El campo "%1$s" no puede estar vacío'),
				   'camp'=>$nomcamp,
				   'val'=>array());
			}
		}
	}
}
/*
*  En caso de error busco la etiqueta del campo (si la hay) para hacer más
*  entendible el mensaje
*/
if (!empty($errores)) {
	// para las etiquetas de los campos:
	$aux_help_campos="aux_help_campos";

	$idioma_2=substr(ConfigGlobal::mi_idioma(),0,2);
	if (!empty($idioma_2) && $idioma_2 != 'es') {
		$aux_help_campos='aux_help_campos_'.$idioma_2;
		$sql_trad="select count(*) from pg_tables where tablename='$aux_help_campos'";
		$oDBSt_trad=$oDbl->query($sql_trad);
		if (!$oDBSt_trad->rowCount()) {
			$aux_help_campos="aux_help_campos";
		}
	}
	$error_txt="";
	foreach($errores as $error) {
		$txt=$error['txt'];
		$camp=$error['camp'];
		$valores=$error['val'];
		$help="SELECT h.etiqueta
			FROM aux_campos_comunes c, $aux_help_campos h
			WHERE c.new_tabla=h.new_tabla 
				AND c.campo=h.nombre_campo
				AND c.campo='$camp'
				AND c.tabla='$tabla'
			";
		if ($oDbl->query($help) === false ) {
			$etiqueta='';
		} else {
			$etiqueta=$oDbl->query($help)->fetchColumn();
		}
		$nomcampo=empty($etiqueta)? $camp : $etiqueta;

		array_unshift($valores,$nomcampo);
		$error_txt.=vsprintf($txt,$valores)."\n";
	}
	echo trim($error_txt);
}
?>
