<?php

// INICIO Cabecera global de URL de controlador *********************************
use core\ConfigGlobal;
use core\ServerConf;
use frontend\shared\model\ViewNewTwig;
use frontend\shared\PostRequest;
use web\Desplegable;
use web\Hash;
use function core\is_true;

// Crea los objetos de uso global **********************************************
require_once("frontend/shared/global_header_front.inc");
// FIN de  Cabecera global de URL de controlador ********************************

$a_sel = (array)filter_input(INPUT_POST, 'sel', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

if (!empty($a_sel)) { //vengo de un checkbox
    $Qid_item = (integer)strtok($a_sel[0], "#");
    // el scroll id es de la página anterior, hay que guardarlo allí
    $oPosicion->addParametro('id_sel', $a_sel, 1);
    $scroll_id = (integer)filter_input(INPUT_POST, 'scroll_id');
    $oPosicion->addParametro('scroll_id', $scroll_id, 1);
}

/////////// Consulta al backend ///////////////////
$url_backend = '/src/certificados/infrastructure/controllers/certificado_emitido_ver_datos.php';
$a_campos_backend = [ 'id_item' => $Qid_item ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);


$id_nom = $data['id_nom'];
$nom = $data['nom'];
$idioma = $data['idioma'];
$destino = $data['destino'];
$certificado = $data['certificado'];
$f_certificado = $data['f_certificado'];
$f_enviado = $data['f_enviado'];
$firmado = $data['firmado'];
if (is_true($firmado)) {
    $chk_firmado = 'checked';
} else {
    $chk_firmado = '';
}
$content_pdf = base64_decode($data['content']);

$apellidos_nombre = $data['apellidos_nombre'];
$nom = $data['nom'];


//Idiomas (blanco para latín)
/////////// Consulta al backend ///////////////////
$url_backend = '/src/shared/infrastructure/controllers/locales_posibles.php';
$a_campos_backend = [ 'id_nom' => $id_nom ];
$data = PostRequest::getDataFromUrl($url_backend, $a_campos_backend);

$a_locales = $data['a_locales'];

$oDesplIdiomas = new Desplegable('idioma', $a_locales, $idioma, true);


$oHashCertificadoPdf = new Hash();
$oHashCertificadoPdf->setCamposForm('certificado_pdf!certificado!firmado!destino!f_certificado!idioma!nom!f_enviado');
$oHashCertificadoPdf->setCamposNo('certificado_pdf!firmado');
//cambio el nombre, porque tiene el mismo id en el otro formulario
$oHashCertificadoPdf->setArrayCamposHidden(
    [
        'id_item' => $Qid_item,
        'id_nom' => $id_nom,
        'certificado_old' => $certificado
    ]);

// borrar los posibles fichero antiguos de /tmp
$dir_tmp = ServerConf::DIR . '/log/tmp/';
$cmd_shell = "find $dir_tmp -mtime +1 -delete";
shell_exec($cmd_shell);

// Descargar el pdf en un file en log/
$filename_sin_barra = str_replace('/', '_', $certificado);
$filename_sin_espacio = str_replace(' ', '_', $filename_sin_barra);
$filename_pdf = ServerConf::DIR . '/log/tmp/' . $filename_sin_espacio . '.pdf';
if (($file_handle = @fopen($filename_pdf, 'wb')) !== false) {
    fwrite($file_handle, $content_pdf);
    fclose($file_handle);
    //file_put_contents($filename_pdf, $content_pdf);
    $filename_pdf_web = ConfigGlobal::getWeb() . '/log/tmp/' . $filename_sin_espacio . '.pdf';
} else {
    $filename_pdf_web = '';
}

$a_campos = ['oPosicion' => $oPosicion,
    'oHashCertificadoPdf' => $oHashCertificadoPdf,
    'ApellidosNombre' => $apellidos_nombre,
    'nom' => $nom,
    'idioma' => $idioma,
    'oDesplIdiomas' => $oDesplIdiomas,
    'destino' => $destino,
    'certificado' => $certificado,
    'f_certificado' => $f_certificado,
    'f_enviado' => $f_enviado,
    'chk_firmado' => $chk_firmado,
    // para ver pdf
    'filename_pdf' => $filename_pdf_web,
];

$oView = new ViewNewTwig('frontend/certificados/controller');
$oView->renderizar('certificado_emitido_ver.html.twig', $a_campos);