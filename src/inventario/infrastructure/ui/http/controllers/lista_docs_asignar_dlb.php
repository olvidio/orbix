<?php

use function src\shared\domain\helpers\input_int;
use function src\shared\domain\helpers\input_string;
use src\shared\infrastructure\DependencyResolver;

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use src\shared\web\ContestarJson;

$Qid_tipo_doc = input_int($_POST, 'id_tipo_doc');
$a_sel = (array)filter_post('sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
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
$isNumerado = $oTipoDoc->isNumerado();

/** @var DocumentoRepositoryInterface $DocumentoRepository */
$DocumentoRepository = DependencyResolver::get(DocumentoRepositoryInterface::class);
if ($isNumerado) {
    //conseguir el último número.
    $ultimo = 0;
    $cDocumentos = $DocumentoRepository->getDocumentos(array('id_tipo_doc' => $Qid_tipo_doc, '_ordre' => 'num_reg DESC'));
    if (!empty($cDocumentos[0])) {
        $ultimo = $cDocumentos[0]->getNum_reg();
    }
}

/** @var UbiInventarioRepositoryInterface $UbiInventarioRepository */
$UbiInventarioRepository = DependencyResolver::get(UbiInventarioRepositoryInterface::class);
$cUbisInventario = $UbiInventarioRepository->getUbisInventario(['_ordre' => 'nom_ubi']);

/** @var LugarRepositoryInterface $LugarRepository */
$LugarRepository = DependencyResolver::get(LugarRepositoryInterface::class);

$a_valores = [];
$i = 0;
$sCamposForm = '';
foreach ($a_sel as $id_lugar) {
    $oLugar = $LugarRepository->findById((int) $id_lugar);
    if ($oLugar === null) {
        continue;
    }
    $id_ubi = $oLugar->getId_ubi();
    $oUbiInventario = $UbiInventarioRepository->findById((int) $id_ubi);
    if ($oUbiInventario === null) {
        continue;
    }
    $i++;
    if ($isNumerado) {
        $num = $ultimo + $i;
    } else {
        // es el número de ejemplares
        $cDocumentos = $DocumentoRepository->getDocumentos(array('id_tipo_doc' => $Qid_tipo_doc, 'id_ubi' => $id_ubi));
        if (!empty($cDocumentos[0])) {
            $num_ej = $cDocumentos[0]->getNum_ejemplares();
        } else {
            $num_ej = 1;
        }
        $num = $num_ej;
    }
    $a_valores[$i][1] = $oUbiInventario->getNom_ubi() . ' - ' . $oLugar->getNom_lugar();
    $a_valores[$i][2] = "<input type=text name='num_$id_lugar' size=5 value=$num>";
    $sCamposForm .= empty($sCamposForm) ? '' : '!';
    $sCamposForm .= "num_$id_lugar";
}

$data = [
    'a_valores' => $a_valores,
    'nombreDoc' => $nombreDoc,
    'isNumerado' => $isNumerado,
    'sCamposForm' => $sCamposForm,
];

// envía una Response
ContestarJson::enviar($error_txt, $data);
