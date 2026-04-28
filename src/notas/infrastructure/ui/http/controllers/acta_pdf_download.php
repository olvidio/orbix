<?php

use src\notas\domain\contracts\ActaRepositoryInterface;

require_once 'frontend/shared/global_header_front.inc';

$_POST = $_GET;

$Qacta = (string)filter_input(INPUT_GET, 'key');

if ($Qacta !== '') {
    $ActaRepository = $GLOBALS['container']->get(ActaRepositoryInterface::class);
    $oActa = $ActaRepository->findById($Qacta);
    $nombre_fichero = $Qacta . '.pdf';
    $doc = $oActa->getpdf();

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

echo json_encode(['error' => true]);
