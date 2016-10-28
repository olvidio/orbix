<?php
use usuarios\model as usuarios;
use menus\model as menus;
/**
* En el fichero config tenemos las variables genéricas del sistema
*/
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************
//	require_once ("classes/personas/ext_web_preferencias_gestor.class");

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************
$oGesPref = new usuarios\GestorPreferencia();

$id_usuario= core\ConfigGlobal::mi_id_usuario();
$id_role= core\ConfigGlobal::mi_id_role();
// ----------- Página de inicio -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario,'tipo'=>'inicio'));
if (is_array($aPref) && count($aPref) > 0) {
	$oPreferencia = $aPref[0];
	$preferencia = $oPreferencia->getPreferencia();
	list($inicio,$oficina) = preg_split('/#/',$preferencia);
} else {
	$inicio='';
	$oficina='';
}
/*
// si no tiene, grabo una prefernecia por defecto.
if (empty($inicio)) {
	$inicio="correo";
	$query_inicio = sprintf( "INSERT INTO web_preferencias (username,tipo,preferencia) VALUES ('%s','%s','%s') ",core\ConfigGlobal::mi_usuario(),"inicio",$inicio);
	$oDBSt_q=$oDB->query($query_inicio);
}
*/
$ini_exterior = "";
$ini_oficina=($inicio=="oficina")? "selected":'';
$ini_personal=($inicio=="personal")? "selected":'';
$ini_avisos=($inicio=="avisos")? "selected":'';
$ini_aniv=($inicio=="aniversarios")? "selected":'';

//oficinas posibles:

$GesGMR = new menus\GestorGrupMenuRole();
$cGMR = $GesGMR->getGrupMenuRoles(array('id_role'=>$id_role));
$mi_oficina_menu=$cGMR[0]->getId_grupmenu();
$posibles = '';
foreach ($cGMR as $oGMR) {
	$id_grupmenu=$oGMR->getId_grupmenu();
	$oGrupMenu = new menus\GrupMenu($id_grupmenu);
	$grup_menu = $oGrupMenu->getGrup_menu();

	if ($id_grupmenu==$oficina) { $sel="selected"; } else { $sel=""; }
	$posibles.="<option value=$id_grupmenu $sel>$grup_menu</option>";
}


// ----------- Página de estilo -------------------
$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario,'tipo'=>'estilo'));
if (is_array($aPref) && count($aPref) > 0) {
	$oPreferencia = $aPref[0];
	$preferencia = $oPreferencia->getPreferencia();
	list($estilo_color,$tipo_menu) = preg_split('/#/',$preferencia);
} else {
	$estilo_color='';
	$tipo_menu='';
}

// color
$estil_azul=($estilo_color=="azul")? "selected":'';
$estil_naranja=($estilo_color=="naranja")? "selected":'';
$estil_verde=($estilo_color=="verde")? "selected":'';

// disposición:
$tipo_menu_h=($tipo_menu=="horizontal")? "selected":'';
$tipo_menu_v=($tipo_menu=="vertical")? "selected":'';



// ----------- Tipo de tablas -------------------
$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'tabla_presentacion'));
$tipo_tabla=$oPref->getPreferencia();
$tipo_tabla_s=($tipo_tabla=="slickgrid")? "selected":'';
$tipo_tabla_h=($tipo_tabla=="html")? "selected":'';

// ----------- Orden Apellidos en listas -------------------
$oPref = new usuarios\Preferencia(array('id_usuario'=>$id_usuario,'tipo'=>'ordenApellidos'));
$tipo_apellidos=$oPref->getPreferencia();
$tipo_apellidos_nom_ap=($tipo_apellidos=="nom_ap")? "selected":'';
$tipo_apellidos_ap_nom=($tipo_apellidos=="ap_nom")? "selected":'';

// ----------- Idioma -------------------
//Tengo la variable $idioma en ConfigGlobal, pero vuelvo a consultarla 
$aPref = $oGesPref->getPreferencias(array('id_usuario'=>$id_usuario,'tipo'=>'idioma'));
if (is_array($aPref) && count($aPref) > 0) {
	$oPreferencia = $aPref[0];
	$preferencia = $oPreferencia->getPreferencia();
	list($idioma) = preg_split('/#/',$preferencia);
} else {
	$idioma='';
}
$oGesLocales = new usuarios\GestorLocal();
$oDesplLocales = $oGesLocales->getListaLocales();
$oDesplLocales->setNombre('idioma_nou');
$oDesplLocales->setOpcion_sel($idioma);

