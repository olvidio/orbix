<?php
// para que funcione bien la seguridad
$_POST = $_GET;

// INICIO Cabecera global de URL de controlador *********************************
use src\certificados\application\repositories\CertificadoRepository;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// El download es via GET!!!";

$Qid_item = (int)filter_input(INPUT_GET, 'key');

if (!empty($Qid_item)) {
    $CertificadoPublicRepository = new CertificadoRepository();
    $oCertificado = $CertificadoPublicRepository->findById($Qid_item);
    $nombre_fichero = $oCertificado->getCertificado();
    $nombre_fichero .= '.pdf';
    $doc = $oCertificado->getDocumento();

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
    ob_clean();
    flush();
    echo $doc;

    exit();
}

$outData = "{'error': true}";
echo json_encode($outData);// return json data