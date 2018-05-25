<?php
use actividades\model\entity as actividades;

// INICIO Cabecera global de URL de controlador *********************************
	require_once ("apps/core/global_header.inc");
// Arxivos requeridos por esta url **********************************************

// Crea los objectos de uso global **********************************************
	require_once ("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

	
$oPosicion->recordar();

$a_sel = (array)  \filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (!empty($a_sel)) { //vengo de un checkbox
	$id_activ = strtok($a_sel[0],"#");
    $nom_activ=strtok("#");
	// el scroll id es de la página anterior, hay que guardarlo allí
	$oPosicion->addParametro('id_sel',$a_sel,1);
	$scroll_id = empty($_POST['scroll_id'])? 0 : $_POST['scroll_id'];
	$oPosicion->addParametro('scroll_id',$scroll_id,1);
} else { // vengo de actualizar
	$id_activ = (integer)  filter_input(INPUT_POST, 'id_activ');
	$nom_activ = (string)  filter_input(INPUT_POST, 'nom_activ');
	// borro la última
	$stack = $oPosicion->getStack() - 1 ;
	$oPosicion2 = new web\Posicion();
	$oPosicion2->olvidar($stack);
}

$gesDelegacion = new ubis\model\entity\GestorDelegacion();
$oDesplDelegaciones = $gesDelegacion->getListaDelegaciones(array('H'));
$oDesplDelegaciones->setNombre('dl');

// comprobar que la actividad está publicada, sino avisar!
$publicado = '';
$oActividad = new actividades\ActividadDl($id_activ);
$publicado = $oActividad->getPublicado();
// Si no es una actividad de la dl, publicado da NULL (igual que todos los campos)
if ($publicado === null) {
	$publicado = true;
}

$gesActividadPlazas = new \actividadplazas\model\entity\GestorResumenPlazas();
$gesActividadPlazas->setId_activ($id_activ);
$a_plazas = $gesActividadPlazas->getResumen();

$plazas_totales = $a_plazas['total']['actividad'];
$tot_calendario = $a_plazas['total']['calendario'];
$tot_cedidas = $a_plazas['total']['cedidas'];
$tot_conseguidas = $a_plazas['total']['conseguidas'];
$tot_actual = $a_plazas['total']['actual'];
$tot_ocupadas = $a_plazas['total']['ocupadas'];



$oHash = new web\Hash();
$camposForm = 'num_plazas!dl';
$a_camposHidden = array(
		'id_activ' => $id_activ,
		'que' => 'ceder'
		);
$oHash->setcamposForm($camposForm);
$oHash->setArraycamposHidden($a_camposHidden);

$oHashActualizar = new web\Hash();
$oHashActualizar->setCamposNo('refresh');
$a_camposHiddenActualizar = array(
		'id_activ' => $id_activ,
		'nom_activ' => $nom_activ
		);
$oHashActualizar->setArraycamposHidden($a_camposHiddenActualizar);

$a_campos = [
			'oPosicion' => $oPosicion,
			'oHashActualizar' => $oHashActualizar,
			'oHash' => $oHash,
			'publicado' => $publicado,
			'nom_activ' => $nom_activ,
			'a_plazas' => $a_plazas,
			'tot_calendario' => $tot_calendario,
			'plazas_totales' => $plazas_totales,
			'tot_cedidas' => $tot_cedidas,
			'tot_conseguidas' => $tot_conseguidas,
			'tot_actual' => $tot_actual,
			'tot_ocupadas' => $tot_ocupadas,
			'oDesplDelegaciones' => $oDesplDelegaciones,
			];

$oView = new core\View('actividadplazas/controller');
echo $oView->render('resumen_plazas.phtml',$a_campos);