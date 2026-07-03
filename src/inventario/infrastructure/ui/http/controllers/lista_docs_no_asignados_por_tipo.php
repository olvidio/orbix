<?php

use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_tipo_doc = \src\shared\domain\helpers\FuncTablasSupport::inputInt($_POST, 'id_tipo_doc');
$error_txt = '';

// muestra los ctr que no tienen el documento.
/** @var TipoDocRepositoryInterface $TipoDocRepository */
$TipoDocRepository = DependencyResolver::get(TipoDocRepositoryInterface::class);
$oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
if ($oTipoDoc === null) {
    ContestarJson::enviar($error_txt, []);
    return;
}
$nom_doc = $oTipoDoc->getNom_doc();
$nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);

$cTodosUbis = $UbiInventarioRepository->getUbisInventario();
$a_valores = [];
$a_nom = [];
$i = 0;
foreach ($cTodosUbis as $oUbiDoc) {
    $id_ubi = $oUbiDoc->getId_ubi();
    $nom_ubi = $oUbiDoc->getNom_ubi();

    $cDocumentos = $DocumentoRepository->getDocumentos(['id_tipo_doc' => $Qid_tipo_doc, 'id_ubi' => $id_ubi]);
    if (count($cDocumentos) > 0) {
        continue;
    }
    $i++;
    $a_valores[$i][1] = $nom_ubi;
    //para poder ordenar
    $a_nom[$i] = $a_valores[$i][1];
}
array_multisort($a_nom, SORT_ASC, $a_valores);

$a_cabeceras = array(ucfirst(_("centro")));
$a_botones = [];

$data = ['a_cabeceras' => $a_cabeceras,
    'a_botones' => $a_botones,
    'a_valores' => $a_valores,
    'nombreDoc' => $nombreDoc,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
