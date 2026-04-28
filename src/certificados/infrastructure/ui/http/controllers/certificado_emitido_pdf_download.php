<?php

use src\certificados\domain\contracts\CertificadoEmitidoRepositoryInterface;

require_once 'frontend/shared/global_header_front.inc';

$_POST = $_GET;

$Qid_item = (int)filter_input(INPUT_GET, 'key');

if ($Qid_item > 0) {
    $certificadoEmitidoRepository = $GLOBALS['container']->get(CertificadoEmitidoRepositoryInterface::class);
    $oCertificadoEmitido = $certificadoEmitidoRepository->findById($Qid_item);
    $nombre_fichero = $oCertificadoEmitido->getCertificado();
    $nombre_fichero .= '.pdf';
    $doc = $oCertificadoEmitido->getDocumento();

    $ctype = 'application/octet-stream';

    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: private', false);
    header('Content-Type: application/force-download');
    header('Content-Type: application/download', false);
    header('Content-Type: ' . $ctype);
    header('Content-disposition: attachment; filename="' . $nombre_fichero . '"');

    ob_start();
    ob_clean();
    flush();
    echo $doc;

    exit();
}

$outData = "{'error': true}";
echo json_encode($outData);
