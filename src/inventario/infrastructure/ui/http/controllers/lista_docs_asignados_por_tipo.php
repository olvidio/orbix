<?php

use src\inventario\domain\contracts\DocumentoRepositoryInterface;
use src\inventario\domain\contracts\LugarRepositoryInterface;
use src\inventario\domain\contracts\TipoDocRepositoryInterface;
use src\inventario\domain\contracts\UbiInventarioRepositoryInterface;
use web\ContestarJson;

$Qid_tipo_doc = (integer)filter_input(INPUT_POST, 'id_tipo_doc');
$error_txt = '';

$TipoDocRepository = $GLOBALS['container']->get(TipoDocRepositoryInterface::class);
$oTipoDoc = $TipoDocRepository->findById($Qid_tipo_doc);
$nom_doc = $oTipoDoc->getNom_doc();
$nombreDoc = empty($nom_doc) ? $oTipoDoc->getSigla() : $oTipoDoc->getSigla() . ' (' . $nom_doc . ')';

$DocumentoRepository = $GLOBALS['container']->get(DocumentoRepositoryInterface::class);
$cDocumentos = $DocumentoRepository->getDocumentos(['id_tipo_doc' => $Qid_tipo_doc]);

$LugarRepository = $GLOBALS['container']->get(LugarRepositoryInterface::class);
$UbiInventarioRepository = $GLOBALS['container']->get(UbiInventarioRepositoryInterface::class);
$i = 0;
foreach ($cDocumentos as $oDocumento) {
    $i++;
    $id_ubi = $oDocumento->getId_ubi();
    $id_lugar = $oDocumento->getId_lugar();
    $num_reg = $oDocumento->getNum_reg();
    $oUbiDoc = $UbiInventarioRepository->findById($id_ubi);
    $nom_ubi = $oUbiDoc->getNom_ubi();
    if (!empty($id_lugar)) {
        $oLugar = $LugarRepository->findById($id_lugar);
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
