<?php

use frontend\shared\model\ViewNewPhtml;
use src\dossiers\domain\contracts\TipoDossierRepositoryInterface;
use web\Hash;

/**
 * Página de selección de los dossiers cuyos permisos deseo visualizar
 * o modificar. Hay que pasarle la variable $tipo, para que sólo aparezca
 * la lista de selección de los dossiers que interesen.
 *
 * Migrado desde `apps/dossiers/controller/perm_dossiers.php`.
 */
// INICIO Cabecera global de URL de controlador *********************************
require_once("apps/core/global_header.inc");
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ********************************

/*
 * listado de todos los dossiers de un tipo, según sea $tipo="p", "a" ó "u"
 * en caso de no pasarla, por defecto lista los de personas:
 */
$Qtipo = (string)filter_input(INPUT_POST, 'tipo');
$tipo = empty($Qtipo) ? 'p' : $Qtipo;

$TipoDossierRepository = $GLOBALS['container']->get(TipoDossierRepositoryInterface::class);
$cTipoDossiers = $TipoDossierRepository->getTiposDossiers(array('tabla_from' => $tipo, '_ordre' => 'id_tipo_dossier'));

$a_filas = [];
foreach ($cTipoDossiers as $oTipoDossier) {
    $id_tipo_dossier = $oTipoDossier->getId_tipo_dossier();
    $depende_modificar = $oTipoDossier->isDepende_modificar();
    $descripcion = $oTipoDossier->getDescripcion();

    $pagina = Hash::link('frontend/dossiers/controller/perm_dossier_ver.php?' . http_build_query(array(
        'id_tipo_dossier' => $id_tipo_dossier,
        'depende_modificar' => $depende_modificar,
        'tipo' => $tipo,
    )));
    $a_filas[] = [
        'descripcion' => $descripcion,
        'pagina' => $pagina,
    ];
}

$oView = new ViewNewPhtml('frontend\\dossiers\\controller');
$oView->renderizar('perm_dossiers.phtml', [
    'a_filas' => $a_filas,
]);