/*
// si no tiene, grabo una prefernecia por defecto.
if (empty($idioma)) {
	$idioma="es_ES.UTF-8@euro";
	$query_idioma = sprintf( "INSERT INTO web_preferencias (username,tipo,preferencia) VALUES ('%s','%s','%s') ",core\ConfigGlobal::mi_usuario(),"idioma",$idioma);
	$oDBSt_q=$oDB->query($query_idioma);
}
*/


$aniversarios=web\Hash::link(core\ConfigGlobal::getWeb().'/programas/aniversarios.php');
$avisos=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_form.php?'.http_build_query(array('quien'=>'usuario','id_usuario'=>$id_usuario)));
$cambio_password=web\Hash::link(core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_form_pwd.php');


$oHash = new web\Hash();
$oHash->setcamposForm('inicio!oficina!estilo_color!tipo_menu!tipo_tabla!ordenApellidos!idioma_nou');
?>
<span class="link" onclick="fnjs_update_div('#main_pref','<?= $aniversarios ?>');"><?= _("ver santos y aniversarios") ?></span><br>
<span class="link" onclick="fnjs_update_div('#main_pref','<?= $avisos ?>');"><?= _("gestionar avisos en cambios actividades") ?></span><br>
<h2><?= ucfirst(_("preferencias personales")) ?><br></h2>
<form id=preferencias name=preferencias action="apps/usuarios/controller/personal_update.php" method="post">
<?= $oHash->getCamposHtml(); ?>
<table>
<tr><td><?= ucfirst(_("página central de inicio")) ?>: </td>
<td>
<select name=inicio>
	<option value=exterior <?php echo $ini_exterior; ?>><?= ucfirst(_("home")) ?></option>
	<option value=avisos <?php echo $ini_avisos; ?>><?= ucfirst(_("avisos cambios actividades")) ?></option>
	<option value=oficina <?php echo $ini_oficina; ?>><?= ucfirst(_("oficina")) ?></option>
	<option value=personal <?php echo $ini_personal; ?>><?= ucfirst(_("personal")) ?></option>
	<option value=aniversarios <?php echo $ini_aniv; ?>><?= ucfirst(_("aniversarios")) ?></option>
</select>
</td>
</tr>
<tr>
<td><?= ucfirst(_("menú principal de la oficina")) ?>:</td><td>
<select name=oficina>
<option />
<?php echo $posibles; ?>;
</select>
</td>
</tr>
<tr><td><?= ucfirst(_("estilo")) ?>:</td>
<td>
<select name=estilo_color>
	<option value=azul <?php echo $estil_azul; ?>>Azul</option>
	<option value=naranja <?php echo $estil_naranja; ?>>Naranja</option>
	<option value=verde <?php echo $estil_verde; ?>>verde</option>
</select></td></tr>
<tr><td><?= ucfirst(_("disposición menú")) ?>:</td>
<td>
<select name=tipo_menu>
	<option value=horizontal <?php echo $tipo_menu_h; ?>>Horizontal</option>
	<option value=vertical <?php echo $tipo_menu_v; ?>>Vertical</option>
</select></td></tr>
<tr><td><?= ucfirst(_("presentación tablas")) ?>:</td>
<td>
<select name=tipo_tabla>
	<option value="slickgrid" <?php echo $tipo_tabla_s; ?>>SlickGrid</option>	
	<option value="html" <?php echo $tipo_tabla_h; ?>>Html</option>
</select></td></tr>
<tr><td><?= ucfirst(_("presentación nombre,Apellidos")) ?>:</td>
<td>
<select name=ordenApellidos>
	<option value="ap_nom" <?php echo $tipo_apellidos_ap_nom; ?>>Apellidos, Nombre</option>	
	<option value="nom_ap" <?php echo $tipo_apellidos_nom_ap; ?>>Nombre Apellidos</option>
</select></td></tr>
<tr><td><?= ucfirst(_("idioma")) ?>:</td>
<td>
<?= $oDesplLocales->desplegable() ?>
</td></tr>
</table>
<br>
<input type="button" onclick=fnjs_enviar_formulario('#preferencias') value="guardar preferencias">
</form>
<br><span class="link" onclick="fnjs_update_div('#main','<?= $cambio_password ?>');"><?= _("cambiar el password") ?></span><br>
