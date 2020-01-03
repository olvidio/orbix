<?php 
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

$oMiUsuario = new usuarios\model\entity\Usuario(core\ConfigGlobal::mi_id_usuario());
$miSfsv = core\ConfigGlobal::mi_sfsv();

//casas
$oHash = new web\Hash();
$oHash->setcamposForm('cdc_sel!id_cdc_mas!id_cdc_num!empiezamax!empiezamin!iactividad_val!iasistentes_val!periodo!year');
$oHash->setcamposNo('id_cdc!modelo');
$a_camposHidden = array(
    'modelo' => '',
);
$oHash->setArraycamposHidden($a_camposHidden);

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
$oFormP->setTitulo(core\strtoupper_dlb(_("período del planning actividades para el próximo año")));
$oFormP->setPosiblesPeriodos($aOpciones);
$oFormP->setDesplAnysOpcion_sel(date('Y')+1);

$oForm = new web\CasasQue();
$oForm->setTitulo(core\strtoupper_dlb(_("búsqueda de casas cuyo planning interesa")));
if ($oMiUsuario->isRole('Casa')) {
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
    'oHash' => $oHash,
    'oFormP' => $oFormP,
    'oForm' => $oForm,
];

$oView = new core\View('actividades/controller');
echo $oView->render('calendario_que.phtml',$a_campos);
