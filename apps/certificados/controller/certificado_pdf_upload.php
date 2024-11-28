<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\CertificadoUpload;
use web\DateTimeLocal;
use function core\is_true;

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

function upload()
{
    $Qsolo_pdf = (integer)filter_input(INPUT_POST, 'solo_pdf');

    $input = 'certificado_pdf'; // the input name for the fileinput plugin
    if (empty($_FILES[$input])) {
        return [];
    }

    $tmpFilePath = $_FILES[$input]['tmp_name']; // the temp file path
    $fileName = $_FILES[$input]['name']; // the file name

    if ($tmpFilePath !== "") {
        $fp = fopen($tmpFilePath, 'rb');
        $contenido_doc = fread($fp, filesize($tmpFilePath));

        if (is_true($Qsolo_pdf)) {
            $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
            $error_txt = CertificadoUpload::uploadTxt($Qid_item, $contenido_doc);
        } else {
            $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
            $Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
            $Qfirmado = (string)filter_input(INPUT_POST, 'firmado');
            $Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');
            $Qidioma = (string)filter_input(INPUT_POST, 'idioma');
            /* convertir las fechas a DateTimeLocal */
            $oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
            if (is_true($Qfirmado)) {
                $firmado = TRUE;
            } else {
                $firmado = FALSE;
            }

            $error_txt = CertificadoUpload::uploadNew($contenido_doc, $Qid_nom, $Qcertificado, $firmado, $Qidioma, $oF_certificado);
        }
    } else {
        $error_txt = sprintf(_("No se puede subir el archivo %s"), $fileName);
    }

    if (!empty($error_txt)) {
        $jsondata['success'] = FALSE;
        $jsondata['mensaje'] = $error_txt;
    } else {
        $jsondata['success'] = TRUE;
    }

    return $jsondata;
}

