<?php
use actividades\model\entity\ActividadAll;
use cambios\model\entity\CambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuarioPropiedadPref;
use core\ConfigGlobal;
use personas\model\entity\GestorPersonaAgd;
use personas\model\entity\GestorPersonaDl;
use personas\model\entity\GestorPersonaN;
use procesos\model\PermAccion;
use procesos\model\PermAfectados;
use procesos\model\entity\ActividadFase;
use procesos\model\entity\GestorPermUsuarioActividad;
use ubis\model\entity\GestorCasaDl;
use ubis\model\entity\GestorCentroDl;
use ubis\model\entity\GestorCentroEllas;
use usuarios\model\entity\GestorRole;
use usuarios\model\entity\Role;
use usuarios\model\entity\Usuario;
use web\Lista;
use web\TiposActividades;
use usuarios\model\entity\GestorPermUsuarioCentro;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************
	$oCuadrosAfecta = new PermAfectados();
	$oPermAccion = new PermAccion();


// FIN de  Cabecera global de URL de controlador ********************************

$Qrefresh = (integer)  \filter_input(INPUT_POST, 'refresh');
$oPosicion->recordar($Qrefresh);

$Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
$Qquien = (string) \filter_input(INPUT_POST, 'quien');

$Qscroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// Hay que usar isset y empty porque puede tener el valor =0.
// Si vengo por medio de Posicion, borro la última
if (isset($_POST['stack'])) {
	$stack = \filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
	if ($stack != '') {
		// No me sirve el de global_object, sino el de la session
		$oPosicion2 = new web\Posicion();
		if ($oPosicion2->goStack($stack)) { // devuelve false si no puede ir
			$a_sel=$oPosicion2->getParametro('id_sel');
			if (!empty($a_sel)) {
                $Qid_usuario = (integer) strtok($a_sel[0],"#");
			} else {
                $Qid_usuario = $oPosicion2->getParametro('id_usuario');
			    $Qquien = $oPosicion2->getParametro('quien');
			}
			$Qscroll_id = $oPosicion2->getParametro('scroll_id');
			$oPosicion2->olvidar($stack);
		}
	}
} elseif (!empty($a_sel)) { //vengo de un checkbox
	$Qque = (string) \filter_input(INPUT_POST, 'que');
	if ($Qque != 'del_grupmenu') { //En el caso de venir de borrar un grupmenu, no hago nada
	    $Qid_usuario = (integer) strtok($a_sel[0],"#");
		// el scroll id es de la página anterior, hay que guardarlo allí
		$oPosicion->addParametro('id_sel',$a_sel,1);
		$Qscroll_id = (integer) \filter_input(INPUT_POST, 'scroll_id');
		$oPosicion->addParametro('scroll_id',$Qscroll_id,1);
	}
}
$oPosicion->setParametros(array('id_usuario'=>$Qid_usuario),1);

$oMiUsuario = new Usuario(ConfigGlobal::mi_id_usuario());
$miRole=$oMiUsuario->getId_role();
$miSfsv = ConfigGlobal::mi_sfsv();

if ($Qquien=='usuario') $obj = 'usuarios\\model\\entity\\Usuario';

