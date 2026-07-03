<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\notas\helpers\NotasPostInput;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$restored = \frontend\shared\helpers\ListNavSupport::restoreSelectionFromStackPost();
/** @var string|list<string> $Qid_sel */
$Qid_sel = !\frontend\shared\helpers\ListNavSupport::idSelIsEmpty($restored['id_sel']) ? $restored['id_sel'] : \frontend\shared\helpers\ListNavSupport::idSelFromPost();

$stackFromPost = \frontend\shared\helpers\ListNavSupport::stackFromPost();
if ($stackFromPost !== 0) {
    \frontend\shared\helpers\ListNavSupport::bootListPageAfterStackReturn($oPosicion, $stackFromPost);
} else {
    \frontend\shared\helpers\ListNavSupport::bootRecordar($oPosicion, $Qrefresh);
}
\frontend\shared\helpers\ListNavSupport::persistRecordarEntry($oPosicion, \frontend\shared\helpers\ListNavSupport::mergeSelectionIntoReturnParametros(
    \frontend\shared\helpers\ListNavSupport::buildReturnParametrosFromPost(),
    $Qid_sel,
    $restored['scroll_id'],
));

$persona = NotasPostInput::personaFromSelPost();
$id_nom = $persona['id_nom'];

$data = PostRequest::getDataFromUrl('/src/notas/tessera_copiar_select_data', [
    'id_nom' => $id_nom,
]);

$nom = \frontend\shared\helpers\PayloadCoercion::string($data['nom'] ?? '');
$aPosibles = NotasFormSupport::desplegableOpciones($data['posibles_personas'] ?? []);

$oDesplPersonas = new Desplegable();
$oDesplPersonas->setNombre('id_nom_dst');
$oDesplPersonas->setBlanco('true');
$oDesplPersonas->setOpciones($aPosibles);

$oHash = new HashFront();
$oHash->setCamposForm('id_nom_dst');
$oHash->setArraycamposHidden(['id_nom_org' => $id_nom]);

$a_campos = [
    'oPosicion' => $oPosicion,
    'nom' => $nom,
    'oDesplPersonas' => $oDesplPersonas,
    'oHash' => $oHash,
];

$oView = new ViewNewPhtml('frontend\\notas\\controller');
$oView->renderizar('tessera_copiar_select.phtml', $a_campos);
