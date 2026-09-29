<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\shared\security\HashB;
use src\shared\web\ContestarJson;

$Qid_lugar = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_lugar');
$Qid_grupo = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_grupo');
$Qid_equipaje = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_equipaje');
$Qid_item_egm = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_item_egm');
$error_txt = '';

$ctx_update_grupo = '';
if ($Qid_grupo > 0 && $Qid_equipaje > 0 && $Qid_lugar > 0) {
    $ctx_update_grupo = HashB::sign('equipajes_update_grupo', [
        'id_grupo' => $Qid_grupo,
        'id_equipaje' => $Qid_equipaje,
        'id_lugar' => $Qid_lugar,
    ]);
}
$ctx_add = '';
if ($Qid_item_egm > 0) {
    $ctx_add = HashB::sign('equipajes_add_doc', ['id_item_egm' => $Qid_item_egm]);
}
$ctx_eliminar_grupo = '';
if ($Qid_grupo > 0 && $Qid_equipaje > 0) {
    $ctx_eliminar_grupo = HashB::sign('equipajes_eliminar_grupo', [
        'id_grupo' => $Qid_grupo,
        'id_equipaje' => $Qid_equipaje,
    ]);
}

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos(['id_lugar' => $Qid_lugar]);

$oLugar = $LugarRepository->findById($Qid_lugar);
if ($oLugar === null) {
    ContestarJson::enviar($error_txt, [
        'a_valores' => [],
        'nombre_valija' => '',
        'ctx_update_grupo' => $ctx_update_grupo,
        'ctx_add' => $ctx_add,
        'ctx_eliminar_grupo' => $ctx_eliminar_grupo,
    ]);
    return;
}
$nombre_valija = $oLugar->getNom_lugar();
$d = 0;
$a_valores = [];
foreach ($cDocumentos as $oDocumento) {
    $d++;
    $id_doc = $oDocumento->getId_doc();
    $id_tipo_doc = $oDocumento->getId_tipo_doc();
    $identificador = $oDocumento->getIdentificador();
    $num_reg = $oDocumento->getNum_reg();

    $oTipoDoc = $TipoDocRepository->findById((int) $id_tipo_doc);
    if ($oTipoDoc === null) {
        continue;
    }
    $a_valores[$d]['sel'] = ['id' => $id_doc, 'select' => 'checked'];
    $a_valores[$d][1] = $oTipoDoc->getSigla() . " " . $oTipoDoc->getNom_doc();
    $a_valores[$d][2] = $identificador;
    $a_valores[$d][3] = $num_reg;
}

$data = [
    'a_valores' => $a_valores,
    'nombre_valija' => $nombre_valija,
    'ctx_update_grupo' => $ctx_update_grupo,
    'ctx_add' => $ctx_add,
    'ctx_eliminar_grupo' => $ctx_eliminar_grupo,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
