<?php

use src\shared\infrastructure\DependencyResolver;
use src\shared\domain\helpers\FuncTablasSupport;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_tipo_doc = FuncTablasSupport::inputInt($_POST, 'id_tipo_doc');
$error_txt = '';

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
$cDocumentos = $DocumentoRepository->getDocumentos(['id_tipo_doc' => $Qid_tipo_doc]);

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);
/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
$a_valores = [];
$a_nom = [];
$i = 0;
foreach ($cDocumentos as $oDocumento) {
    $i++;
    $id_ubi = $oDocumento->getId_ubi();
    $id_lugar = $oDocumento->getId_lugar();
    $num_reg = $oDocumento->getNum_reg();
    $oUbiDoc = $UbiInventarioRepository->findById((int) $id_ubi);
    if ($oUbiDoc === null) {
        continue;
    }
    $nom_ubi = $oUbiDoc->getNom_ubi();
    if (!empty($id_lugar)) {
        $oLugar = $LugarRepository->findById((int) $id_lugar);
        if ($oLugar === null) {
            continue;
        }
        $nom_ubi .= " --> " . $oLugar->getNom_lugar();
    }
    $a_valores[$i][1] = $nom_ubi;
    $a_valores[$i][2] = $num_reg;
    //para poder ordenar
    $a_nom[$i] = $a_valores[$i][1];
}
array_multisort($a_nom, SORT_ASC, $a_valores);

$a_cabeceras = array(ucfirst(_("centro - lugar")), ucfirst(_("número")));
$a_botones = [];

$data = ['a_cabeceras' => $a_cabeceras,
    'a_botones' => $a_botones,
    'a_valores' => $a_valores,
    'nombreDoc' => $nombreDoc,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
