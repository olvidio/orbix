<?php
// para que funcione bien la seguridad
use src\certificados\domain\contracts\CertificadoRecibidoRepositoryInterface;

$_POST = $_GET;

// INICIO Cabecera global de URL de controlador *********************************

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

// FIN de  Cabecera global de URL de controlador ********************************

// El download es via GET!!!";

$Qid_item = (int)filter_input(INPUT_GET, 'key');

if (!empty($Qid_item)) {
    $certificadoRecibidoRepository = $GLOBALS['container']->get(CertificadoRecibidoRepositoryInterface::class);
    $oCertificadoRecibido = $certificadoRecibidoRepository->findById($Qid_item);
    $nombre_fichero = $oCertificadoRecibido->getCertificado();
    $nombre_fichero .= '.pdf';
    $doc = $oCertificadoRecibido->getDocumento();

    $ctype = "application/octet-stream";

    header('Content-Description: File Transfer');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header("Pragma: public"); // required
    header("Expires: 0");
    header("Cache-Control: private", false); // required for certain browsers
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
echo json_encode($outData);// return json data