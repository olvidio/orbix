<?php
namespace frontend\personas\controller;

use frontend\personas\helpers\PersonasPayload;
use frontend\personas\helpers\PersonasPostInput;
use frontend\dossiers\helpers\DossiersListaRender;
use frontend\shared\PostRequest;
use frontend\shared\config\AppUrlConfig;
use frontend\shared\model\ViewNewPhtml;
use frontend\shared\security\HashFront;
use frontend\shared\web\Posicion;
use frontend\shared\FrontBootstrap;
use frontend\shared\helpers\PayloadCoercion;
use frontend\shared\helpers\ListNavSupport;

/**
 * Pantalla de cabecera de una persona (datos basicos + acceso a dossiers y ficha).
 *
 * Migrado desde `apps/personas/controller/home_persona.php` (slice 3 del
 * modulo `personas`). Refactor conforme a `refactor.md`: la resolucion de
 * repositorios, el acceso a centros, telecos, traduccion del nivel_stgr y la
 * normalizacion de `Qobj_pau` se han movido a
 * `src/personas/application/HomePersonaData.php` tras el endpoint
 * `/src/personas/home_persona_data`. Este controlador no importa clases `src\`.
 */
require_once 'frontend/shared/FrontBootstrap.php';
$oPosicion = FrontBootstrap::boot();
/** @var Posicion $oPosicion */

$ids = PersonasPostInput::idFromSelPost();
$id_nom = $ids['id_nom'];
$id_tabla = $ids['id_tabla'];

$Qobj_pau = (string)filter_input(INPUT_POST, 'obj_pau');

$navIdentity = $id_nom > 0 ? ['id_nom' => $id_nom, 'id_tabla' => $id_tabla] : [];
$navState = ListNavSupport::mergeSelectionForRecordar(
    ListNavSupport::buildReturnParametrosFromPost(),
    ListNavSupport::idSelFromPost(),
    ListNavSupport::scrollIdFromPost(),
);
$oPosicion->nav()->enter(
    PayloadCoercion::string($_SERVER['PHP_SELF'] ?? ''),
    '#main',
    $navIdentity,
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

PersonasPostInput::sessionGoToSetTabla($Qobj_pau);

$pau = 'p';

$campos = [
    'id_nom' => $id_nom,
    'id_tabla' => $id_tabla,
    'obj_pau' => $Qobj_pau,
];

$data = PostRequest::getDataFromUrl('/src/personas/home_persona_data', $campos, false);
$aviso = '';
if (!empty($data['error'])) {
    $errorHtml = PostRequest::stripInternalCallProvenance(\frontend\shared\helpers\PayloadCoercion::string($data['error']));
    if (
        str_contains($errorHtml, _('persona no válida'))
        || str_contains($errorHtml, 'persona no válida')
        || str_contains($errorHtml, _('Delegaciones no dadas de alta'))
        || str_contains($errorHtml, 'Delegaciones no dadas de alta')
    ) {
        $aviso = $errorHtml;
        $data = [];
    } else {
        echo $errorHtml;
        return;
    }
}
$payload = PersonasPayload::postPayload($data);
$home = PersonasPayload::homeFromPayload($payload, $Qobj_pau, $aviso);
$Qobj_pau = $home['Qobj_pau'];
$nom = $home['titulo'];
$dl = $home['dl'];
$f_nacimiento = $home['f_nacimiento'];
$situacion = $home['situacion'];
$f_situacion = $home['f_situacion'];
$profesion = $home['profesion'];
$stgr = $home['stgr'];
$observ = $home['observ'];
$ctr = $home['ctr'];
$telfs = $home['telfs'];
$mails = $home['mails'];
$aviso = $home['aviso'];

$a_parametros = ['pau' => $pau, 'id_nom' => $id_nom, 'obj_pau' => $Qobj_pau];
$base = AppUrlConfig::getPublicAppBaseUrl();
$gohome = HashFront::link($base . '/frontend/personas/controller/home_persona.php?' . http_build_query($a_parametros));
$go_ficha = HashFront::link($base . '/frontend/personas/controller/personas_editar.php?' . http_build_query($a_parametros));
$a_parametros_dossier = ['pau' => $pau, 'id_pau' => $id_nom, 'obj_pau' => $Qobj_pau];
$godossiers = HashFront::link($base . '/frontend/dossiers/controller/dossiers_ver.php?' . http_build_query($a_parametros_dossier));

$lista_dossiers_html = \frontend\dossiers\helpers\DossiersListaRender::render($pau, $id_nom, $Qobj_pau);

$a_campos = [
    'oPosicion' => $oPosicion,
    'gohome' => $gohome,
    'godossiers' => $godossiers,
    'go_ficha' => $go_ficha,
    'titulo' => $nom,
    'telfs' => $telfs,
    'mails' => $mails,
    'stgr' => $stgr,
    'profesion' => $profesion,
    'celebra' => '',
    'santo' => '',
    'f_nacimiento' => $f_nacimiento,
    'dl' => $dl,
    'ctr' => $ctr,
    'pau' => $pau,
    'id_pau' => $id_nom,
    'Qobj_pau' => $Qobj_pau,
    'aviso' => $aviso,
    'lista_dossiers_html' => $lista_dossiers_html,
];

$oView = new ViewNewPhtml('frontend\personas\controller');
$oView->renderizar('home_persona.phtml', $a_campos);
