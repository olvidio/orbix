<?php


// INICIO Cabecera global de URL de controlador *********************************
use src\certificados\domain\CertificadoEmitidoUpload;
use src\shared\domain\value_objects\DateTimeLocal;
use function core\is_true;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
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

    $error_txt = '';
    if ($tmpFilePath !== "") {
        $fp = fopen($tmpFilePath, 'rb');
        $contenido_doc = fread($fp, filesize($tmpFilePath));
        if ($contenido_doc === false) {
            $error_txt = sprintf(_("No se puede leer el archivo %s"), $fileName);
        } else {
            $certificadoUpload = new CertificadoEmitidoUpload();

            if (is_true($Qsolo_pdf)) {
                $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
                $rta = $certificadoUpload->uploadTxtFirmado($Qid_item, $contenido_doc);
                if (!is_object($rta)) {
                    $error_txt = $rta;
                }
            } else {
                $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
                $Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
                $Qfirmado = (string)filter_input(INPUT_POST, 'firmado');
                $Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');
                $Qidioma = (string)filter_input(INPUT_POST, 'idioma');
                $Qdestino = (string)filter_input(INPUT_POST, 'destino');
                $Qf_enviado = (string)filter_input(INPUT_POST, 'f_enviado');
                /* convertir las fechas a DateTimeLocal */
                $oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);
                $oF_enviado = DateTimeLocal::createFromLocal($Qf_enviado);

                if (is_true($Qfirmado)) {
                    $firmado = TRUE;
                } else {
                    $firmado = FALSE;
                }

                $rta = $certificadoUpload->uploadNew($Qid_nom, $contenido_doc, $Qidioma, $Qcertificado, $firmado, $oF_certificado, $oF_enviado, $Qdestino);

                if (!is_object($rta)) {
                    $error_txt = $rta;
                }
            }
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
