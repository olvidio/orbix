<?php
use cambios\model\entity\Cambio;
use cambios\model\entity\CambioDl;
use cambios\model\entity\CambioUsuarioObjetoPref;
use cambios\model\entity\GestorCambioUsuario;
use core\ConfigGlobal;
use usuarios\model\entity\GestorUsuario;
use usuarios\model\entity\Usuario;
use web\Desplegable;
use web\Lista;
use cambios\model\entity\CambioUsuario;

// INICIO Cabecera global de URL de controlador *********************************

require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
require_once ("apps/core/global_object.inc");
// Crea los objectos para esta url  **********************************************


// Tipos de avisos
$aTipos_aviso = CambioUsuarioObjetoPref::getTipos_aviso();
$dele = ConfigGlobal::mi_dele();
$delef = $dele.'f';
$aSecciones = array(1=>$dele,2=>$delef);

$oDesplTiposAviso = new Desplegable();
$oDesplTiposAviso->setNombre('aviso_tipo');
$oDesplTiposAviso->setBlanco('false');
$oDesplTiposAviso->setOpciones($aTipos_aviso);

$GesUsuarios = new GestorUsuario();
$ListaUsuarios = $GesUsuarios->getListaUsuarios();

$oDesplUsuarios = new Desplegable();
$oDesplUsuarios->setNombre('id_usuario');
$oDesplUsuarios->setBlanco('false');
$oDesplUsuarios->setOpciones($ListaUsuarios);


if ($_SESSION['oPerm']->have_perm_oficina('dtor')){ //el admin_sv incluye el admin_sf
    $Qid_usuario = (integer) \filter_input(INPUT_POST, 'id_usuario');
    $Qaviso_tipo = (integer) \filter_input(INPUT_POST, 'aviso_tipo');
} else {
	$Qid_usuario = ConfigGlobal::mi_id_usuario();
	$Qaviso_tipo =  CambioUsuario::TIPO_LISTA; // de moment nomes "anotar en lista".
}

$a_campos = [];

if (!empty($Qid_usuario)) {
	//$Qid_usuario = 4128; // of1sm.
	//$Qaviso_tipo = 1; // lista.

	// seleccionar por usuario
	$mi_sfsv = ConfigGlobal::mi_sfsv();

	$aWhere = array();
	$aWhere['id_usuario'] = $Qid_usuario;
	$aWhere['sfsv'] = $mi_sfsv;
	$aWhere['aviso_tipo'] = $Qaviso_tipo;
	$aWhere['avisado'] = 'false';
	$GesCambiosUsuario = new GestorCambioUsuario();
	$cCambiosUsuario = $GesCambiosUsuario->getCambiosUsuario($aWhere);
	$a_valores = [];
	$i = 0;
	foreach ($cCambiosUsuario as $oCambioUsuario) {
		$id_item_cmb = $oCambioUsuario->getId_item_cambio();
		$id_schema_cmb = $oCambioUsuario->getId_schema_cambio();
		if ($id_schema_cmb == 3000) {
            $oCambio = new Cambio($id_item_cmb);
		} else {
            $oCambio = new CambioDl($id_item_cmb);
		}
		$quien_cambia = $oCambio->getQuien_cambia();
		$sfsv_quien_cambia = $oCambio->getSfsv_quien_cambia();
		$timestamp_cambio = $oCambio->getTimestamp_cambio();
		
		$aviso_txt = $oCambio->getAvisoTxt();
		
		if ($aviso_txt === false) continue;
		$i++;
		if ($sfsv_quien_cambia == $mi_sfsv) {
            $oUsuarioCmb = new Usuario($quien_cambia);
            $quien = $oUsuarioCmb->getUsuario();
		} else {
            $quien = $aSecciones[$sfsv_quien_cambia] ;
		}
		$a_valores[$i]['sel']="$id_item_cmb#$mi_sfsv#$Qid_usuario#$Qaviso_tipo";
		$a_valores[$i][1]=$timestamp_cambio;
		$a_valores[$i][2]=$quien;
		$a_valores[$i][3]=$aviso_txt;
	}

	$a_cabeceras = [ [ 'name'=>ucfirst(_("fecha cambio")),'class'=>'fecha_hora' ],
					 ucfirst(_("quien")),
					 ucfirst(_("cambio"))
				   ];
	$a_botones = [
	           array( 'txt' => _("borrar"), 'click' =>"fnjs_borrar(\"#seleccionados\")" ),
	           array( 'txt' => _("todos"), 'click' =>"fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"all\",0)" ),
	           array( 'txt' => _("ninguno"), 'click' =>"fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"none\",0)" ),
	           array( 'txt' => _("invertir"), 'click' =>"fnjs_selectAll(\"#seleccionados\",\"sel[]\",\"toggle\",0)" ),
	           ];

    $oTabla = new Lista();
    $oTabla->setId_tabla('avisos_tabla');
    $oTabla->setCabeceras($a_cabeceras);
    $oTabla->setSortCol(ucfirst(_("fecha cambio"))); // Tiene que ser el nombre de la cabecera (mayusculas).
    $oTabla->setBotones($a_botones);
    $oTabla->setDatos($a_valores);

    $oHash = new web\Hash();
    $oHash->setArrayCamposHidden(['que' => 'eliminar']);
    $oHash->setCamposNo('scroll_id!sel');

    $a_campos = [
        'oPosicion' => $oPosicion,
        'oHash' => $oHash,
        'oTabla' => $oTabla,
    ];
    
} else {
    $oHashCond = new web\Hash();
    $oHashCond->setcamposForm("id_usuario!aviso_tipo");
    
    $a_camposCond = [
        'oPosicion' => $oPosicion,
        'oHashCond' => $oHashCond,
        'oDesplUsuarios' => $oDesplUsuarios,
        'oDesplTiposAviso' => $oDesplTiposAviso,
    ];
    
    $oView = new core\ViewTwig('cambios/controller');
    echo $oView->render('avisos_generar_condicion.html.twig',$a_camposCond);
}

$oView = new core\ViewTwig('cambios/controller');
echo $oView->render('avisos_generar_lista.html.twig',$a_campos);