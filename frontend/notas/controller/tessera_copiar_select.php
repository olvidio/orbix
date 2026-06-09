<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/notas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';

$oPosicion = FrontBootstrap::boot();
if (isset($_POST['stack'])) {
    $stack2 = (int)filter_input(INPUT_POST, 'stack', FILTER_SANITIZE_NUMBER_INT);
    if ($stack2 !== 0) {
        $oPosicion2 = new frontend\shared\web\Posicion();
        if ($oPosicion2->goStack($stack2)) {
            $Qid_sel = $oPosicion2->getParametro('id_sel');
            $Qscroll_id = $oPosicion2->getParametro('scroll_id');
            $oPosicion2->olvidar($stack2);
        }
    }
}

$persona = notas_persona_from_sel_post();
$id_nom = $persona['id_nom'];

$data = PostRequest::getDataFromUrl('/src/notas/tessera_copiar_select_data', [
    'id_nom' => $id_nom,
]);

$nom = tessera_imprimir_string($data['nom'] ?? '');
$aPosibles = notas_desplegable_opciones($data['posibles_personas'] ?? []);

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
