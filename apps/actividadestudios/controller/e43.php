<?php
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$oPosicion->recordar();

$msg_err = '';
$Qid_activ = (integer)filter_input(INPUT_POST, 'id_pau');

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
// vengo directamente con un id:
if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_nom = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
} else {
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
}


$oPersona = personas\model\entity\Persona::NewPersona($Qid_nom);
if (!is_object($oPersona)) {
    $msg_err .= "<br>$oPersona con id_nom: $Qid_nom en  " . __FILE__ . ": line " . __LINE__;
}

$nom = $oPersona->getNombreApellidos();
$lugar_nacimiento = $oPersona->getLugar_nacimiento();
$f_nacimiento = $oPersona->getF_nacimiento()->getFromLocal();
$txt_nacimiento = "$lugar_nacimiento ($f_nacimiento)";

$dl_origen = core\ConfigGlobal::mi_delef();
$dl_destino = $oPersona->getDl();

$oActividad = new actividades\model\entity\Actividad($Qid_activ);
$nom_activ = $oActividad->getNom_activ();
$id_ubi = $oActividad->getId_ubi();
$f_ini = $oActividad->getF_ini()->getFromLocal();
$f_fin = $oActividad->getF_fin()->getFromLocal();
$oUbi = ubis\model\entity\Ubi::NewUbi($id_ubi);
$lugar = $oUbi->getNombre_ubi();

$txt_actividad = "$lugar, $f_ini-$f_fin";

$GesMatriculas = new actividadestudios\model\entity\GestorMatricula();
$cMatriculas = $GesMatriculas->getMatriculas(array('id_nom' => $Qid_nom, 'id_activ' => $Qid_activ));
$matriculas = count($cMatriculas);
$aAsignaturasMatriculadas = array();
if ($matriculas > 0) {
    // para ordenar
    foreach ($cMatriculas as $oMatricula) {
        $id_asignatura = $oMatricula->getId_asignatura();
        $oAsignatura = new asignaturas\model\entity\Asignatura($id_asignatura);
        $nombre_corto = $oAsignatura->getNombre_corto();
        //$nota = $oMatricula->getNota_txt();

        $GesNotas = new notas\model\entity\GestorPersonaNota();
        $cNotas = $GesNotas->getPersonaNotas(array('id_nom' => $Qid_nom, 'id_asignatura' => $id_asignatura));
        if ($cNotas !== FALSE && count($cNotas) > 0) {
            $oNota = $cNotas[0];
            $nota = $oNota->getNota_txt();
            $acta = $oNota->getActa();
            $f_acta = $oNota->getF_acta()->getFromLocal();
        } else {
            $nota = '';
            $acta = '';
            $f_acta = '';
        }
        $aAsignaturasMatriculadas[] = array('nom_asignatura' => $nombre_corto,
            'nota' => $nota,
            'f_acta' => $f_acta,
            'acta' => $acta);
    }
} else {
    $msg_err .= _("no hay ninguna matrícula de esta persona");
}

$oHash = new web\Hash();
$oHash->setUrl(core\ConfigGlobal::getWeb() . '/apps/actividadestudios/controller/e43_2_mpdf.php');
$oHash->setCamposForm('id_nom!id_activ');
$h = $oHash->linkSinVal();


if (!empty($msg_err)) {
    echo $msg_err . "<br><br>";
}

$a_campos = ['oPosicion' => $oPosicion,
    'id_nom' => $Qid_nom,
    'h' => $h,
    'id_activ' => $Qid_activ,
    'dl_destino' => $dl_destino,
    'dl_origen' => $dl_origen,
    'nom' => $nom,
    'txt_nacimiento' => $txt_nacimiento,
    'txt_actividad' => $txt_actividad,
    'matriculas' => $matriculas,
    'aAsignaturasMatriculadas' => $aAsignaturasMatriculadas,
];

$oView = new core\View('actividadestudios/controller');
echo $oView->render('e43.phtml', $a_campos);
