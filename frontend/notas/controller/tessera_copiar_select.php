<?php

use frontend\shared\model\ViewNewPhtml;
use frontend\shared\PostRequest;
use frontend\shared\web\Desplegable;
use frontend\shared\security\HashFront;
use frontend\shared\FrontBootstrap;

require_once __DIR__ . '/../helpers/notas_support.php';
require_once 'frontend/shared/FrontBootstrap.php';
require_once __DIR__ . '/../../shared/helpers/list_nav_support.php';

$oPosicion = FrontBootstrap::boot();
$Qrefresh = (int) filter_input(INPUT_POST, 'refresh');

$restored = list_nav_restore_selection_from_stack_post();
/** @var string|list<string> $Qid_sel */
$Qid_sel = !list_nav_id_sel_is_empty($restored['id_sel']) ? $restored['id_sel'] : list_nav_id_sel_from_post();

$stackFromPost = list_nav_stack_from_post();
if ($stackFromPost !== 0) {
    list_nav_boot_list_page_after_stack_return($oPosicion, $stackFromPost);
} else {
    list_nav_boot_recordar($oPosicion, $Qrefresh);
}
list_nav_persist_recordar_entry($oPosicion, list_nav_merge_selection_into_return_parametros(
    list_nav_build_return_parametros_from_post(),
    $Qid_sel,
    $restored['scroll_id'],
));

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
