<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_lugar = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_lugar');
$error_txt = '';

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos(['id_lugar' => $Qid_lugar]);

$oLugar = $LugarRepository->findById($Qid_lugar);
if ($oLugar === null) {
    ContestarJson::enviar($error_txt, []);
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
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