if( (ConfigGlobal::is_app_installed('cambios')) && (!empty($Qid_usuario)) && ($Qquien == 'usuario') ) {
    $a_status = ActividadAll::ARRAY_STATUS_TXT;
    
	// avisos
	$oGesCambiosUsuariosObjeto = new GestorCambioUsuarioObjetoPref();
	$cListaTablas = $oGesCambiosUsuariosObjeto->getCambioUsuarioObjetosPrefs(array('id_usuario'=>$Qid_usuario));

	// Tipos de avisos
	$aTipos_aviso = CambioUsuarioObjetoPref::getTipos_aviso();

	$i=0;
	$a_cabeceras_avisos = [_("dl propia"),
	                   _("tipo de actividad"),
	                   _("fase inicial"),
	                   _("fase final"),
	                   _("objeto"),
	                   _("tipo de aviso"),
	                   _("propiedades"),
	                   _("valor"),
	               ];
	$a_botones_avisos = [
				        array( 'txt' => _("modificar"), 'click' =>"fnjs_mod_cambio(\"#avisos\")" ),
				        array( 'txt' => _("eliminar"), 'click' =>"fnjs_del_cambio(\"#avisos\")" ) 
			        ];
	$a_valores_avisos = [];
	$oFase = new ActividadFase();
	foreach ($cListaTablas as $oCambioUsuarioObjetoPref) {
		$i++;
		
		$id_item_usuario_objeto=$oCambioUsuarioObjetoPref->getId_item_usuario_objeto();
		$id_tipo=$oCambioUsuarioObjetoPref->getId_tipo_activ_txt();
		$id_fase_ini=$oCambioUsuarioObjetoPref->getId_fase_ini();
		$id_fase_fin=$oCambioUsuarioObjetoPref->getId_fase_fin();
		$dl_org=$oCambioUsuarioObjetoPref->getDl_org();
		$objeto=$oCambioUsuarioObjetoPref->getObjeto();
		$aviso_tipo=$oCambioUsuarioObjetoPref->getAviso_tipo();

		$isfsv = substr($id_tipo, 0, 1);
		$mi_dele = ConfigGlobal::mi_delef($isfsv);
		if ($dl_org != $mi_dele) {
		    $dl_org = _("otras");
		}

		$oTipoActividad = new TiposActividades($oCambioUsuarioObjetoPref->getId_tipo_activ_txt());

		$a_valores_avisos[$i]['sel']="$Qid_usuario#$id_item_usuario_objeto";
		$a_valores_avisos[$i][1]=$dl_org;
		$a_valores_avisos[$i][2]=$oTipoActividad->getNom();
		if (ConfigGlobal::is_app_installed('procesos')) {
            $oFase->setId_fase($id_fase_ini);
            $oFase->DBCarregar();
            $a_valores_avisos[$i][3]= $oFase->getDesc_fase();
            $oFase->setId_fase($id_fase_fin);
            $oFase->DBCarregar();
            $a_valores_avisos[$i][4]= $oFase->getDesc_fase();
		} else {
		    
            $a_valores_avisos[$i][3] = $a_status[$id_fase_ini];
            $a_valores_avisos[$i][4] = $a_status[$id_fase_fin];
		}
		$a_valores_avisos[$i][5]=$objeto;
		$a_valores_avisos[$i][6]=$aTipos_aviso[$aviso_tipo];
		$GesCambiosUsuarioPropiedadesPref = new GestorCambioUsuarioPropiedadPref();
		$cListaPropiedades = $GesCambiosUsuarioPropiedadesPref->getCambioUsuarioPropiedadesPrefs(array('id_item_usuario_objeto'=>$id_item_usuario_objeto));
		$txt_cambio = '';
		$txt_propiedades = '';
		$c = 0;
		foreach ($cListaPropiedades as $oCambioUsuarioPropiedadPref) {
			$c++;
			$propiedad = $oCambioUsuarioPropiedadPref->getPropiedad();
			$operador = $oCambioUsuarioPropiedadPref->getOperador();
			$valor = $oCambioUsuarioPropiedadPref->getValor();
			$valor_old = $oCambioUsuarioPropiedadPref->getValor_old();
			$valor_new = $oCambioUsuarioPropiedadPref->getValor_new();
			if ($c > 1) { $txt_propiedades .= ', '; }
			$txt_cambio .= empty($txt_cambio)? '' : ', ';
			$txt_propiedades .= $propiedad;
			$txt_cambio .= $oCambioUsuarioPropiedadPref->getTextCambio();
			
		}
		$a_valores_avisos[$i][7]=$txt_propiedades;
		$a_valores_avisos[$i][8]=$txt_cambio;
	}

	$oTablaAvisos = new Lista();
	$oTablaAvisos->setId_tabla('usuario_form_avisos');
	$oTablaAvisos->setCabeceras($a_cabeceras_avisos);
	$oTablaAvisos->setBotones($a_botones_avisos);
	$oTablaAvisos->setDatos($a_valores_avisos);
}

