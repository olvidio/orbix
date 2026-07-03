<?php

use frontend\actividadestudios\helpers\ActividadestudiosDesplegableSupport;
use frontend\actividadestudios\helpers\FormMatriculasPayload;
use frontend\actividadestudios\helpers\ActividadestudiosRenderSupport;
use frontend\shared\helpers\ListNavSupport;

/**
 * Form de alta / edicion de una `Matricula` desde los dossiers
 * `matriculas_de_una_persona` (1303) y `matriculas_de_una_actividad` (3103).
 *
 * Sucesor de `apps/actividadestudios/controller/form_1303.php`. URL canonica.
 *
 * @param integer $_POST['id_pau']         id de la persona (cuando pau=p) o actividad
 * @param integer $_POST['id_activ']       id de la actividad
 * @param integer $_POST['id_nivel']       nivel seleccionado
 * @param integer $_POST['id_asignatura']  asignatura seleccionada
 * @param array   $_POST['sel']            checkbox: id_activ#id_asignatura
 */

use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
ListNavSupport::bootDossierChildRecordar($oPosicion);

$obj = 'actividadestudios\\model\\entity\\Matricula';

$Qid_nom = (int) filter_input(INPUT_POST, 'id_pau');
$Qid_activ = (int) filter_input(INPUT_POST, 'id_activ');
$Qid_nivel = (int) filter_input(INPUT_POST, 'id_nivel');
$Qid_asignatura = (int) filter_input(INPUT_POST, 'id_asignatura');

$a_sel = (array) filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
$camposMatricula = [
    'id_nom' => $Qid_nom,
    'id_pau' => $Qid_nom,
    'id_activ' => $Qid_activ,
    'id_nivel' => $Qid_nivel,
    'id_asignatura' => $Qid_asignatura,
];
if (!empty($a_sel)) {
    $camposMatricula['sel'] = $a_sel;
}

$d = FormMatriculasPayload::fromPayload(
    ActividadestudiosRenderSupport::stringKeyRow(PostRequest::getDataFromUrl('/src/actividadestudios/form_matriculas_de_una_persona_data', $camposMatricula))
);

$nom_activ = $d['nom_activ'];
$mod = $d['mod'];
$id_asignatura_real = $d['id_asignatura_real'];
$nombre_corto = $d['nombre_corto'];
$chk_preceptor = $d['chk_preceptor'];
$id_preceptor = $d['id_preceptor'];
$condicion_js = $d['condicion_js'];

$oDesplNiveles = new Desplegable();
$oDesplNiveles->setNombre('id_nivel');
$oDesplNiveles->setOpciones($d['oDesplNiveles_opciones']);
$oDesplNiveles->setBlanco(ActividadestudiosDesplegableSupport::blanco(1));
$oDesplNiveles->setAction('fnjs_cmb_opcional()');

$oDesplProfesores = new Desplegable();
if ($d['oDesplProfesores_opciones'] !== []) {
    $oDesplProfesores->setOpciones($d['oDesplProfesores_opciones']);
    $oDesplProfesores->setBlanco(ActividadestudiosDesplegableSupport::blanco(1));
    $oDesplProfesores->setNombre('id_preceptor');
    $oDesplProfesores->setOpcion_sel(ActividadestudiosDesplegableSupport::opcionSel($id_preceptor));
}

$oHash = new HashFront();
$oHash->setCamposNo('preceptor!id_preceptor');
$oHash->setCamposForm($d['camposForm']);
$oHash->setArraycamposHidden($d['a_camposHidden']);

$web = AppUrlConfig::getPublicAppBaseUrl();

$url_posibles_opcionales = $web . '/src/notas/posibles_opcionales_data';
$oHashOpcionales = new HashFront();
$oHashOpcionales->setUrl($url_posibles_opcionales);
$oHashOpcionales->setCamposForm('id_nom');
$h_posibles_opcionales = $oHashOpcionales->linkSinValParams();

$url_posibles_preceptores = $web . '/src/notas/posibles_preceptores_data';
$oHashPreceptores = new HashFront();
$oHashPreceptores->setUrl($url_posibles_preceptores);
$h_posibles_preceptores = $oHashPreceptores->linkSinValParams();

$url_matricula_nueva = $web . '/src/actividadestudios/matricula_nueva';
$url_matricula_editar = $web . '/src/actividadestudios/matricula_editar';

$a_campos = [
    'obj' => $obj,
    'oPosicion' => $oPosicion,
    'oHash' => $oHash,
    'url_posibles_opcionales' => $url_posibles_opcionales,
    'h_posibles_opcionales' => $h_posibles_opcionales,
    'url_posibles_preceptores' => $url_posibles_preceptores,
    'h_posibles_preceptores' => $h_posibles_preceptores,
    'url_matricula_nueva' => $url_matricula_nueva,
    'url_matricula_editar' => $url_matricula_editar,
    'condicion_js' => $condicion_js,
    'nom_activ' => $nom_activ,
    'id_asignatura_real' => $id_asignatura_real,
    'nombre_corto' => $nombre_corto,
    'oDesplNiveles' => $oDesplNiveles,
    'chk_preceptor' => $chk_preceptor,
    'id_preceptor' => $id_preceptor,
    'oDesplProfesores' => $oDesplProfesores,
    'mod' => $mod,
];

(new ViewNewPhtml('frontend\\actividadestudios\\controller'))
    ->renderizar('form_matriculas_de_una_persona.phtml', $a_campos);
