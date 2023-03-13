<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\entity\Certificado;
use certificados\domain\repositories\CertificadoRepository;
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
    $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
    $Qcertificado = (string)filter_input(INPUT_POST, 'certificado_num');
    $Qcopia = (string)filter_input(INPUT_POST, 'copia');
    $Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');

    /* convertir las fechas a DateTimeLocal */
    $oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);


    $error_txt = '';
    $input = 'certificado_pdf'; // the input name for the fileinput plugin
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

            $certificadoRepository = new CertificadoRepository();
            $id_item = $certificadoRepository->getNewId_item();
            $oCertificado = new Certificado();
            $oCertificado->setId_item($id_item);
            $oCertificado->setDocumento($contenido_doc);
            $oCertificado->setId_nom($Qid_nom);
            $oCertificado->setCertificado($Qcertificado);
            if (is_true($Qcopia)) {
                $copia = TRUE;
            } else {
                $copia = FALSE;
            }
            $oCertificado->setCopia($copia);
            $oCertificado->setPropio(FALSE);
            $oCertificado->setF_certificado($oF_certificado);

            if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
                $error_txt .= $certificadoRepository->getErrorTxt();
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