// a los usuarios normales (no administrador) sólo dejo ver la parte de los avisos.
if ($miRole < 4) {
	if ($miRole > 3) exit(_("no tiene permisos para ver esto")); // no es administrador
	if ($miRole != 1) {
	    $cond_role="WHERE id_role <> 1 ";
	} else {
	    $cond_role="WHERE id_role > 0 "; //absurda cond, pero para que no se borre el role del superadmin
	}

	switch($miSfsv) {
		case 1:
			$cond_role.="AND sv='t'";
			break;
		case 2:
			$cond_role.="AND sf='t'";
			break;
	}

	if( !(ConfigGlobal::is_app_installed('personas')) ) {
	    $cond_role.="AND (pau != '".Role::PAU_SACD."' OR pau IS NULL)";
	}
	if( !(ConfigGlobal::is_app_installed('ubis')) ) {
	    $cond_role.="AND (pau != '".Role::PAU_CTR."' OR pau != '".Role::PAU_CDC."' OR pau IS NULL)";
	}
			
	$oGRoles = new GestorRole();
	$oDesplRoles= $oGRoles->getListaRoles($cond_role);
	$oDesplRoles->setNombre('id_role');

	$txt_guardar=_("guardar datos usuario");
	$oGrupoGrupoPermMenu = array();
    $cUsuarioPerm = [];
    $cUsuarioPermCtr = [];
	$oSelects = array();
	if (!empty($Qid_usuario)) {
		$que_user='guardar';
		$oUsuario = new Usuario(array('id_usuario'=>$Qid_usuario));

		$id_usuario=$oUsuario->getId_usuario();
		$seccion=$miSfsv;
		$usuario=$oUsuario->getUsuario();
		$nom_usuario=$oUsuario->getNom_usuario();
		$pass=$oUsuario->getPassword();
		$email=$oUsuario->getEmail();
		$id_role=$oUsuario->getId_role();
		$oDesplRoles->setOpcion_sel($id_role);
		$oRole = new Role($id_role);
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
			$oGCasas = new GestorCasaDl();
			$oOpcionesCasas = $oGCasas->getPosiblesCasas($cond);
			//$oDesplCasas = new Desplegable(array('oOpciones'=>$oOpcionesCasas));	

			$oSelects = new web\DesplegableArray($id_pau,$oOpcionesCasas,'casas');
			$oSelects->setBlanco('t');
			$oSelects->setAccionConjunto('fnjs_mas_casas(event)');
			$camposMas = 'casas!casas_mas!casas_num';
		}
		if ($pau == 'ctr' && $sv == 1) { //centroSv
			$id_pau=$oUsuario->getId_pau();
			$oGesCentrosDl = new GestorCentroDl();
			$oSelects = $oGesCentrosDl->getListaCentros();

			$oSelects->setNombre('id_ctr');
			$oSelects->setOpcion_sel($id_pau);
			$oSelects->setBlanco('t');
			$camposMas = 'id_ctr';
		}
		if ($pau == 'ctr' && $sf == 1) { //centroSf
			$id_pau=$oUsuario->getId_pau();
			$oGesCentrosDl = new GestorCentroEllas();
			$oSelects = $oGesCentrosDl->getListaCentros();

			$oSelects->setNombre('id_ctr');
			$oSelects->setOpcion_sel($id_pau);
			$oSelects->setBlanco('t');
			$camposMas = 'id_ctr';
		}
		if ($pau == Role::PAU_NOM || $pau == Role::PAU_SACD) { //sacd //personas dl
			$id_pau=$oUsuario->getId_pau();

			$nom_role = $oRole->getRole();
			switch ($nom_role) {
			    case "p-agd":
                    $GesPersonas = new GestorPersonaAgd();
                    $oSelects = $GesPersonas->getListaPersonas();
                break;
			    case "p-n":
			        $GesPersonas = new GestorPersonaN();
                    $oSelects = $GesPersonas->getListaPersonas();
                    break;
			    case "p-sacd":
			    case "p-sacdInt": // para hacer pruebas desde dentro (dmz=false)
                    $GesPersonas = new GestorPersonaDl();
                    // de momento sólo n y agd
                    $oSelects = $GesPersonas->getListaSacd("AND id_tabla ~ '[na]'");
                break;
			}

			$oSelects->setNombre('id_nom');
			$oSelects->setOpcion_sel($id_pau);
			$oSelects->setBlanco('t');
			$camposMas = 'id_nom';
			
			/*			
			$oGesPermCtr = new GestorPermUsuarioCentro();
			$cUsuarioPermCtr = $oGesPermCtr->getPermUsuarioCentros(array('id_usuario'=>$id_usuario));
			*/
			
		}
		
		if (ConfigGlobal::is_app_installed('procesos')) { 
			$oGesPerm = new GestorPermUsuarioActividad();
			$cUsuarioPerm = $oGesPerm->getPermUsuarioActividades(array('id_usuario'=>$id_usuario));
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
		$pau='';
	}
	//$camposForm = 'que!usuario!nom_usuario!password!email!id_role!id_ctr!id_sacd!casas';
	$camposForm = 'que!usuario!nom_usuario!password!email!id_role';
	$camposForm = !empty($camposMas)? $camposForm.'!'.$camposMas : $camposForm;
	$oHash = new web\Hash();
	$oHash->setcamposForm($camposForm);
	$oHash->setcamposNo('pass!password!id_ctr!id_nom!casas');
	$a_camposHidden = array(
			'id_usuario' => $id_usuario,
			'quien' => $Qquien
			);
	$oHash->setArraycamposHidden($a_camposHidden);

	$url_usuario_ajax = ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php';
	$oHash1 = new web\Hash();
	$oHash1->setUrl($url_usuario_ajax);
	$oHash1->setCamposForm('que!id_usuario'); 
	$oHash1->setCamposNo('scroll_id'); 
	$h1 = $oHash1->linkSinVal();
	
	$txt_eliminar = _("¿Está seguro que desea quitar este permiso?");
	
	$a_campos = [
				'oPosicion' => $oPosicion,
				'url_usuario_ajax' => $url_usuario_ajax,
				'id_usuario' => $Qid_usuario,
				'h1' => $h1,
				'obj' => $obj,
				'que_user' => $que_user,
				'quien' => $Qquien,
				'id_usuario' => $id_usuario,
				'pau' => $pau,
				'oSelects' => $oSelects,
				'usuario' => $usuario,
				'oHash' => $oHash,
				'pass' => $pass,
				'usuario' => $usuario,
				'nom_usuario' => $nom_usuario,
				'oDesplRoles' => $oDesplRoles,
				'oGrupoGrupoPermMenu' => $oGrupoGrupoPermMenu,
                'cUsuarioPermCtr' => $cUsuarioPermCtr,
				'email' => $email,
				'txt_guardar' => $txt_guardar,
				'txt_eliminar' => $txt_eliminar,
				];

	$oView = new core\View('usuarios/controller');
	echo $oView->render('usuario_form.phtml',$a_campos);
} 

