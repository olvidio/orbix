<?php
use usuarios\model as usuarios;
use permisos\model as permisos;
use personas\model as personas;
use ubis\model as ubis;
// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************
	$oCuadros=new permisos\PermDl;

// FIN de  Cabecera global de URL de controlador ********************************

echo $oPosicion->atras();

$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv = core\ConfigGlobal::mi_sfsv();

$id_usuario = empty($_POST['id_usuario'])? '' : $_POST['id_usuario'];
$quien = empty($_POST['quien'])? '' : $_POST['quien'];

if( (core\ConfigGlobal::is_app_installed('avisos')) && (!empty($id_usuario)) && ($quien == 'usuario') ) {

	// avisos
	$oGesCambiosUsuariosTabla = new GestorCambioUsuarioTablaPref();
	$cListaTablas = $oGesCambiosUsuariosTabla->getCambiosUsuarioTablaPref(array('id_usuario'=>$id_usuario));

	// Tipos de avisos
	$aTipos_aviso = CambioUsuarioTablaPref::getTipos_aviso();

	$i=0;
	$a_cabeceras_avisos=array('dl propia','tipo de actividad','fase inicial','fase final','objeto','tipo de aviso','campos','valor');
	$a_botones_avisos=array(
				array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#avisos\")" ),
				array( 'txt' => _('eliminar'), 'click' =>"fnjs_borrar(\"#avisos\")" ) 
			);
	$a_valores_avisos=array();
	$oFase = new ActividadFase();
	foreach ($cListaTablas as $oCambioUsuarioTablaPref) {
		$i++;
		
		$id_item_usuario_tabla=$oCambioUsuarioTablaPref->getId_item_usuario_tabla();
		$id_tipo=$oCambioUsuarioTablaPref->getId_tipo_activ_txt();
		$id_fase_ini=$oCambioUsuarioTablaPref->getId_fase_ini();
		$id_fase_fin=$oCambioUsuarioTablaPref->getId_fase_fin();
		$dl_propia=$oCambioUsuarioTablaPref->getDl_propia();
		$tabla_obj=$oCambioUsuarioTablaPref->getTabla_obj();
		$aviso_tipo=$oCambioUsuarioTablaPref->getAviso_tipo();


		if ($dl_propia=='t') { $dl_propia_txt = core\ConfigGlobal::$dele; } else { $dl_propia_txt = _('otras'); }

		$oTipoActividad = new TiposActividades($oCambioUsuarioTablaPref->getId_tipo_activ_txt());

		$a_valores_avisos[$i]['sel']="$id_usuario#$id_item_usuario_tabla";
		$a_valores_avisos[$i][1]=$dl_propia_txt;
		$a_valores_avisos[$i][2]=$oTipoActividad->getNom();
		$oFase->setId_fase($id_fase_ini);
		$oFase->DBCarregar();
		$a_valores_avisos[$i][3]= $oFase->getDesc_fase();
		$oFase->setId_fase($id_fase_fin);
		$oFase->DBCarregar();
		$a_valores_avisos[$i][4]= $oFase->getDesc_fase();
		$a_valores_avisos[$i][5]=$tabla_obj;
		$a_valores_avisos[$i][6]=$aTipos_aviso[$aviso_tipo];
		$GesCambiosUsuarioCamposPref = new GestorCambioUsuarioCampoPref();
		$cListaCampos = $GesCambiosUsuarioCamposPref->getCambiosUsuarioCampoPref(array('id_item_usuario_tabla'=>$id_item_usuario_tabla));
		$txt_cambio = '';
		$c = 0;
		foreach ($cListaCampos as $oCambioUsuarioCampoPref) {
			$c++;
			$campo = $oCambioUsuarioCampoPref->getCampo();
			$operador = $oCambioUsuarioCampoPref->getOperador();
			$valor = $oCambioUsuarioCampoPref->getValor();
			$valor_old = $oCambioUsuarioCampoPref->getValor_old();
			$valor_new = $oCambioUsuarioCampoPref->getValor_new();
			if ($c > 1) $txt_cambio .= ", ";
			$txt_cambio .= $campo;
		}
		$a_valores_avisos[$i][7]=$txt_cambio;
	}
}

