<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\CertificadoDlUpload;
use web\DateTimeLocal;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// Crea los objetos por esta url  **********************************************

// FIN de  Cabecera global de URL de controlador ********************************

// example of a PHP server code that is called in `uploadUrl` above
// file-upload-batch script
header('Content-Type: application/json'); // set json response headers
$outData = upload(); // a function to upload the bootstrap-fileinput files
echo json_encode($outData); // return json data
exit(); // terminate

function upload(): array
{
    $error_txt = '';
    $input = 'certificado_pdf'; // the input name for the fileinput plugin

    if (empty($_FILES[$input])) {
        return [];
    }

    $tmpFilePath = $_FILES[$input]['tmp_name']; // the temp file path
    $fileName = $_FILES[$input]['name']; // the file name

    //Make sure we have a file path
    if ($tmpFilePath !== "") {
        $fp = fopen($tmpFilePath, 'rb');
        $contenido_doc = fread($fp, filesize($tmpFilePath));

        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
        $Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
        $Qfirmado = (string)filter_input(INPUT_POST, 'firmado');
        $Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');
        $Qidioma = (string)filter_input(INPUT_POST, 'idioma');
        $Qf_recibido = (string)filter_input(INPUT_POST, 'f_recibido');
        /* convertir las fechas a DateTimeLocal */
        $oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
        $oF_recibido = DateTimeLocal::createFromLocal($Qf_recibido);

        $error_txt = CertificadoDlUpload::uploadNew($Qid_nom, $Qid_item, $contenido_doc, $Qidioma, $Qcertificado, $Qfirmado, $oF_certificado, $oF_recibido);
    } else {
        $error_txt .= sprintf(_("No se puede subir el archivo %s"), $fileName);
    }

    if (!empty($error_txt)) {
        $jsondata['success'] = FALSE;
        $jsondata['mensaje'] = $error_txt;
    } else {
        $jsondata['success'] = TRUE;
    }

    return $jsondata;
}

