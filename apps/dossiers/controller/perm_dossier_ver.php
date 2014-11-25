<?php
use dossiers\model as dossiers;
/**
* Página de visualización de los permisos de los dossiers
* Le llegan las variables $tipo y $id_tipo
*
* Tiene include de ficha.php la cual permite guardar en la tabla 
* d_tipos_dossiers 
*
*@package	delegacion
*@subpackage	system
*@author	Josep Companys
*@since		15/5/02.
*		
*/
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************


function cuadros_perm_doss($nomcamp,$dec){
	/*
	Es para dibujar los cuadros de chekcbox para generar 
	los permisos de dossiers. En $nomcamp esta el nombre del campo
	En $dec está un valor en decimal.
	*/

	$camp=$nomcamp."[]";

	if (empty($dec)) { $dec=0; }
	//for1
	for ($i=0;$i<14;$i++) {
		switch ($i) {
				case "1":
					if ($dec & 4096) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"4096\" $chk>"._("dtor");
					break;
				case "2":
					if ($dec & 2048) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"2048\" $chk>"._("ocs");
					break;
				case "3":
					if ($dec & 1024) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"1024\" $chk>"._("ss");
					break;
				case "4":
					if ($dec & 512) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"512\" $chk>"._("sr");
					break;
				case "5":
					if ($dec & 256) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"256\" $chk>"._("soi");
					break;
				case "6":
					if ($dec & 128) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"128\" $chk>"._("sm");
					break;
				case "7":
					if ($dec & 64) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"64\" $chk>"._("sg");
					break;
				case "8":
					if ($dec & 32) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"32\" $chk>"._("scl");
					break;
				case "9":
					if ($dec & 16) {$chk="checked";} else {$chk="";}
					echo "   <input type=\"Checkbox\" name=\"$camp\" value=\"16\" $chk>"._("est");
					break;
				case "10":
					if ($dec & 8) {$chk="checked";} else {$chk="";}
					echo "<input type=\"Checkbox\" name=\"$camp\" value=\"8\" $chk>"._("des");
					break;
				case "11":
					if ($dec & 4) {$chk="checked";} else {$chk="";}
					echo "<input type=\"Checkbox\" name=\"$camp\" value=\"4\" $chk>"._("aop");
					break;
				case "12":
					if ($dec & 2) {$chk="checked";} else {$chk="";}
					echo "<input type=\"Checkbox\" name=\"$camp\" value=\"2\" $chk>"._("agd");
					break;
				case "13":
					if ($dec & 1) {$chk="checked";} else {$chk="";}
					echo "<input type=\"Checkbox\" name=\"$camp\" value=\"1\" $chk>"._("adl");
					break;
		
		}//fin switch
	}//fin for1
}//fin funcion

?>
<script>
fnjs_eliminar=function(){
   $('#que').val('eliminar');
   $('#frm2').attr('action','apps/dossiers/controller/perm_dossier_update.php');
   fnjs_enviar_formulario('#frm2');
}
fnjs_guardar=function(){
   $('#que').val('guardar');
   $('#frm2').attr('action','apps/dossiers/controller/perm_dossier_update.php');
   fnjs_enviar_formulario('#frm2');
}
</script>
<?php
$oTipoDossier = new dossiers\TipoDossier(array('id_tipo_dossier'=>$_POST['id_tipo_dossier']));
$a_campos = $oTipoDossier->getTot();
$botones = 0;
/*
1: guardar cambios
2: eliminar
*/
if ($_SESSION['oPerm']->have_perm("admin_sv") OR $_SESSION['oPerm']->have_perm("admin_sf")) { 
	$botones="1,2";
}
$a_campos['botones'] = $botones;

$oView = new core\View('dossiers\controller');
echo $oView->render('perm_dossier_pres.phtml',$a_campos);
?>
