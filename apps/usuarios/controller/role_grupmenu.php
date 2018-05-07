<?php
use usuarios\model\entity as usuarios;
use menus\model\entity as menus;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

if (isset($_POST['sel'])) { //vengo de un checkbox
	//$id_nom=$sel[0];
	$id_role=strtok($_POST['sel'][0],"#");
} else {
	empty($_POST['id_role'])? $id_role="" : $id_role=$_POST['id_role'];
}

$oRole = new usuarios\Role(array('id_role'=>$id_role));
$role=$oRole->getRole();
/*
$sf=$oRole->getSf();
$sv=$oRole->getSv();
$sfsv = '';
if ($sv == 1) $sfsv = 1;
if ($sf == 1) $sfsv = 2;
$aWhere = array('sfsv'=>$sfsv);
if ($sv == 1 & $sf == 1) $aWhere = array();
*/

$go_to= web\Hash::link('apps/usuarios/controller/role_form.php?'.http_build_query(array('id_role'=>$id_role)));


// los que ya tengo:
$oGesGMRol = new menus\GestorGrupMenuRole();
$cGMR = $oGesGMRol->getGrupMenuRoles(array('id_role'=>$id_role));
$aGrupMenus = array();
foreach ($cGMR as $oGrupMenuRole) {
	$id_grupmenu = $oGrupMenuRole->getId_grupmenu();
	$aGrupMenus[$id_grupmenu] = 'x';
}

$oGesGM = new menus\GestorGrupMenu();
$cGM = $oGesGM->getGrupMenus();
$a_valores=array();
$i = 0;
foreach ($cGM as $oGrupMenu) {
	$i++;
	$id_grupmenu=$oGrupMenu->getId_grupmenu();
	// que no lo tenga
	if (array_key_exists($id_grupmenu,$aGrupMenus)) continue;

	$grup_menu=$oGrupMenu->getGrup_menu();

	$a_valores[$i]['sel']="$id_role#$id_grupmenu";
	$a_valores[$i][1]=$grup_menu;
}

$a_cabeceras=array('grupmenu');
$a_botones[]=array( 'txt'=> _('añadir'), 'click'=>"fnjs_add_grupmenu(\"#from_grupmenu\")");
$oTabla = new web\Lista();
$oTabla->setId_tabla('grupmenu');
$oTabla->setCabeceras($a_cabeceras);
$oTabla->setBotones($a_botones);
$oTabla->setDatos($a_valores);


$oHash = new web\Hash();
$oHash->setcamposForm('sel');
$oHash->setcamposNo('scroll_id');
$a_camposHidden = array(
		'id_role' => $id_role,
		'go_to' => $go_to,
		'que' => 'add_grupmenu'
		);
$oHash->setArraycamposHidden($a_camposHidden);

?>
<script>
fnjs_add_grupmenu=function(formulario){
	go='<?= $go_to ?>';
	$(formulario).attr('action',"apps/usuarios/controller/role_update.php");
	$(formulario).submit(function() {
		$.ajax({
			data: $(this).serialize(),
			type: 'post',
			url: $(this).attr('action'),
			complete: function (rta) { 
				rta_txt=rta.responseText;
				if (rta_txt.search('id="ir_a"') != -1) {
					fnjs_mostra_resposta(rta,'#main'); 
				} else {
					if (go) fnjs_update_div('#main',go); 
				}
			}
		});
		return false;
	});
	$(formulario).submit();
	$(formulario).off();
}
</script>
<h1><?= sprintf(_("Añadir nuevo grupMenu a %s"),$role) ?></h1>
<form id=from_grupmenu  name=from_grupmenu action="" method="post" >
<?= $oHash->getCamposHtml(); ?>
<h4><?= ucfirst(_("grupos de menús")) ?>:</h4>
<?php
echo $oTabla->mostrar_tabla();
?>
</form>
