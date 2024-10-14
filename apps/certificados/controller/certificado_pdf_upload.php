<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\entity\Certificado;
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use personas\model\entity\Persona;
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

    $error_txt = '';
    $input = 'certificado_pdf'; // the input name for the fileinput plugin
    if (is_true($Qsolo_pdf)) {
        $Qid_item = (integer)filter_input(INPUT_POST, 'id_item');
        $tmpFilePath = $_FILES[$input]['tmp_name']; // the temp file path
        $fileName = $_FILES[$input]['name']; // the file name
        //$fileSize = $_FILES[$input]['size']; // the file size

        //Make sure we have a file path
        if ($tmpFilePath != "") {

            $fp = fopen($tmpFilePath, 'rb');
            $contenido_doc = fread($fp, filesize($tmpFilePath));

            $certificadoRepository = new CertificadoRepository();
            $oCertificado = $certificadoRepository->findById($Qid_item);

            $oCertificado->setDocumento($contenido_doc);
            $oCertificado->setFirmado(TRUE);

            if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
                $error_txt .= $certificadoRepository->getErrorTxt();
            }

        } else {
            $error_txt .= sprintf(_("No se puede subir el archivo %s"), $fileName);
        }

    } else {
        $Qid_nom = (integer)filter_input(INPUT_POST, 'id_nom');
        $Qcertificado = (string)filter_input(INPUT_POST, 'certificado');
        $Qfirmado = (string)filter_input(INPUT_POST, 'firmado');
        $Qf_certificado = (string)filter_input(INPUT_POST, 'f_certificado');
        $Qidioma = (string)filter_input(INPUT_POST, 'idioma');
        /* convertir las fechas a DateTimeLocal */
        $oF_certificado = DateTimeLocal::createFromLocal($Qf_certificado);

        $oPersona = Persona::NewPersona($Qid_nom);
        $apellidos_nombre = $oPersona->getApellidosNombre();
        $nom = $apellidos_nombre;

        $destino = ConfigGlobal::mi_region();

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
                $oCertificado->setNom($nom);
                $oCertificado->setDestino($destino);
                $oCertificado->setIdioma($Qidioma);
                $oCertificado->setCertificado($Qcertificado);
                if (is_true($Qfirmado)) {
                    $firmado = TRUE;
                } else {
                    $firmado = FALSE;
                }
                $oCertificado->setFirmado($firmado);
                $oCertificado->setEsquema_emisor(FALSE);
                $oCertificado->setF_certificado($oF_certificado);

                if ($certificadoRepository->Guardar($oCertificado) === FALSE) {
                    $error_txt .= $certificadoRepository->getErrorTxt();
                }

            } else {
                $error_txt .= sprintf(_("No se puede subir el archivo %s"), $fileName);
            }
        }
    }
    if (!empty($error_txt)) {
        $jsondata['success'] = FALSE;
        $jsondata['mensaje'] = $error_txt;
    } else {
        $jsondata['success'] = TRUE;
    }

    return $jsondata;
}