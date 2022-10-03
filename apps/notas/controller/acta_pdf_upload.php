<?php

use notas\model\entity\Acta;

// INICIO Cabecera global de URL de controlador *********************************
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
    $Qacta = (string)\filter_input(INPUT_POST, 'acta_num');

    $error_txt = '';
    $input = 'acta_pdf'; // the input name for the fileinput plugin
    if (empty($_FILES[$input])) {
        return [];
    } else {
        $tmpFilePath = $_FILES[$input]['tmp_name']; // the temp file path
        $fileName = $_FILES[$input]['name']; // the file name
        //$fileSize = $_FILES[$input]['size']; // the file size

        //Make sure we have a file path
        if ($tmpFilePath != "") {

            $fp = fopen($tmpFilePath, 'rb');
            $contenido_doc = fread($fp, filesize($tmpFilePath));

            $oActa = new Acta($Qacta);
            $oActa->DBCarregar();
            $oActa->setPdf($contenido_doc);

            if ($oActa->DBGuardar() === FALSE) {
                $error_txt .= $oActa->getErrorTxt();
            }
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
}