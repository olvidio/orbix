<?php

use frontend\notas\helpers\NotasFormSupport;
use frontend\notas\helpers\NotasPostInput;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\ListNavSupport;

require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();

/** @var string|list<string> $Qid_sel */
$Qid_sel = ListNavSupport::idSelFromPost();

$persona = NotasPostInput::personaFromSelPost();
$id_nom = $persona['id_nom'];

$navState = ListNavSupport::mergeSelectionIntoReturnParametros(
    ListNavSupport::buildReturnParametrosFromPost(),
    $Qid_sel,
    '',
);
$oPosicion->nav()->enter(
    (string) ($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $id_nom > 0 ? ['id_nom' => $id_nom] : [],
    $navState,
);
ListNavSupport::syncNavStateAt(
    $oPosicion,
    1,
    array_merge(
        ListNavSupport::buildPersonasSelectReturnParametros(),
        ListNavSupport::buildSelectionStatePatchFromPost(),
    ),
);

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
