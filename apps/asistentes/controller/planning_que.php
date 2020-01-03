<?php 
use usuarios\model\entity as usuarios;
/**
* Página que presentará los formularios de los distintos plannings 
* Según sea el submenú seleccionado seleccionará el formulario
* correspondiente
*
*@package	delegacion
*@subpackage	actividades
*@author	Josep Companys
*@since		15/5/02.
*		
*/

// INICIO Cabecera global de URL de controlador *********************************
	require_once ('apps/core/global_header.inc');
// Arxivos requeridos por esta url **********************************************
	

// Crea los objectos de uso global **********************************************
	require_once ('apps/core/global_object.inc');
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();
	
$oMiUsuario = new usuarios\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv = core\ConfigGlobal::mi_sfsv();

if (date('m')>9) {
	$periodo_txt= _("(por defecto: periodo desde 1/10 hasta 31/5)"); 
} else {
	$periodo_txt= _("(por defecto: periodo desde 1/6 hasta 30/9)"); 
}
$Qtipo = (string) \filter_input(INPUT_POST, 'tipo');
$Qobj_pau = (string) \filter_input(INPUT_POST, 'obj_pau');
$Qna = (string) \filter_input(INPUT_POST, 'na');

//personas
$oHash = new web\Hash();
$oHash->setcamposForm('nombre!apellido1!apellido2!centro!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('modelo');
$a_camposHidden = array(
		'tipo' => $Qtipo,
		'obj_pau' => $Qobj_pau,
		'na' => $Qna
		);
$oHash->setArraycamposHidden($a_camposHidden);
// centros
$oHash1 = new web\Hash();
$oHash1->setcamposForm('sacd!ctr!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash1->setcamposNo('todos_n!todos_agd!todos_s!modelo');
$a_camposHidden1 = array(
		'tipo' => $Qtipo,
		'obj_pau' => $Qobj_pau,
		);
$oHash1->setArraycamposHidden($a_camposHidden1);
//casas
$oHash2 = new web\Hash();
$oHash2->setcamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash2->setcamposNo('id_cdc!sin_activ!modelo');
$a_camposHidden2 = array(
		'tipo' => $Qtipo,
		'obj_pau' => $Qobj_pau
		);
$oHash2->setArraycamposHidden($a_camposHidden2);

$aOpciones =  array(
					'tot_any' => _("todo el año"),
					'trimestre_1'=>_("primer trimestre"),
					'trimestre_2'=>_("segundo trimestre"),
					'trimestre_3'=>_("tercer trimestre"),
					'trimestre_4'=>_("cuarto trimestre"),
					'separador'=>'---------',
					'otro'=>_("otro")
					);
$oFormP = new web\PeriodoQue();
$oFormP->setFormName('que');
$oFormP->setTitulo(core\strtoupper_dlb(_("periodo del planning actividades")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel(date('Y'));

switch ($Qtipo) {
	case 'planning':
	case 'p_de_paso':
        $personas_txt = '';
	    switch ($Qobj_pau) {
	        case 'PersonaN':
	            $personas_txt = _("numerarios");
	            break;
	        case 'PersonaNax':
	            $personas_txt = _("nax");
	            break;
	        case 'PersonaAgd':
	            $personas_txt = _("agregados");
	            break;
	        case 'PersonaS':
	            $personas_txt = _("supernumerarios");
	            break;
	        case 'PersonaSSSC':
	            $personas_txt = _("de la sss+");
	            break;
	    }
	//cuando queramos visualizar el calendario de actividades de
	//1 persona de dlb o de paso
		$a_campos = ['oPosicion' => $oPosicion,
					'oHash' => $oHash,
					'oFormP' => $oFormP,
		            'personas_txt' => $personas_txt,
					];

		$oView = new core\View('asistentes/controller');
		echo $oView->render('planning_persona_que.phtml',$a_campos);
	break;
	case 'planning_ctr':
	//cuando queramos visualizar el calendario de actividades de
	//todas las personas de 1 ctr
		$a_campos = ['oPosicion' => $oPosicion,
					'oHash1' => $oHash1,
					'oFormP' => $oFormP,
					];

		$oView = new core\View('asistentes/controller');
		echo $oView->render('planning_ctr_que.phtml',$a_campos);
	break;
	case 'planning_cdc':
		$oForm = new web\CasasQue();
		$oForm->setTitulo(core\strtoupper_dlb(_("búsqueda de casas cuyo planning interesa")));
		// miro que rol tengo. Si soy casa, sólo veo la mía
		if ($oMiUsuario->isRolePau('cdc')) {
			$id_pau=$oMiUsuario->getId_pau();
			$sDonde=str_replace(",", " OR id_ubi=", $id_pau);
			//formulario para casas cuyo calendario de actividades interesa 
			$donde="WHERE status='t' AND (id_ubi=$sDonde)";
			$oForm->setCasas('casa');
		} else {
			if ($_SESSION['oPerm']->have_perm_oficina('des') or $_SESSION['oPerm']->have_perm_oficina('vcsd')) {
				$oForm->setCasas('all');
				$donde="WHERE status='t'";
			} else {
				if ($miSfsv == 1) {
					$oForm->setCasas('sv');
					$donde="WHERE status='t' AND sv='t'";
				}
				if ($miSfsv == 2) {
					$oForm->setCasas('sf');
					$donde="WHERE status='t' AND sf='t'";
				}
			}
		}
		$oForm->setPosiblesCasas($donde);
		
		$a_campos = ['oPosicion' => $oPosicion,
					'oHash2' => $oHash2,
					'oFormP' => $oFormP,
					'oForm' => $oForm,
					];

		$oView = new core\View('asistentes/controller');
		echo $oView->render('planning_casa_que.phtml',$a_campos);
}