<?php
use ubis\model as ubis;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
	//require_once ("classes/personas/aux_menus_gestor.class");
	//require_once ("classes/personas/ext_aux_menus_ext_gestor.class");
	//require_once ("classes/web/desplegable.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oGesReg = new ubis\GestorRegion();
$oDesplRegiones = $oGesReg->getListaRegiones();
$oDesplRegiones->setNombre('region');

$oGesDl = new ubis\GestorDelegacion();
$oDesplDelegaciones = $oGesDl->getListaDelegaciones();
$oDesplDelegaciones->setNombre('dl');
//$oDesplDelegacionesOrg->setOpcion_sel($dl);


?>
<script>
fnjs_db_crear=function(){
	$('#frm').attr('action','apps/devel/controller/db_crear_esquema.php');
	fnjs_enviar_formulario('#frm','#main');
}
fnjs_db_copiar=function(){
	$('#frm').attr('action','apps/devel/controller/db_copiar.php');
	fnjs_enviar_formulario('#frm','#main');
}
fnjs_db_eliminar=function(){
	$('#frm').attr('action','apps/devel/controller/db_eliminar.php');
	fnjs_enviar_formulario('#frm','#main');
}

</script>
<h1>Nuevas dl</h1>
<form id="frm" action="" method=post>
<table>
 <tr valign=top align=left>
  <td><?= _("región") ?>:</td>
  <td><?= $oDesplRegiones->desplegable() ?>
  </td>
  <td><?= _("Delegación") ?>:</td>
  <td><?= $oDesplDelegaciones->desplegable() ?>
  </td>
 </tr>
 <tr>
 <td><?= _("sv") ?>:</td>
  <td><input type="checkbox" value="1" checked name="sv">
  </td>
  <td><?= _("sf") ?>:</td>
  <td><input type="checkbox" value="1" checked name="sf">
  </td>
 </tr>
 </table>
 <table>
 <tr>
  <td align=right><input type="button" name="bcrear" onclick="fnjs_db_crear()" value="<?= ("crear Esquema") ?>"></td>
  <td align=right><input type="button" name="bimportar" onclick="fnjs_db_copiar()" value="<?= ("importar datos de resto") ?>"></td>
  <td align=right><input type="button" name="beliminar" onclick="fnjs_db_eliminar()" value="<?= ("passar datos a resto y eliminar esquema") ?>"></td>
 </tr>
</table>
