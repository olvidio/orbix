<?php

// INICIO Cabecera global de URL de controlador *********************************
use certificados\domain\repositories\CertificadoRepository;
use core\ConfigGlobal;
use core\ServerConf;
use personas\model\entity\Persona;
use web\Hash;
use function core\is_true;

require_once("apps/core/global_header.inc");
// Archivos requeridos por esta url **********************************************

// Crea los objetos de uso global **********************************************
require_once("apps/core/global_object.inc");
// FIN de  Cabecera global de URL de controlador ****************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

$CertificadoRepository = new CertificadoRepository();
$oCertificado = $CertificadoRepository->findById($Qid_item);

$id_nom = $oCertificado->getId_nom();
$certificado = $oCertificado->getCertificado();
$f_certificado = $oCertificado->getF_certificado()->getFromLocal();
$copia = $oCertificado->isCopia();
if (is_true($copia)) {
    $chk_copia = 'checked';
} else {
    $chk_copia = '';
}
$propio = $oCertificado->isPropio();

$oPersona = Persona::NewPersona($id_nom);
$apellidos_nombre = $oPersona->getApellidosNombre();

$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado_num!copia!f_certificado');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!copia');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden(['id_item' => $Qid_item, 'id_nom' => $id_nom, 'certificado_old' => $certificado]);

// borrar los posibles fichero antiguos de /tmp
$dir_tmp = ServerConf::DIR .'/log/tmp/';
$cmd_shell = "find $dir_tmp -mtime +1 -delete";
shell_exec($cmd_shell);

// Descargar el pdf en un file en log/
$filename_sin_barra = str_replace('/', '_',$certificado);
$filename_sin_espacio = str_replace(' ', '_',$filename_sin_barra);
$filename_pdf = ServerConf::DIR .'/log/tmp/' . $filename_sin_espacio . '.pdf';
$filename_pdf_web = ConfigGlobal::getWeb() .'/log/tmp/' . $filename_sin_espacio . '.pdf';
$content = $oCertificado->getDocumento();
file_put_contents($filename_pdf, $content);

$a_campos = ['oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $apellidos_nombre,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'chk_copia' => $chk_copia,
        // para ver pdf
    'filename_pdf' => $filename_pdf_web,
];

$oView = new core\ViewTwig('certificados/controller');
$oView->renderizar('certificado_ver.html.twig', $a_campos);