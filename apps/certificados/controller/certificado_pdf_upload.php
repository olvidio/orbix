<?php


// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\entity\CertificadoDl;
use certificados\domain\repositories\CertificadoDlRepository;
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
    $Qid_dossier = (integer)filter_input(INPUT_POST, 'id_dossier');
    $local = empty($Qid_dossier) ? FALSE : TRUE;

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

            if ($local) {
                $certificadoDlRepository = new CertificadoDlRepository();
            } else {
                $certificadoDlRepository = new CertificadoRepository();
            }
            $oCertificadoDl = $certificadoDlRepository->findById($Qid_item);

            $oCertificadoDl->setDocumento($contenido_doc);
            $oCertificadoDl->setFirmado(TRUE);

            if ($certificadoDlRepository->Guardar($oCertificadoDl) === FALSE) {
                $error_txt .= $certificadoDlRepository->getErrorTxt();
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
            if ($tmpFilePath !== "") {

                $fp = fopen($tmpFilePath, 'rb');
                $contenido_doc = fread($fp, filesize($tmpFilePath));

                if ($local) {
                    $certificadoDlRepository = new CertificadoDlRepository();
                } else {
                    $certificadoDlRepository = new CertificadoRepository();
                }
                $id_item = $certificadoDlRepository->getNewId_item();
                $oCertificadoDl = new CertificadoDl();
                $oCertificadoDl->setId_item($id_item);
                $oCertificadoDl->setDocumento($contenido_doc);
                $oCertificadoDl->setId_nom($Qid_nom);
                $oCertificadoDl->setNom($nom);
                $oCertificadoDl->setDestino($destino);
                $oCertificadoDl->setIdioma($Qidioma);
                $oCertificadoDl->setCertificado($Qcertificado);
                if (is_true($Qfirmado)) {
                    $firmado = TRUE;
                } else {
                    $firmado = FALSE;
                }
                $oCertificadoDl->setFirmado($firmado);
                $oCertificadoDl->setEsquema_emisor(ConfigGlobal::mi_region_dl());
                $oCertificadoDl->setF_certificado($oF_certificado);

                if ($certificadoDlRepository->Guardar($oCertificadoDl) === FALSE) {
                    $error_txt .= $certificadoDlRepository->getErrorTxt();
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