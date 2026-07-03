<?php
namespace frontend\personas\controller;

use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\web\Posicion;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

/**
 * Formulario de busqueda de personas.
 *
 * Migrado desde `apps/personas/controller/personas_que.php` (slice 4).
 *
 * La rama `$Qque === 'telf'` (que apuntaba a un inexistente
 * `personas_select_telf.php`) se ha eliminado por enlace muerto.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */
if (isset($_POST['stack'])) {
    $stack = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack !== 0) {
        $oPosicion2 = new Posicion();
        if ($oPosicion2->goStack($stack)) {
            $oPosicion2->olvidar($stack);
        }
    }
}
ListNavSupport::bootRecordar($oPosicion);
ListNavSupport::persistRecordarEntry($oPosicion, ListNavSupport::mergeSelectionIntoReturnParametros(ListNavSupport::buildReturnParametrosFromPost(), ListNavSupport::idSelFromPost(), ListNavSupport::scrollIdFromPost()));



$Qna = (string)filter_input(INPUT_POST, 'na');
$Qque = (string)filter_input(INPUT_POST, 'que');
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$Qtabla = (string)filter_input(INPUT_POST, 'tabla');
$Qes_sacd = (int)filter_input(INPUT_POST, 'es_sacd');
$Qexacto = (string)filter_input(INPUT_POST, 'exacto');
$Qcmb = (string)filter_input(INPUT_POST, 'cmb');
$Qnombre = (string)filter_input(INPUT_POST, 'nombre');
$Qapellido1 = (string)filter_input(INPUT_POST, 'apellido1');
$Qapellido2 = (string)filter_input(INPUT_POST, 'apellido2');
$Qcentro = (string)filter_input(INPUT_POST, 'centro');

if (!empty($Qtabla)) {
    $nom_tabla = substr($Qtabla, 2);
    if ($nom_tabla === "de_paso") {
        $nom_tabla = $Qna . " " . $nom_tabla;
    }
} else {
    $Qtabla = "personas";
    $nom_tabla = ucfirst(_("todos"));
}

$action = AppUrlConfig::getPublicAppBaseUrl() . '/frontend/personas/controller/personas_select.php';

$oHash = new HashFront();
$oHash->setCamposForm('nombre!apellido1!apellido2!centro!exacto!cmb');
$oHash->setcamposNo('exacto!cmb');
$oHash->setArraycamposHidden([
    'tipo' => $Qtipo,
    'tabla' => $Qtabla,
    'na' => $Qna,
    'que' => $Qque,
    'es_sacd' => $Qes_sacd,
]);

$a_campos = [
    'oHash' => $oHash,
    'action' => $action,
    'nom_tabla' => $nom_tabla,
    'chk_exacto_0' => empty($Qexacto) ? 'checked' : '',
    'chk_exacto_1' => empty($Qexacto) ? '' : 'checked',
    'chk_cmb' => empty($Qcmb) ? '' : 'checked="checked"',
    'tabla' => $Qtabla,
    'tipo' => $Qtipo,
    'nombre' => $Qnombre,
    'apellido1' => $Qapellido1,
    'apellido2' => $Qapellido2,
    'centro' => $Qcentro,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('personas_que.phtml', $a_campos);