//////////// Permisos de grupos ////////////
if (!empty($id_usuario)) { // si no hay usuario, no puedo poner permisos.
    //grupo
    $oGesUsuarioGrupo = new usuarios\model\entity\GestorUsuarioGrupo();
    $oListaGrupos = $oGesUsuarioGrupo->getUsuariosGrupos(array('id_usuario'=>$id_usuario));
    $i=0;
    $txt='';
    foreach ($oListaGrupos as $oUsuarioGrupo) {
        $i++;
        $oGrupo = new usuarios\model\entity\Grupo($oUsuarioGrupo->getId_grupo());
        if ($i > 1) $txt.=", ";
        $txt.= $oGrupo->getUsuario();
    }
    ?>
	<br>
	<h3><?= _("grupos") ?>: </h3>
    <?php
    //////////// Aclaración permisos ////////////
    if (ConfigGlobal::is_app_installed('procesos')) {
        ?>
    	<p class="comentario"><?= _("OJO: los permisos en los grupos no tienen una preferencia definida.") ?>
    	<?= _("Si hay más de uno, deberían ser independiente, sino no se sabe cual sobreescribirá a cual.") ?></p>
		<?php
    } ?>
    <br>
	<p><?= $txt ?></p>
	<br>
	<input type=button onclick="fnjs_add_grup();" value="<?= _("añadir un grupo de permisos") ?>">
	<input type=button onclick="fnjs_del_grup();" value="<?= _("quitar de un grupo de permisos") ?>">
	<div id=lst_grupos></div>
	<br>
    <br>
    <?php
    //////////// Permisos en centros ////////////
    if (ConfigGlobal::is_app_installed('ubis')) {
        if ($pau == Role::PAU_NOM || $pau == Role::PAU_SACD) { //sacd //personas dl
            $a_campos = [
                        'quien' => $Qquien,
                        'id_usuario' => $id_usuario,
                        'usuario' => $usuario,
                        'cUsuarioPermCtr' => $cUsuarioPermCtr,
                        'oCuadrosAfecta' => $oCuadrosAfecta,
                        'oPermAccion' => $oPermAccion,
                        ];

            $oView = new core\View('usuarios/controller');
            echo $oView->render('perm_ctr_form.phtml',$a_campos);
        }
    }
    //////////// Permisos en actividades ////////////
    if (ConfigGlobal::is_app_installed('procesos')) {
        
        $a_campos = [
                    'quien' => $Qquien,
                    'id_usuario' => $id_usuario,
                    'usuario' => $usuario,
                    'cUsuarioPerm' => $cUsuarioPerm,
                    'oCuadrosAfecta' => $oCuadrosAfecta,
                    'oPermAccion' => $oPermAccion,
                    ];

        $oView = new core\View('usuarios/controller');
        echo $oView->render('perm_activ_form.phtml',$a_campos);
    }
}

//////////// Esto lo ven todos ////////////
// si no hay usuario, no puedo poner permisos.
if( (ConfigGlobal::is_app_installed('cambios')) && (!empty($id_usuario)) && ($Qquien == 'usuario') ) {
    
	$url_usuario_ajax = ConfigGlobal::getWeb().'/apps/usuarios/controller/usuario_ajax.php';
	$oHashAvisos = new web\Hash();
	$oHashAvisos->setUrl($url_usuario_ajax);
	$oHashAvisos->setCamposNo('sel!scroll_id'); 
	$a_camposHidden = array(
			'id_usuario' => $id_usuario,
			'quien' => $Qquien,
	        'salida' => 'aviso_eliminar',
			);
	$oHashAvisos->setArraycamposHidden($a_camposHidden);
	$h1 = $oHashAvisos->linkSinVal();
	
	$a_camposAvisos = [
	            'oPosicion' => $oPosicion,
				'oHashAvisos' => $oHashAvisos,
				'oTablaAvisos' => $oTablaAvisos,
				];

	$oView = new core\View('cambios/controller');
	echo $oView->render('usuario_form_avisos.phtml',$a_camposAvisos);
}