// a los usuarios normales (no administrador) sólo dejo ver la parte de los avisos.
if ($miRole < 4) {
	if ($miRole > 3) exit(_('no tiene permisos para ver esto')); // no es administrador
	if ($miRole != 1) { $cond_role="WHERE id_role <> 1 "; } else {$cond_role="WHERE id_role > 0 "; } //absurda cond, pero pero para que no se borre el role del superadmin

	switch($miSfsv) {
		case 1:
			$cond_role.="AND sv='t'";
			break;
		case 2:
			$cond_role.="AND sf='t'";
			break;
	}

	if( !(core\ConfigGlobal::is_app_installed('personas')) ) { $cond_role.="AND (pau != 'sacd' OR pau IS NULL)"; }
	if( !(core\ConfigGlobal::is_app_installed('ubis')) ) { $cond_role.="AND (pau != 'ctr' OR pau != 'cdc' OR pau IS NULL)"; }
			
	$oGRoles = new usuarios\GestorRole();
	$oDesplRoles= $oGRoles->getListaRoles($cond_role);
	$oDesplRoles->setNombre('id_role');

	/*
	if( (core\ConfigGlobal::is_app_installed('procesos')) {
		$oGesFases = new GestorActividadFase();
		$oDesplFases= $oGesFases->getListaActividadFases();
		$oDesplFases->setNombre('fase');
	}
	*/
	switch($quien) {
		case 'usuario':
			$txt_guardar=_("guardar datos usuario");

			if (!empty($id_usuario)) {
				$que_user='guardar';
				$oUsuario = new usuarios\Usuario(array('id_usuario'=>$id_usuario));

				$id_usuario=$oUsuario->getId_usuario();
				$seccion=$miSfsv;
				$usuario=$oUsuario->getUsuario();
				$nom_usuario=$oUsuario->getNom_usuario();
				$pass=$oUsuario->getPassword();
				$email=$oUsuario->getEmail();
				$id_role=$oUsuario->getId_role();
				$oDesplRoles->setOpcion_sel($id_role);
				$oRole = new usuarios\Role($id_role);
				$pau = $oRole->getPau();
				$sv = $oRole->getSv();
				$sf = $oRole->getSf();
				if ($pau == 'cdc') { //casa
					$id_pau=$oUsuario->getId_pau();
					$cond = '';
					switch ($seccion) {
						case 1:
							$cond = "WHERE sv = 't'";
							break;
						case 2:
							$cond = "WHERE sf = 't'";
							break;
					}
					$oGCasas = new ubis\GestorCasaDl();
					$oOpcionesCasas = $oGCasas->getPosiblesCasas($cond);
					//$oDesplCasas = new Desplegable(array('oOpciones'=>$oOpcionesCasas));	

					$oSelects = new web\DesplegableArray($id_pau,$oOpcionesCasas,'casas');
					$oSelects->setBlanco('t');
					$oSelects->setAccionConjunto('fnjs_mas_casas(event)');
					$camposMas = 'casas!casas_mas!casas_num';
				}
				if ($pau == 'ctr' && $sv == 1) { //centroSv
					$id_pau=$oUsuario->getId_pau();
					$oGesCentrosDl = new ubis\GestorCentroDl();
					$oSelects = $oGesCentrosDl->getListaCentros();

					$oSelects->setNombre('id_ctr');
					$oSelects->setOpcion_sel($id_pau);
					$oSelects->setBlanco('t');
					$camposMas = 'id_ctr';
				}
				if ($pau == 'ctr' && $sf == 1) { //centroSf
					$id_pau=$oUsuario->getId_pau();
					$oGesCentrosDl = new GestorCentroSf();
					$oSelects = $oGesCentrosDl->getListaCentros();

					$oSelects->setNombre('id_ctr');
					$oSelects->setOpcion_sel($id_pau);
					$oSelects->setBlanco('t');
					$camposMas = 'id_ctr';
				}
				if ($pau == 'sacd') { //sacd
					$id_pau=$oUsuario->getId_pau();

					$GesPersonas = new personas\GestorPersonaDl();
					$oSelects = $GesPersonas->getListaSacd();

					$oSelects->setNombre('id_sacd');
					$oSelects->setOpcion_sel($id_pau);
					$oSelects->setBlanco('t');
					$camposMas = 'id_sacd';
				}
				if (core\ConfigGlobal::is_app_installed('procesos')) { 
					$oGesPerm = new usuarios\GestorUsuarioPerm();
					$oUsuarioUsuarioPerm = $oGesPerm->getUsuarioPerms(array('id_usuario'=>$id_usuario));
				}
			} else {
				$que_user='nuevo';
				$id_role='';
				$id_usuario='';
				$usuario='';
				$nom_usuario='';
				$pass='';
				$seccion='';
				$email='';
				$role='';
				$pau='';
			}
			//$camposForm = 'que!usuario!nom_usuario!password!email!id_role!id_ctr!id_sacd!casas';
			$camposForm = 'que!usuario!nom_usuario!password!email!id_role';
			$camposForm = !empty($camposMas)? $camposForm.'!'.$camposMas : $camposForm;
			$oHash = new web\Hash();
			$oHash->setcamposForm($camposForm);
			$oHash->setcamposNo('id_ctr!id_sacd!casas');
			$a_camposHidden = array(
					'pass' => $pass,
					'id_usuario' => $id_usuario,
					'quien' => $quien
					);
			$oHash->setArraycamposHidden($a_camposHidden);
			break;
		case 'grupo':
			$txt_guardar=_("guardar datos grupo");
			if (!empty($id_usuario)) {
				$que_user='guardar';
				$oUsuario = new usuarios\Grupo(array('id_usuario'=>$id_usuario));
				$id_usuario=$oUsuario->getId_usuario();
				$id_role=$oUsuario->getId_role();
				$oDesplRoles->setOpcion_sel($id_role);
				$usuario=$oUsuario->getUsuario();
				$oRole = new usuarios\Role($id_role);
				$pau = $oRole->getPau();
				$sv = $oRole->getSv();
				$sf = $oRole->getSf();
				//$nom_usuario=$oUsuario->getNom_usuario();
				$nom_usuario='';
				$seccion=$miSfsv;
				$oGesPermMenu = new usuarios\GestorPermMenu();
				$oGrupoGrupoPermMenu = $oGesPermMenu->getPermMenus(array('id_usuario'=>$id_usuario));
				if (core\ConfigGlobal::is_app_installed('procesos')) { 
					$oGesPerm = new usuarios\GestorUsuarioPerm();
					$oUsuarioUsuarioPerm = $oGesPerm->getUsuarioPerms(array('id_usuario'=>$id_usuario));
				}
				$pass='';
			} else {
				$que_user='nuevo';
				$id_role='';
				$pau='';
				$id_usuario='';
				$usuario='';
				$nom_usuario='';
				$seccion='';
				$pass='';
			}
			$oHash = new web\Hash();
			$oHash->setcamposForm('que!usuario!id_role');
			$oHash->setcamposNo('id_ctr!id_sacd!casas');
			$a_camposHidden = array(
					'pass' => $pass,
					'id_usuario' => $id_usuario,
					'quien' => $quien
					);
			$oHash->setArraycamposHidden($a_camposHidden);
			break;
	}
	

	$url_usuario_ajax = core\ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php';
	$oHash1 = new web\Hash();
	$oHash1->setUrl($url_usuario_ajax);
	$oHash1->setCamposForm('que!id_usuario'); 
	$h1 = $oHash1->linkSinVal();

	?>
	<style type="text/css">
	input {
		border: 1px solid #999;
		margin: 0 5px;
		}
	.password_strength {
		padding: 0 5px;
		display: inline-block;
		}
	.password_strength_1 {
		background-color: #fcb6b1;
		}
	.password_strength_2 {
		background-color: #fccab1;
		}
	.password_strength_3 {
		background-color: #fcfbb1;
		}
	.password_strength_4 {
		background-color: #dafcb1;
		}
	.password_strength_5 {
		background-color: #bcfcb1;
		}
	</style>
	<script type='text/javascript' src='<?php echo core\ConfigGlobal::$web_scripts.'/jquery.password_strength.js'; ?>'></script>
	<script>
	var strong = 0;
	algo=function(level) {
		strong = level;
	}
	var options = {	'texts' : {
			1 : 'Demasiado débil',
			2 : 'password débil',
			3 : 'Normal',
			4 : 'Strong password',
			5 : 'Very strong password'
		},
		'onCheck': algo
	}


	fnjs_actualizar_permisos=function(){
		/* obtener el listado de periodos */
		var url='<?= core\ConfigGlobal::getWeb() ?>/des/activ_sacd_ajax.php';
		var parametros='que=get&id_activ='+id_activ+'&PHPSESSID=<?php echo session_id(); ?>';
			 
		$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			success: function (rta) {
				rta2=jQuery.parseJSON(rta);
				if (rta2.error) { alert("<?php echo _("No es de este año, ni del año pasado"); ?>"); s=1; }
				if (rta2.txt) {
					/* añadir debajo de la actividad */
					var txt_id='#'+id_activ+'_sacds';
					$(txt_id).replace(rta2.txt);
				}
			}
		});
	}

	fnjs_add_grup=function(){
		var url='<?= $url_usuario_ajax ?>';
		var parametros='que=grupo_lst&id_usuario=<?= $id_usuario ?><?= $h1 ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
		$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			success: function (rta_txt) {
				//rta_txt=rta.responseText;
				//alert ('respuesta: '+rta_txt);
				$('#lst_grupos').html(rta_txt);
			}
		});
	}

	fnjs_del_grup=function(){
		var url='<?= $url_usuario_ajax ?>';
		var parametros='que=grupo_del_lst&id_usuario=<?= $id_usuario ?><?= $h1 ?>&PHPSESSID=<?php echo session_id(); ?>';
			 
		$.ajax({
			url: url,
			type: 'post',
			data: parametros,
			success: function (rta_txt) {
				//rta_txt=rta.responseText;
				//alert ('respuesta: '+rta_txt);
				$('#lst_grupos').html(rta_txt);
			}
		});
	}

	fnjs_add_perm=function(que){
		if (que=='menu'){
			$('#frm_usuario').attr('action',"apps/usuarios/controller/usuario_perm_menu.php");
		}
		if (que=='activ'){
			$('#frm_usuario').attr('action',"apps/usuarios/controller/usuario_perm_activ.php");
		}
		fnjs_enviar_formulario('#frm_usuario');
	}
	fnjs_guardar=function(formulario){
		// si es 0, no se cambia el password.
		if (strong != 0 && strong < 5) {
		  alert('Debe poner un password "fuerte" o "muy fuerte"');
		  return false;
		}

		<?php 
		if ($quien=='usuario') echo "tabla = 'aux_usuarios';";
		if ($quien=='grupo') echo "tabla = 'aux_grupos_y_usuarios';";
		?>
		var rr=fnjs_comprobar_campos(formulario,'',0,tabla);
		//alert ("EEE "+rr);
		if (rr=='ok') {
			$('#que_user').val('<?= $que_user ?>');
			go='<?= web\Hash::link('apps/usuarios/controller/usuario_form.php?quien='.$quien.'&id_usuario='.$id_usuario) ?>';
			$(formulario).attr('action',"apps/usuarios/controller/usuario_update.php");
			$(formulario).submit(function() {
				$.ajax({
					data: $(this).serialize(),
					type: 'post',
					url: $(this).attr('action'),
					complete: function (rta) { 
						rta_txt=rta.responseText;
						if (rta_txt != '' && rta_txt != '\n') {
							alert (rta_txt);
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
	}
	<?php
	if ($pau == 'cdc') { //casa
		?>
		fnjs_mas_casas=function(evt){
			if(evt=="x") {
				var valor=1;
			} else {
				var id_campo=evt.currentTarget.id;
				var valor=$(id_campo).val();
				evt.preventDefault();
				evt.stopPropagation();
			}
			if (evt.keyCode==9 || evt.type=="change" || evt=="x") {
				if (valor!=0) {
					<?php
						echo $oSelects->ListaSelectsJs();
					?>
				} else {
					ir_a('f_entrada');
				}
			}
		}
		<?php
		echo $oSelects->ComprobarSelectJs();
	}
	?>
	</script>
	<h3><?= $usuario ?></h3>
	<form id=frm_usuario  name=frm_usuario action='' method="post" >
	<?= $oHash->getCamposHtml(); ?>
	<input type=hidden id=que_user  name=que value=''>
	<br>
	<?= ucfirst(_("nombre")) ?>:<input type=text name=usuario value="<?= $usuario ?>">
	<?php if ($quien == 'usuario') { ?>
	<?= ucfirst(_("nombre a mostrar")) ?>:<input type=text name=nom_usuario value="<?= $nom_usuario ?>">
	<?php } ?>
	<?= ucfirst(_("role")) ?>:
	<?= $oDesplRoles->desplegable(); ?>
	<br>
	<?php 
	if ($quien == 'usuario') {
		if ($pau == 'sacd') {
		?>
			<!--  --------------- Sacd --------------- -->
			<tr>
				<td class=etiqueta><?php echo _("sacd"); ?>:</td>	
			<td colspan=8 id="col_sacd">
			<?php
			echo $oSelects->desplegable();
			echo "</td></tr>";
		}
		if ($pau == 'ctr') {
		?>
			<!--  --------------- CENTROS --------------- -->
			<tr>
				<td class=etiqueta><?php echo _("centro"); ?>:</td>	
			<td colspan=8 id="col_centros">
			<?php
			echo $oSelects->desplegable();
			echo "</td></tr>";
		}
		if ($pau == 'cdc') {
		?>
			<!--  --------------- CASAS --------------- -->
			<tr>
				<td class=etiqueta><?php echo _("casas"); ?>:</td>	
			<td colspan=8 id="col_casas">
			<?php
			echo $oSelects->ListaSelects();
			echo "</td></tr>";
		}
	?>
		<br>
		<?= ucfirst(_("password")) ?>:<input type="password" type="password" name="password"><br>
		<?= ucfirst(_("email")) ?>:<input type=text name=email value="<?= $email ?>"><br>
		<?php
	}
	?>
	<input type=button onclick="fnjs_guardar(this.form);" value="<?= $txt_guardar ?>">

	<br>
	</form>
	<?php
	if (!empty($id_usuario)) { // si no hay usuario, no puedo poner permisos.
		//grupo
		$oGesUsuarioGrupo = new usuarios\GestorUsuarioGrupo();
		$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$id_usuario));
		$i=0;
		$txt='';
		foreach ($oListaGrupos as $oUsuarioGrupo) {
			$i++;
			$oGrupo = new usuarios\Grupo($oUsuarioGrupo->getId_grupo());
			if ($i > 1) $txt.=", ";
			$txt.= $oGrupo->getUsuario();
		}	
		?>
		<br>
		<?php
		if ($quien == 'usuario') {  // de momento no dejo hacer grupos de grupos.
			?>
			<h3><?= _('grupos') ?>: </h3><?= $txt ?>
			<input type=button onclick="fnjs_add_grup();" value='<?= _("añadir un grupo de permisos") ?>'>
			<input type=button onclick="fnjs_del_grup();" value='<?= _("quitar de un grupo de permisos") ?>'>
			<div id=lst_grupos></div>
			<br>
			<?php
		}
		// propios (sólo para los grupos)
		if ($quien == 'grupo') {
			$i=0;
			$a_cabeceras=array(array('name'=>_("oficina o grupo"),'width'=>'350'));
			$a_botones=array(
						array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#permisos_menu\")" ),
						array( 'txt' => _('eliminar'), 'click' =>"fnjs_borrar(\"#permisos_menu\")" ) 
					);
			$a_valores=array();
			foreach ($oGrupoGrupoPermMenu as $oPermMenu) {
				$i++;
				
				$id_item=$oPermMenu->getId_item();
				$menu_perm=$oPermMenu->getMenu_perm();

				$a_valores[$i]['sel']="$id_usuario#$id_item";
				$a_valores[$i][1]=$oCuadros->lista_txt($menu_perm);
			}

			$oHash2 = new web\Hash();
			$oHash2->setcamposForm('que!sel');
			$a_camposHidden = array(
					'id_usuario' => $id_usuario,
					'quien' => $quien
					);
			$oHash2->setArraycamposHidden($a_camposHidden);
			?>
			<h4><?= ucfirst(_("permisos de menús")) ?>:</h4>
			<form id=permisos_menu name=permisos_menu action=''>
			<?= $oHash2->getCamposHtml(); ?>
			<input type=hidden id=que  name=que value=''>
			<?php
			$oTabla = new web\Lista();
			$oTabla->setId_tabla('usuario_form_permisos');
			$oTabla->setCabeceras($a_cabeceras);
			$oTabla->setBotones($a_botones);
			$oTabla->setDatos($a_valores);
			echo $oTabla->mostrar_tabla();
			?>
			<br>
			<input type=button onclick="fnjs_add_perm('menu');" value='<?= _("añadir permiso") ?>'>
			</form>
			<?php
		}
	}
	?>
	<br>
	</form>
	<?php
	if ((core\ConfigGlobal::is_app_installed('procesos')) && !empty($id_usuario)) { // si no hay usuario, no puedo poner permisos.
		//grupo
		$oGesUsuarioGrupo = new usuarios\GestorUsuarioGrupo();
		$oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$id_usuario));
		$i=0;
		$txt='';
		foreach ($oListaGrupos as $oUsuarioGrupo) {
			$i++;
			$oGrupo = new usuarios\Grupo($oUsuarioGrupo->getId_grupo());
			if ($i > 1) $txt.=", ";
			$txt.= $oGrupo->getUsuario();
		}	
		// propios
		$i=0;
		$a_cabeceras=array('tipo de actividad','fase inicial','fase final','permiso','dl propia',array('name'=>'afecta_a','width'=>'350'));
		$a_botones=array(
					array( 'txt' => _('modificar'), 'click' =>"fnjs_modificar(\"#permisos\")" ),
					array( 'txt' => _('eliminar'), 'click' =>"fnjs_borrar(\"#permisos\")" ) 
				);
		$a_valores=array();
		$oFase = new ActividadFase();
		foreach ($oUsuarioUsuarioPerm as $oUsuarioPerm) {
			$i++;
			
			$id_item=$oUsuarioPerm->getId_item();
			$id_tipo=$oUsuarioPerm->getId_tipo_activ_txt();
			$id_fase_ini=$oUsuarioPerm->getId_fase_ini();
			$id_fase_fin=$oUsuarioPerm->getId_fase_fin();
			$id_accion=$oUsuarioPerm->getAccion();
			$dl_propia=$oUsuarioPerm->getDl_propia();

			if ($dl_propia=='t') { $dl_propia_txt = core\ConfigGlobal::$dele; } else { $dl_propia_txt = _('otras'); }

			$oTipoActividad = new TiposActividades($oUsuarioPerm->getId_tipo_activ_txt());

			$a_valores[$i]['sel']="$id_usuario#$id_item";
			$a_valores[$i][1]=$oTipoActividad->getNom();
			$oFase->setId_fase($id_fase_ini);
			$oFase->DBCarregar();
			$a_valores[$i][2]= $oFase->getDesc_fase();
			$oFase->setId_fase($id_fase_fin);
			$oFase->DBCarregar();
			$a_valores[$i][3]= $oFase->getDesc_fase();
			$a_valores[$i][4]=$oPermAccion->lista_txt($oUsuarioPerm->getAccion());
			$a_valores[$i][5]=$dl_propia_txt;

			$a_valores[$i][6]=$oCuadros->lista_tiene_txt($oUsuarioPerm->getAfecta_a());
		}
		$oHash3 = new web\Hash();
		$oHash3->setcamposForm('que!sel');
		$a_camposHidden = array(
				'id_usuario' => $id_usuario,
				'quien' => $quien
				);
		$oHash3->setArraycamposHidden($a_camposHidden);
		?>
		<br>
		<h3><?= ucfirst(_("permisos en actividades")) ?>:</h3>
		<?php
		if ($quien == 'usuario') {  // de momento no dejo hacer grupos de grupos.
			?>
			<b><?= _('grupos') ?>: </b><?= $txt ?>
			<input type=button onclick="fnjs_add_grup();" value='<?= _("añadir un grupo de permisos") ?>'>
			<input type=button onclick="fnjs_del_grup();" value='<?= _("quitar de un grupo de permisos") ?>'>
			<div id=lst_grupos></div>
			<br>
			<?php
		}
		?>
		<b><?= _('propios') ?>:</b>
		<form id=permisos name=permisos action=''>
		<?= $oHash3->getCamposHtml(); ?>
		<input type=hidden id=que  name=que value=''>
		<?php
		$oTabla = new web\Lista();
		$oTabla->setId_tabla('usuario_form_permisos');
		$oTabla->setCabeceras($a_cabeceras);
		$oTabla->setBotones($a_botones);
		$oTabla->setDatos($a_valores);
		echo $oTabla->mostrar_tabla();
		?>
		<br>
		<input type=button onclick="fnjs_add_perm('activ');" value='<?= _("añadir permiso") ?>'>
		</form>
		<?php
	}
} 
//////////// Esto lo ven todos ////////////

if (!empty($id_usuario)) { // si no hay usuario, no puedo poner permisos.
	?>
		<script>
		fnjs_modificar=function(formulario){
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				switch (formulario) {
					case '#permisos_menu':
						$(formulario).attr('action','apps/usuarios/controller/usuario_perm_menu.php');
						break;
					case '#permisos':
						$(formulario).attr('action','apps/usuarios/controller/usuario_perm_activ.php');
						break;
					case '#avisos':
						$(formulario).attr('action','apps/usuarios/controller/usuario_avisos_pref.php');
						break;
				}
				fnjs_enviar_formulario(formulario);
			}
		}

		fnjs_borrar=function(formulario,que_val){
			rta=fnjs_solo_uno(formulario);
			if (rta==1) {
				switch (formulario) {
					case '#permisos_menu':
					case '#permisos':
						if (confirm("<?php echo _("¿Esta seguro que desea borrar este permiso?");?>") ) {
							id_usuario=$('#id_usuario').val();
							if (formulario == '#permisos_menu') {
								$('#que').val('perm_menu_eliminar');
								go='<?= web\Hash::link('apps/usuarios/controller/usuario_form.php?quien=grupo&id_usuario='.$id_usuario) ?>';
							}	
							if (formulario == '#permisos') {
								$('#que').val('perm_eliminar');
								go='<?= web\Hash::link('apps/usuarios/controller/usuario_form.php?quien=usuario&id_usuario='.$id_usuario) ?>';
							}	
							$(formulario).attr('action',"apps/usuarios/controller/usuario_update.php");
							$(formulario).submit(function() {
								$.ajax({
									data: $(this).serialize(),
									type: 'post',
									url: $(this).attr('action'),
									complete: function (rta) { 
										rta_txt=rta.responseText;
										if (rta_txt.indexOf('id="ir_a"') != -1) {
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
						break;
					case '#avisos':
						if (confirm("<?php echo _("¿Esta seguro que desea borrar este aviso?");?>") ) {
							$('#av_que').val('aviso_eliminar');
							go='<?= web\Hash::link('apps/usuarios/controller/usuario_form.php?quien=usuario&id_usuario='.$id_usuario) ?>';
							$(formulario).attr('action',"apps/usuarios/controller/usuario_aviso_update.php");
							$(formulario).submit(function() {
								$.ajax({
									data: $(this).serialize(),
									type: 'post',
									url: $(this).attr('action'),
									complete: function (rta) { 
										rta_txt=rta.responseText;
										if (rta_txt.indexOf('id="ir_a"') != -1) {
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
						break;
				}
			}
		}
	fnjs_add_alert=function(){
		$('#avisos').attr('action',"apps/usuarios/controller/usuario_avisos_pref.php");
		fnjs_enviar_formulario('#avisos');
	}

	/* $('input[type=password]').password_strength(options); */
	</script>
	<?php
}
// si no hay usuario, no puedo poner permisos.
if( (core\ConfigGlobal::is_app_installed('avisos')) && (!empty($id_usuario)) && ($quien == 'usuario') ) {
	?>
	<b><?= _('avisos') ?>:</b>
	<form id="avisos" name="avisos" action=''>
	<?= $oHash3->getCamposHtml(); ?>
	<input type="hidden" id="av_que"  name="que" value="">
	<?php
	$oTablaAvisos = new Lista();
	$oTablaAvisos->setId_tabla('usuario_form_avisos');
	$oTablaAvisos->setCabeceras($a_cabeceras_avisos);
	$oTablaAvisos->setBotones($a_botones_avisos);
	$oTablaAvisos->setDatos($a_valores_avisos);
	echo $oTablaAvisos->mostrar_tabla();
	?>
	<br>
	<input type=button onclick="fnjs_add_alert();" value="<?= _("añadir aviso") ?>">
	</form>
	<?php
}
?>
